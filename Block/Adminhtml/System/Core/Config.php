<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Config extends Fieldset
{
    public function render(AbstractElement $element)
    {
        return '<iframe id="gdw_website" width="100%" height="1000" frameBorder="0" src="https://gestiondigitalweb.com/gdw-modulos/index.php"></iframe>';
    }
}