<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Data;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ModuleInfo extends Fieldset
{
    const MODULE_NAME = 'GDW_Core';
    const MODULE_CODE = 'GDW_Core';
    
    protected $helpData;

    public function __construct(Data $helpData) {
        $this->helpData = $helpData;
    }

    public function render(AbstractElement $element)
    {
        $name = self::MODULE_NAME;
        $version = self::MODULE_CODE;
        $desc = $this->getDesc();

        return $this->helpData->getInfo($name,$version,$desc);
    }

    public function getDesc()
    {
        return 'Necesario para las funciones generales de GDW.';
    }
}