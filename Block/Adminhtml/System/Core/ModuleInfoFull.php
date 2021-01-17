<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Data;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ModuleInfoFull extends Fieldset
{
    const MODULE_NAME = 'GDW_Core';
    const MODULE_CODE = 'GDW_Core';
    
    protected $helpData;

    public function __construct(Data $helpData) 
    {
        $this->helpData = $helpData;
    }

    public function render(AbstractElement $element)
    {
        $name = self::MODULE_NAME;
        $version = self::MODULE_CODE;
        $desc = $this->getDescFull();

        return $this->helpData->getInfoFull($name, $version, $desc);
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