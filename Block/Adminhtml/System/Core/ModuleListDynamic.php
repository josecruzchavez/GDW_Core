<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Internal;
use GDW\Core\Model\ModuleVersionCatalog;
use Throwable;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class ModuleListDynamic extends Fieldset
{
    /** @var Internal */
    private $helperInternal;
    /** @var ComponentRegistrarInterface */
    private $componentRegistrar;
    /** @var ModuleVersionCatalog */
    private $moduleVersionCatalog;

    public function __construct(
        Context $context,
        AuthSession $authSession,
        Js $jsHelper,
        Internal $helperInternal,
        ComponentRegistrarInterface $componentRegistrar,
        ModuleVersionCatalog $moduleVersionCatalog,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->helperInternal = $helperInternal;
        $this->componentRegistrar = $componentRegistrar;
        $this->moduleVersionCatalog = $moduleVersionCatalog;
    }

    public function render(AbstractElement $element): string
    {
        $modules = $this->helperInternal->getInstalledGdwModules();
        $total = count($modules);
        $catalog = $this->moduleVersionCatalog->getCatalog();
        $registeredModuleCodes = array_keys(
            $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE)
        );

        $html  = '<div style="background:#f8f8f8; border:0; margin:0 !important; padding:8px;">';
        $html .= '<p><strong>Total de módulos GDW detectados:</strong> ' . $total . '</p>';
        if ($catalog === null) {
            $html .= '<p><small>El catálogo de versiones aún no está disponible; se consulta diariamente.</small></p>';
        } else {
            $catalogUpdatedAt = htmlspecialchars($catalog['updated_at'], ENT_QUOTES, 'UTF-8');
            $html .= '<p><small>Última actualización del catálogo de versiones: ' . $catalogUpdatedAt . '</small></p>';
        }

        if ($total === 0) {
            $html .= '<p>No se encontraron módulos con prefijo GDW_.</p>';
            $html .= '</div>';
            return $html;
        }

        $html .= '<table style="width:100%; border-collapse:collapse; background:#fff; table-layout:fixed;">';
        $html .= '<thead><tr>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:20%;">Módulo</th>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:10%;">Versión instalada</th>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:16%;">Última versión<br>publicada</th>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:38%;">Descripción corta</th>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:16%;">Enlaces</th>';
        $html .= '</tr></thead><tbody>';

        $moduleMetadata = [];
        $proCategories = [];
        $proModuleCodes = [];
        foreach ($modules as $module) {
            $meta = $this->resolveModuleMetadata($module['code']);
            $moduleMetadata[$module['code']] = $meta;
            if ($meta['pro_module'] !== null) {
                $proCategories[$meta['pro_module']] = trim($meta['category']);
                $proModuleCodes[$meta['pro_module']] = true;
            }
        }

        $groupedModules = [];
        foreach ($modules as $module) {
            $meta = $moduleMetadata[$module['code']];
            $category = $proCategories[$module['code']] ?? trim($meta['category']);
            $groupedModules[$category][] = [
                'module' => $module,
                'meta' => $meta,
            ];
        }

        uksort($groupedModules, static function ($left, $right): int {
            $left = (string)$left;
            $right = (string)$right;

            if ($left === '') {
                return $right === '' ? 0 : -1;
            }
            if ($right === '') {
                return 1;
            }

            return strcmp($left, $right);
        });

        foreach ($groupedModules as $category => $categoryModules) {
            $categoryModules = $this->orderFreemiumPairs($categoryModules);
            $categoryLabel = $category === '' ? 'Generales' : $category;
            $html .= '<tr><th colspan="5" style="text-align:left; background:#f3f3f3; padding:8px; border-bottom:1px solid #ddd;">'
                . htmlspecialchars($categoryLabel, ENT_QUOTES, 'UTF-8')
                . '</th></tr>';

            foreach ($categoryModules as $item) {
                $module = $item['module'];
                $meta = $item['meta'];
                $code = htmlspecialchars($module['code'], ENT_QUOTES, 'UTF-8');
                $version = htmlspecialchars($module['version'], ENT_QUOTES, 'UTF-8');
                $desc = htmlspecialchars($meta['desc'], ENT_QUOTES, 'UTF-8');
                $versionInfo = $catalog === null
                    ? null
                    : $this->moduleVersionCatalog->getModuleRelease(
                        $catalog,
                        $module['code'],
                        $module['version']
                    );
                $versionCell = $this->renderVersionCell($versionInfo);
                $pricingModel = $meta['pricing_model'] === 'paid' || isset($proModuleCodes[$module['code']])
                    ? 'paid'
                    : null;
                $pricingBadge = $this->renderPaidBadge($pricingModel);
                $proOffer = $this->renderProOffer(
                    $meta['pro_module'],
                    $meta['pro_url'],
                    $registeredModuleCodes
                );
                $linksCell = $this->renderLinksCell(
                    $meta['config_url'],
                    $meta['repo_url'],
                    $meta['docs_url'],
                    $versionInfo['changelog_url'] ?? null
                );

                $html .= '<tr>';
                $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top; word-break:break-word;">' . $pricingBadge . $code . '</td>';
                $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top;">' . $version . '</td>';
                $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top;">' . $versionCell . '</td>';
                $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top; word-break:break-word;">' . $desc . $proOffer . '</td>';
                $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top; word-break:break-word;">' . $linksCell . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param array{line:string, version:string, released_at:string, changelog_url:string, update_available:bool}|null $versionInfo
     */
    private function renderVersionCell(?array $versionInfo): string
    {
        if ($versionInfo === null) {
            return '<span style="color:#777; font-size:11px; line-height:1.3;">Sin información pública<br>para esta versión.</span>';
        }

        $version = htmlspecialchars($versionInfo['version'], ENT_QUOTES, 'UTF-8');
        $date = new \DateTimeImmutable($versionInfo['released_at']);
        $dateLabel = $date->format('Y-m-d');
        $html = '<strong>' . $version . '</strong><br><small>Publicada '
            . htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8')
            . '</small>';

        if ($versionInfo['update_available']) {
            $html .= '<div style="margin-top:6px; color:#9a6700;">'
                . 'Actualización disponible. Instálala con Composer; los módulos Pro requieren acceso vigente en Packeton.'
                . '</div>';
        }

        return $html;
    }

    private function orderFreemiumPairs(array $modules): array
    {
        $modulesByCode = [];
        $pairedProCodes = [];
        foreach ($modules as $item) {
            $code = $item['module']['code'];
            $modulesByCode[$code] = $item;
            if ($item['meta']['pro_module'] !== null) {
                $pairedProCodes[$item['meta']['pro_module']] = true;
            }
        }

        $ordered = [];
        $added = [];
        foreach ($modules as $item) {
            $code = $item['module']['code'];
            if (isset($added[$code]) || isset($pairedProCodes[$code])) {
                continue;
            }

            $ordered[] = $item;
            $added[$code] = true;

            $proModule = $item['meta']['pro_module'];
            if ($proModule !== null && isset($modulesByCode[$proModule]) && !isset($added[$proModule])) {
                $ordered[] = $modulesByCode[$proModule];
                $added[$proModule] = true;
            }
        }

        foreach ($modules as $item) {
            $code = $item['module']['code'];
            if (!isset($added[$code])) {
                $ordered[] = $item;
                $added[$code] = true;
            }
        }

        return $ordered;
    }

    /**
     * @param array<int, string> $registeredModuleCodes
     */
    private function renderProOffer(?string $proModule, ?string $proUrl, array $registeredModuleCodes): string
    {
        if ($proModule === null || $proModule === '' || in_array($proModule, $registeredModuleCodes, true)
            || $proUrl === null || trim($proUrl) === ''
        ) {
            return '';
        }

        $safeUrl = htmlspecialchars($proUrl, ENT_QUOTES, 'UTF-8');

        return '<div style="margin-top:8px; padding:8px; border-left:3px solid #1979c3; background:#f4f9fc;">'
            . '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer">Conoce el complemento Pro</a>'
            . '</div>';
    }

    private function renderPaidBadge(?string $pricingModel): string
    {
        if ($pricingModel !== 'paid') {
            return '';
        }

        return '<span role="img" aria-label="Módulo de pago" title="Módulo de pago" style="display:inline-block; margin-right:6px; padding:3px 7px; border-radius:3px; background:#fff3cd; color:#856404; font-size:12px; font-weight:600; vertical-align:middle;">$</span>';
    }

    private function renderLinksCell(
        ?string $configUrl,
        ?string $repoUrl,
        ?string $docsUrl,
        ?string $changelogUrl
    ): string
    {
        $items = [];

        $items[] = $this->renderLinkIcon(
            $configUrl,
            'Configuracion',
            '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/><circle cx="9" cy="6" r="2" fill="#fff"/><circle cx="15" cy="12" r="2" fill="#fff"/><circle cx="8" cy="18" r="2" fill="#fff"/></svg>'
        );

        $items[] = $this->renderLinkIcon(
            $repoUrl,
            'Repositorio',
            '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="6" cy="5" r="2.5"/><circle cx="18" cy="19" r="2.5"/><circle cx="18" cy="5" r="2.5"/><path d="M6 7.5v9a2.5 2.5 0 0 0 2.5 2.5H15.5M6 12h7a5 5 0 0 0 5-5V7.5"/></svg>'
        );

        $items[] = $this->renderLinkIcon(
            $docsUrl,
            'Documentacion',
            '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 7c-2-1.5-4.5-2-8-2v14c3.5 0 6 .5 8 2 2-1.5 4.5-2 8-2V5c-3.5 0-6 .5-8 2Z"/><path d="M12 7v14M7 9h2M7 12h2m6-3h2m-2 3h2"/></svg>'
        );

        $items[] = $this->renderLinkIcon(
            $changelogUrl,
            'Changelog',
            '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h14v16H5z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>'
        );

        return '<span style="display:inline-flex; align-items:center; gap:10px;">' . implode('', $items) . '</span>';
    }

    private function renderLinkIcon(?string $url, string $label, string $svg): string
    {
        $baseStyle = 'display:inline-flex; width:30px; height:30px; align-items:center; justify-content:center; border-radius:4px; transition:background-color .15s, border-color .15s;';

        if (is_string($url) && trim($url) !== '') {
            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

            return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" title="' . $safeLabel . '" aria-label="' . $safeLabel . '" style="' . $baseStyle . ' color:#1979c3; border:1px solid #d6d6d6; background:#fff;">' . $svg . '</a>';
        }

        $safeLabel = htmlspecialchars($label . ' no disponible', ENT_QUOTES, 'UTF-8');
        return '<span title="' . $safeLabel . '" aria-label="' . $safeLabel . '" aria-disabled="true" style="' . $baseStyle . ' color:#9e9e9e; opacity:.45; border:1px solid #e6e6e6; background:#f7f7f7; cursor:not-allowed;">' . $svg . '</span>';
    }

    /**
    * @return array{desc:string, category:string, config_url:?string, repo_url:?string, docs_url:?string, pro_module:?string, pro_url:?string, pricing_model:?string}
     */
    private function resolveModuleMetadata(string $moduleCode): array
    {
        $metadata = $this->readModuleMetadataClass($moduleCode);
        if ($metadata !== null) {
            return $metadata;
        }

        $shortInfoClass = $this->resolveClassByConvention($moduleCode, false);
        $fullInfoClass = $this->resolveClassByConvention($moduleCode, true);

        $desc = $this->invokeNoArgsStringMethod($shortInfoClass, 'getDesc');
        if ($desc === null) {
            $desc = 'Sin descripción corta.';
        }

        $configUrl = null;

        if (class_exists($fullInfoClass)) {
            $linkConst = $fullInfoClass . '::GDW_MODULE_LINK';
            $sectionConst = $fullInfoClass . '::GDW_MODULE_LINK_SECC';

            $link = defined($linkConst) ? (string) constant($linkConst) : null;
            $section = defined($sectionConst) ? (string) constant($sectionConst) : null;

            if ($link !== null && $link !== '') {
                $configUrl = $this->getUrl($link);

                if ($section !== null && $section !== '') {
                    $configUrl .= $section;
                }
            }
        }

        return [
            'desc' => trim($desc),
            'category' => '',
            'config_url' => $configUrl,
            'repo_url' => null,
            'docs_url' => null,
            'pro_module' => null,
            'pro_url' => null,
            'pricing_model' => null,
        ];
    }

    /** @return array{desc:string, category:string, config_url:?string, repo_url:?string, docs_url:?string, pro_module:?string, pro_url:?string, pricing_model:?string}|null */
    private function readModuleMetadataClass(string $moduleCode): ?array
    {
        try {
            $metaClass = $this->resolveMetaClassByConvention($moduleCode);
            if (!class_exists($metaClass)) {
                return null;
            }

            $meta = method_exists($metaClass, 'getMeta') ? $metaClass::getMeta() : null;
            if (!is_array($meta)) {
                return null;
            }

            $desc = isset($meta['desc']) && is_string($meta['desc'])
                ? trim($meta['desc'])
                : 'Sin descripción corta.';
            $category = isset($meta['category']) && is_string($meta['category'])
                ? trim($meta['category'])
                : '';

            $configUrl = null;
            if (isset($meta['config_path']) && is_string($meta['config_path']) && $meta['config_path'] !== '') {
                $configUrl = $this->getUrl($meta['config_path']);

                if (isset($meta['config_anchor']) && is_string($meta['config_anchor']) && $meta['config_anchor'] !== '') {
                    $configUrl .= $meta['config_anchor'];
                }
            }

            $repoUrl = isset($meta['repo_url']) && is_string($meta['repo_url']) && $meta['repo_url'] !== ''
                ? $meta['repo_url']
                : null;

            $docsUrl = isset($meta['docs_url']) && is_string($meta['docs_url']) && $meta['docs_url'] !== ''
                ? $meta['docs_url']
                : null;
            $proModule = isset($meta['pro_module']) && is_string($meta['pro_module']) && $meta['pro_module'] !== ''
                ? $meta['pro_module']
                : null;
            $proUrl = isset($meta['pro_url']) && is_string($meta['pro_url']) && $meta['pro_url'] !== ''
                ? $meta['pro_url']
                : null;
            $pricingModel = ($meta['pricing_model'] ?? null) === 'paid' ? 'paid' : null;

            return [
                'desc' => $desc,
                'category' => $category,
                'config_url' => $configUrl,
                'repo_url' => $repoUrl,
                'docs_url' => $docsUrl,
                'pro_module' => $proModule,
                'pro_url' => $proUrl,
                'pricing_model' => $pricingModel,
            ];
        } catch (Throwable $e) {
            return null;
        }
    }

    private function resolveMetaClassByConvention(string $moduleCode): string
    {
        if ($moduleCode === 'GDW_Core') {
            return 'GDW\\Core\\Helper\\GdwModuleMeta';
        }

        $namespace = str_replace('_', '\\', $moduleCode);
        return $namespace . '\\Helper\\GdwModuleMeta';
    }

    private function resolveClassByConvention(string $moduleCode, bool $isFull): string
    {
        $suffix = $isFull ? 'ModuleInfoFull' : 'ModuleInfo';

        if ($moduleCode === 'GDW_Core') {
            return 'GDW\\Core\\Block\\Adminhtml\\System\\Core\\' . $suffix;
        }

        $namespace = str_replace('_', '\\', $moduleCode);
        return $namespace . '\\Block\\Adminhtml\\System\\' . $suffix;
    }

    private function invokeNoArgsStringMethod(?string $className, string $method): ?string
    {
        if ($className === null || !class_exists($className) || !method_exists($className, $method)) {
            return null;
        }

        try {
            $refClass = new \ReflectionClass($className);
            $refMethod = $refClass->getMethod($method);

            if (!$refMethod->isPublic() || $refMethod->getNumberOfRequiredParameters() > 0) {
                return null;
            }

            $instance = $refClass->newInstanceWithoutConstructor();
            $result = $refMethod->invoke($instance);

            return is_string($result) ? $result : null;
        } catch (Throwable $e) {
            return null;
        }
    }
}