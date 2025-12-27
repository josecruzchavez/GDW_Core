<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ModuleInfoFull extends Fieldset
{
    const GDW_MODULE_CODE = 'GDW_Core';
    const GDW_MODULE_LINK = null;
    const GDW_MODULE_LINK_SECC = null;
    
    protected $helperInternal;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
    \GDW\Core\Helper\Internal $helperInternal,
    array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperInternal = $helperInternal;
    }

    public function render(AbstractElement $element)
    {
        $desc = $this->getDescFull();
        $link = static::GDW_MODULE_LINK;
        $name = static::GDW_MODULE_CODE;
        $vers = static::GDW_MODULE_CODE;
        $secc = static::GDW_MODULE_LINK_SECC;
        return $this->helperInternal->getInfoFull($name, $vers, $desc, $link, $secc);
    }

    public function getDescFull()
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