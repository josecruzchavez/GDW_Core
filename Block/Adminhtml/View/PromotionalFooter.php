<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\View;

use Magento\Framework\View\Element\Template;

class PromotionalFooter extends Template
{
    public function getBadge(): string
    {
        return 'GDW Admin Footer';
    }

    public function getTitle(): string
    {
        return 'gdw.mx: Soporte y desarrollo Magento';
    }

    public function getDescription(): string
    {
        return 'Ayudamos con optimización, mantenimiento y personalizaciones para tus módulos y procesos de administración.';
    }

    public function getPrimaryLabel(): string
    {
        return 'Contactar soporte';
    }

    public function getPrimaryUrl(): string
    {
        return $this->buildTrackedUrl('https://gdw.mx/contacto', 'primary', $this->getPrimaryLabel());
    }

    public function getSecondaryLabel(): string
    {
        return 'Ver servicios';
    }

    public function getSecondaryUrl(): string
    {
        return $this->buildTrackedUrl('https://gdw.mx', 'secondary', $this->getSecondaryLabel());
    }

    public function shouldShowSecondary(): bool
    {
        return $this->getSecondaryUrl() !== '';
    }

    private function buildTrackedUrl(string $baseUrl, string $content, string $actionLabel): string
    {
        $request = $this->getRequest();
        $domain = $request->getHttpHost();
        $sourcePath = (string)$request->getPathInfo();
        $sourcePath = preg_replace('#/key/[^/]+(?=/|$)#', '', $sourcePath) ?? $sourcePath;
        $sourceUrl = ($request->isSecure() ? 'https://' : 'http://') . $domain . $sourcePath;
        $separator = strpos($baseUrl, '?') === false ? '?' : '&';

        return $baseUrl . $separator . http_build_query([
            'utm_source' => $domain,
            'utm_medium' => 'gdw_admin_footer',
            'utm_campaign' => $request->getFullActionName(),
            'utm_content' => $content,
            'gdw_domain' => $domain,
            'gdw_page' => $sourceUrl,
            'gdw_action' => $actionLabel,
            'gdw_target' => $baseUrl,
        ]);
    }
}
