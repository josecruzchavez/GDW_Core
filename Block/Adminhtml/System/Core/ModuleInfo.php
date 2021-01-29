<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Data;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ModuleInfo extends Fieldset
{
    const GDW_MODULE_CODE = 'GDW_Core';
    
    protected $helpData;

    public function __construct(Data $helpData) {
        $this->helpData = $helpData;
    }

    public function render(AbstractElement $element)
    {
        $desc = $this->getDesc();
        $name = static::GDW_MODULE_CODE;
        $version = static::GDW_MODULE_CODE;
        return $this->helpData->getInfo($name,$version,$desc);
    }

    public function getDesc()
    {
        return 'Necesario para las funciones generales de GDW.';
    }
}