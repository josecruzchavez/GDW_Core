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
        return $this->buildTrackedUrl('https://gdw.mx/contacto', 'primary');
    }

    public function getSecondaryLabel(): string
    {
        return 'Ver servicios';
    }

    public function getSecondaryUrl(): string
    {
        return $this->buildTrackedUrl('https://gdw.mx', 'secondary');
    }

    public function shouldShowSecondary(): bool
    {
        return $this->getSecondaryUrl() !== '';
    }

    private function buildTrackedUrl(string $baseUrl, string $content): string
    {
        $separator = strpos($baseUrl, '?') === false ? '?' : '&';

        return $baseUrl . $separator . http_build_query([
            'utm_source' => 'magento_admin',
            'utm_medium' => 'gdw_admin_footer',
            'utm_campaign' => 'gdw_modules',
            'utm_content' => $content,
        ]);
    }
}
