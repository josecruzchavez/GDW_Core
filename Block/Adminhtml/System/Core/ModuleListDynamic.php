<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Internal;
use Throwable;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class ModuleListDynamic extends Fieldset
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        Context $context,
        AuthSession $authSession,
        Js $jsHelper,
        private readonly Internal $helperInternal,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    public function render(AbstractElement $element): string
    {
        $modules = $this->helperInternal->getInstalledGdwModules();
        $total = count($modules);

        $html  = '<div style="background:#f8f8f8; border:0; margin:0 !important; padding:8px;">';
        $html .= '<p><strong>Total de módulos GDW detectados:</strong> ' . $total . '</p>';

        if ($total === 0) {
            $html .= '<p>No se encontraron módulos con prefijo GDW_.</p>';
            $html .= '</div>';
            return $html;
        }

        $html .= '<table style="width:100%; border-collapse:collapse; background:#fff; table-layout:fixed;">';
        $html .= '<thead><tr>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:24%;">Módulo</th>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:12%;">Versión</th>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:40%;">Descripción corta</th>';
        $html .= '<th style="text-align:left; border-bottom:1px solid #ddd; padding:8px; width:24%;">Enlaces</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($modules as $module) {
            $code = htmlspecialchars($module['code'], ENT_QUOTES, 'UTF-8');
            $version = htmlspecialchars($module['version'], ENT_QUOTES, 'UTF-8');
            $meta = $this->resolveModuleMetadata($module['code']);

            $desc = htmlspecialchars($meta['desc'], ENT_QUOTES, 'UTF-8');
            $linksCell = $this->renderLinksCell(
                $meta['config_url'],
                $meta['repo_url'],
                $meta['docs_url']
            );

            $html .= '<tr>';
            $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top; word-break:break-word;">' . $code . '</td>';
            $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top;">' . $version . '</td>';
            $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top; word-break:break-word;">' . $desc . '</td>';
            $html .= '<td style="padding:8px; border-bottom:1px solid #eee; vertical-align:top; word-break:break-word;">' . $linksCell . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</div>';

        return $html;
    }

    private function renderLinksCell(?string $configUrl, ?string $repoUrl, ?string $docsUrl): string
    {
        $items = [];

        $items[] = $this->renderLinkIcon(
            $configUrl,
            'Configuracion',
            '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M19.14 12.94c.04-.31.06-.62.06-.94s-.02-.63-.06-.94l2.03-1.58a.5.5 0 0 0 .12-.63l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.1 7.1 0 0 0-1.63-.94l-.36-2.54a.5.5 0 0 0-.49-.42h-3.84a.5.5 0 0 0-.49.42l-.36 2.54a7.1 7.1 0 0 0-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.7 8.85a.5.5 0 0 0 .12.63l2.03 1.58c-.04.31-.06.62-.06.94s.02.63.06.94L2.82 14.52a.5.5 0 0 0-.12.63l1.92 3.32a.5.5 0 0 0 .6.22l2.39-.96c.5.39 1.05.71 1.63.94l.36 2.54a.5.5 0 0 0 .49.42h3.84a.5.5 0 0 0 .49-.42l.36-2.54c.58-.23 1.13-.55 1.63-.94l2.39.96a.5.5 0 0 0 .6-.22l1.92-3.32a.5.5 0 0 0-.12-.63l-2.03-1.58zM12 15.5A3.5 3.5 0 1 1 12 8.5a3.5 3.5 0 0 1 0 7z"/></svg>'
        );

        $items[] = $this->renderLinkIcon(
            $repoUrl,
            'Repositorio',
            '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 3a5 5 0 0 0 0 10h6a3 3 0 0 1 0 6H9a3 3 0 0 1-3-3H4a5 5 0 0 0 5 5h6a5 5 0 0 0 0-10H9a3 3 0 0 1 0-6h6a3 3 0 0 1 3 3h2a5 5 0 0 0-5-5H9z"/></svg>'
        );

        $items[] = $this->renderLinkIcon(
            $docsUrl,
            'Documentacion',
            '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm8 1.5V8h4.5L14 3.5zM8 12h8v1.5H8V12zm0 3h8v1.5H8V15zm0 3h6v1.5H8V18z"/></svg>'
        );

        return '<span style="display:inline-flex; align-items:center; gap:10px;">' . implode('', $items) . '</span>';
    }

    private function renderLinkIcon(?string $url, string $label, string $svg): string
    {
        $baseStyle = 'display:inline-flex; width:22px; height:22px; align-items:center; justify-content:center; border-radius:3px;';

        if (is_string($url) && trim($url) !== '') {
            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

            return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" title="' . $safeLabel . '" aria-label="' . $safeLabel . '" style="' . $baseStyle . ' color:#1979c3; border:1px solid #d6d6d6; background:#fff;">' . $svg . '</a>';
        }

        $safeLabel = htmlspecialchars($label . ' no disponible', ENT_QUOTES, 'UTF-8');
        return '<span title="' . $safeLabel . '" aria-label="' . $safeLabel . '" aria-disabled="true" style="' . $baseStyle . ' color:#9e9e9e; opacity:.45; border:1px solid #e6e6e6; background:#f7f7f7; cursor:not-allowed;">' . $svg . '</span>';
    }

    /**
    * @return array{desc:string, config_url:?string, repo_url:?string, docs_url:?string}
     */
    private function resolveModuleMetadata(string $moduleCode): array
    {
        $metadata = $this->readModuleMetadataClass($moduleCode);
        if ($metadata !== null) {
            return $metadata;
        }

        $shortInfoClass = $this->resolveClassByConvention($moduleCode, false);
        $fullInfoClass = $this->resolveClassByConvention($moduleCode, true);

        $desc = $this->invokeNoArgsStringMethod($shortInfoClass, 'getDesc') ?? 'Sin descripción corta.';
        $configUrl = null;

        if (class_exists($fullInfoClass)) {
            $link = $fullInfoClass::GDW_MODULE_LINK ?? null;
            $section = $fullInfoClass::GDW_MODULE_LINK_SECC ?? null;

            if (is_string($link) && $link !== '') {
                $configUrl = $this->getUrl($link);

                if (is_string($section) && $section !== '') {
                    $configUrl .= $section;
                }
            }
        }

        return [
            'desc' => trim($desc),
            'config_url' => $configUrl,
            'repo_url' => null,
            'docs_url' => null,
        ];
    }

    /** @return array{desc:string, config_url:?string, repo_url:?string, docs_url:?string}|null */
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

            return [
                'desc' => $desc,
                'config_url' => $configUrl,
                'repo_url' => $repoUrl,
                'docs_url' => $docsUrl,
            ];
        } catch (Throwable) {
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
        } catch (Throwable) {
            return null;
        }
    }
}