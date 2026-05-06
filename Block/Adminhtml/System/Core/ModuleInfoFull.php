<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Internal;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class ModuleInfoFull extends Fieldset
{
    const GDW_MODULE_CODE = 'GDW_Core';
    const GDW_MODULE_LINK = null;
    const GDW_MODULE_LINK_SECC = null;

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
        $desc = $this->getDescFull();
        $link = static::GDW_MODULE_LINK;
        $name = static::GDW_MODULE_CODE;
        $vers = static::GDW_MODULE_CODE;
        $secc = static::GDW_MODULE_LINK_SECC;
        return $this->helperInternal->getInfoFull($name, $vers, $desc, $link, $secc);
    }

    public function getDescFull(): string
    {
        $html = 
<<<HTML
    <p>Configuración base para módulos de magento 2 creados por GDW</p>
    <ul style="padding-left:25px;">
        <li>Crea un grupo general para el acceso ACL.</li>
        <li>Crea un grupo personalizado para las tareas cron.</li>
        <li>Crea una tab general en la configuración del administrador.</li>
        <li>Crea una tab para mostrar un listados de módulos creados por GDW instalados en su magento.</li>
    </ul>
HTML;
        return $html;
    }
}