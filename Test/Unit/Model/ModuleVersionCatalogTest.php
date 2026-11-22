<?php
declare(strict_types=1);

namespace GDW\Core\Test\Unit\Model;

use GDW\Core\Block\Adminhtml\System\Core\ModuleListDynamic;
use GDW\Core\Model\ModuleVersionCatalog;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class ModuleVersionCatalogTest extends TestCase
{
    private CacheInterface $cache;
    private Curl $httpClient;
    private Json $serializer;
    private LoggerInterface $logger;
    private ModuleVersionCatalog $subject;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->httpClient = $this->getMockBuilder(Curl::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setTimeout', 'addHeader', 'get', 'getStatus', 'getBody'])
            ->getMock();
        $this->serializer = new Json();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->subject = new ModuleVersionCatalog(
            $this->cache,
            $this->httpClient,
            $this->serializer,
            $this->logger
        );
    }

    public function testHttp404ReturnsFalseAndKeepsTheLastCatalog(): void
    {
        $this->httpClient->method('getStatus')->willReturn(404);

        $this->assertFailurePreservesCatalog('unexpected HTTP status');
    }

    public function testRequestExceptionReturnsFalseAndKeepsTheLastCatalog(): void
    {
        $this->httpClient->method('get')
            ->willThrowException(new \Exception('Network unavailable.'));

        $this->assertFailurePreservesCatalog('Unable to request');
    }

    public function testInvalidJsonReturnsFalseAndKeepsTheLastCatalog(): void
    {
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn('{invalid');

        $this->assertFailurePreservesCatalog('response is invalid');
    }

    public function testInvalidSchemaReturnsFalseAndKeepsTheLastCatalog(): void
    {
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($this->serializer->serialize(['schema' => 2]));

        $this->assertFailurePreservesCatalog('response is invalid');
    }

    public function testValidCatalogIsCached(): void
    {
        $catalog = $this->validCatalog();
        $serializedCatalog = $this->serializer->serialize($catalog);
        $cachedValue = null;

        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($serializedCatalog);
        $this->cache->expects($this->once())
            ->method('save')
            ->with(
                $serializedCatalog,
                $this->isType('string'),
                ['GDW_CORE_MODULE_VERSIONS']
            )
            ->willReturnCallback(static function (string $data) use (&$cachedValue): bool {
                $cachedValue = $data;
                return true;
            });
        $this->cache->expects($this->once())
            ->method('load')
            ->willReturnCallback(static function () use (&$cachedValue): ?string {
                return $cachedValue;
            });
        $this->logger->expects($this->never())->method('warning');

        $this->assertTrue($this->subject->refresh());
        $this->assertSame($catalog, $this->subject->getCatalog());
    }

    public function testCacheWriteFailureReturnsFalse(): void
    {
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($this->serializer->serialize($this->validCatalog()));
        $this->cache->expects($this->once())->method('save')->willReturn(false);
        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Unable to cache'));

        $this->assertFalse($this->subject->refresh());
    }

    public function testInvalidCachedJsonIsLoggedRemovedAndTreatedAsUnavailable(): void
    {
        $this->cache->expects($this->once())->method('load')->willReturn('{invalid');
        $this->cache->expects($this->once())
            ->method('remove')
            ->with('gdw_core_module_versions_v1')
            ->willReturn(true);
        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('cached GDW module version catalog is invalid'), $this->anything());

        $this->assertNull($this->subject->getCatalog());
    }

    public function testIncompatibleCachedSchemaIsLoggedRemovedAndTreatedAsUnavailable(): void
    {
        $this->cache->expects($this->once())
            ->method('load')
            ->willReturn($this->serializer->serialize(['schema' => 2]));
        $this->cache->expects($this->once())
            ->method('remove')
            ->with('gdw_core_module_versions_v1')
            ->willReturn(true);
        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('cached GDW module version catalog is invalid'), $this->anything());

        $this->assertNull($this->subject->getCatalog());
    }

    public function testAdminShowsFallbackWhenNoPublicVersionIsAvailable(): void
    {
        $html = $this->renderVersionCell(null);

        $this->assertStringContainsString('Sin información pública<br>para esta versión.', $html);
    }

    public function testAdminUsesTheUnifiedPublicationDateFormat(): void
    {
        $html = $this->renderVersionCell([
            'line' => '4.4.x',
            'version' => '4.4.5',
            'released_at' => '2026-05-29T17:30:24Z',
            'changelog_url' => 'https://docs.gdw.mx/modulos/gdw_core/changelog/',
            'update_available' => false,
        ]);

        $this->assertStringContainsString('Publicada 2026-05-29', $html);
        $this->assertStringNotContainsString('hace ', $html);
    }

    public function testAdminDisplaysPaidLabelOnlyForPaidModules(): void
    {
        $reflection = new ReflectionClass(ModuleListDynamic::class);
        $block = $reflection->newInstanceWithoutConstructor();
        $renderPaidBadge = $reflection->getMethod('renderPaidBadge');

        $badge = $renderPaidBadge->invoke($block, 'paid');

        $this->assertStringContainsString('>$</span>', $badge);
        $this->assertStringContainsString('aria-label="Módulo de pago"', $badge);
        $this->assertStringNotContainsString('>Módulo de pago</span>', $badge);
        $this->assertSame('', $renderPaidBadge->invoke($block, null));
    }

    private function assertFailurePreservesCatalog(string $warningMessage): void
    {
        $catalog = $this->validCatalog();
        $this->cache->expects($this->once())
            ->method('load')
            ->willReturn($this->serializer->serialize($catalog));
        $this->cache->expects($this->never())->method('save');
        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains($warningMessage), $this->anything());

        $this->assertFalse($this->subject->refresh());
        $this->assertSame($catalog, $this->subject->getCatalog());
    }

    /**
     * @return array{
     *     schema:int,
     *     updated_at:string,
     *     packages:array<string, array{
     *         latest_by_line:array<string, array{version:string, released_at:string}>,
     *         changelog_url:string
     *     }>
     * }
     */
    private function validCatalog(): array
    {
        return [
            'schema' => 1,
            'updated_at' => '2026-11-22',
            'packages' => [
                'gdw/core' => [
                    'latest_by_line' => [
                        '4.4.x' => [
                            'version' => '4.4.5',
                            'released_at' => '2026-05-29T17:30:24Z',
                        ],
                    ],
                    'changelog_url' => 'https://docs.gdw.mx/modulos/gdw_core/changelog/',
                ],
            ],
        ];
    }

    /**
     * @param array{line:string, version:string, released_at:string, changelog_url:string, update_available:bool}|null $versionInfo
     */
    private function renderVersionCell(?array $versionInfo): string
    {
        $reflection = new ReflectionClass(ModuleListDynamic::class);
        $block = $reflection->newInstanceWithoutConstructor();

        return $reflection->getMethod('renderVersionCell')->invoke($block, $versionInfo);
    }
}
