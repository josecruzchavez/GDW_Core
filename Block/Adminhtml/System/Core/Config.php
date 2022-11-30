<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Config extends Fieldset
{
    public function render(AbstractElement $element)
    {

        $getData = file_get_contents('https://php.gdw.mx/gdw-modulos/index.php');
        return $getData ?? '';
    }
}