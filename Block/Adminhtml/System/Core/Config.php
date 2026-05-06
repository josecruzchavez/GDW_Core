<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Config extends Fieldset
{
    private const REMOTE_URL = 'https://php.gdw.mx/gdw-modulos/index.php';

    public function render(AbstractElement $element): string
    {
        return sprintf(
            <<<HTML
<div style="background:#f8f8f8;border:1px solid #d6d6d6;padding:16px;border-radius:4px;">
    <p style="margin:0 0 8px 0;"><strong>GDW Core</strong> ya no carga contenido remoto durante el render del panel para evitar dependencias externas y ralentizaciones del admin.</p>
    <p style="margin:0;">Si quieres revisar recursos o novedades, ábrelos manualmente aquí: <a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>
</div>
HTML,
            self::REMOTE_URL,
            self::REMOTE_URL
        );
    }
}