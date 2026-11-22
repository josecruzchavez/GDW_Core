<?php
declare(strict_types=1);

namespace GDW\Core\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class ModuleVersionCatalog
{
    public const SOURCE_URL = 'https://gdw.mx/module-versions.json';

    private const CACHE_ID = 'gdw_core_module_versions_v1';
    private const CACHE_TAG = 'GDW_CORE_MODULE_VERSIONS';
    private const REQUEST_TIMEOUT = 15;

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly Curl $httpClient,
        private readonly Json $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCatalog(): ?array
    {
        $cachedCatalog = $this->cache->load(self::CACHE_ID);
        if ($cachedCatalog === false) {
            return null;
        }

        try {
            if (!is_string($cachedCatalog)) {
                throw new UnexpectedValueException('The cached GDW module version catalog is not a string.');
            }

            return $this->validateCatalog($this->serializer->unserialize($cachedCatalog));
        } catch (\InvalidArgumentException|UnexpectedValueException $exception) {
            $this->logger->warning(
                'The cached GDW module version catalog is invalid.',
                ['exception' => $exception]
            );

            try {
                if (!$this->cache->remove(self::CACHE_ID)) {
                    $this->logger->warning('Unable to remove the invalid GDW module version catalog from cache.');
                }
            } catch (\Exception $cacheException) {
                $this->logger->warning(
                    'Unable to remove the invalid GDW module version catalog from cache.',
                    ['exception' => $cacheException]
                );
            }

            return null;
        }
    }

    public function refresh(): bool
    {
        try {
            $this->httpClient->setTimeout(self::REQUEST_TIMEOUT);
            $this->httpClient->addHeader('Accept', 'application/json');
            $this->httpClient->get(self::SOURCE_URL);
            $status = $this->httpClient->getStatus();
            $body = $this->httpClient->getBody();
        } catch (\Exception $exception) {
            $this->logger->warning(
                'Unable to request the GDW module version catalog.',
                ['url' => self::SOURCE_URL, 'exception' => $exception]
            );
            return false;
        }

        if ($status !== 200) {
            $this->logger->warning(
                'The GDW module version catalog request returned an unexpected HTTP status.',
                ['url' => self::SOURCE_URL, 'status' => $status]
            );
            return false;
        }

        try {
            $catalog = $this->validateCatalog($this->serializer->unserialize($body));
        } catch (\InvalidArgumentException|UnexpectedValueException $exception) {
            $this->logger->warning(
                'The GDW module version catalog response is invalid.',
                ['url' => self::SOURCE_URL, 'exception' => $exception]
            );
            return false;
        }

        try {
            $saved = $this->cache->save(
                $this->serializer->serialize($catalog),
                self::CACHE_ID,
                [self::CACHE_TAG]
            );
        } catch (\Exception $exception) {
            $this->logger->warning(
                'Unable to cache the GDW module version catalog.',
                ['exception' => $exception]
            );
            return false;
        }

        if (!$saved) {
            $this->logger->warning('Unable to cache the GDW module version catalog.');
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $catalog
     * @return array{line:string, version:string, released_at:string, changelog_url:string, update_available:bool}|null
     */
    public function getModuleRelease(array $catalog, string $moduleCode, string $installedVersion): ?array
    {
        if (strpos($moduleCode, 'GDW_') !== 0
            || preg_match('/^(\d+)\.(\d+)\.\d+(?:[-+][0-9A-Za-z.-]+)?$/', trim($installedVersion), $versionParts) !== 1
        ) {
            return null;
        }

        $packageName = 'gdw/' . strtolower(substr($moduleCode, 4));
        $package = $catalog['packages'][$packageName] ?? null;
        if (!is_array($package) || !is_array($package['latest_by_line'] ?? null)) {
            return null;
        }

        $line = $versionParts[1] . '.' . $versionParts[2] . '.x';
        if (!isset($package['latest_by_line'][$line])) {
            $line = $versionParts[1] . '.x';
        }

        $release = $package['latest_by_line'][$line] ?? null;
        if (!is_array($release)) {
            return null;
        }

        return [
            'line' => $line,
            'version' => $release['version'],
            'released_at' => $release['released_at'],
            'changelog_url' => $package['changelog_url'],
            'update_available' => version_compare($installedVersion, $release['version'], '<'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCatalog(mixed $catalog): array
    {
        if (!is_array($catalog)
            || ($catalog['schema'] ?? null) !== 1
            || !is_string($catalog['updated_at'] ?? null)
            || !is_array($catalog['packages'] ?? null)
            || $catalog['packages'] === []
        ) {
            throw new UnexpectedValueException('The GDW module version catalog has an invalid schema.');
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $catalog['updated_at'], $dateParts) !== 1
            || !checkdate((int)$dateParts[2], (int)$dateParts[3], (int)$dateParts[1])
        ) {
            throw new UnexpectedValueException('The GDW module version catalog has an invalid updated_at date.');
        }

        foreach ($catalog['packages'] as $packageName => $package) {
            if (!is_string($packageName)
                || preg_match('/^gdw\/[a-z0-9-]+$/', $packageName) !== 1
                || !is_array($package)
                || !is_array($package['latest_by_line'] ?? null)
                || !is_string($package['changelog_url'] ?? null)
            ) {
                throw new UnexpectedValueException('The GDW module version catalog contains an invalid package entry.');
            }

            $changelogUrl = parse_url($package['changelog_url']);
            if (!is_array($changelogUrl)
                || strtolower((string)($changelogUrl['scheme'] ?? '')) !== 'https'
                || strtolower((string)($changelogUrl['host'] ?? '')) !== 'docs.gdw.mx'
            ) {
                throw new UnexpectedValueException(sprintf(
                    'The GDW module version catalog contains an invalid changelog URL for %s.',
                    $packageName
                ));
            }

            foreach ($package['latest_by_line'] as $line => $release) {
                if (!is_string($line)
                    || preg_match('/^\d+(?:\.\d+)?\.x$/', $line) !== 1
                    || !is_array($release)
                    || !is_string($release['version'] ?? null)
                    || preg_match('/^\d+\.\d+\.\d+$/', $release['version']) !== 1
                    || !is_string($release['released_at'] ?? null)
                    || preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $release['released_at']) !== 1
                    || strtotime($release['released_at']) === false
                ) {
                    throw new UnexpectedValueException(sprintf(
                        'The GDW module version catalog contains an invalid release for %s.',
                        $packageName
                    ));
                }
            }
        }

        return $catalog;
    }
}
