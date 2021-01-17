<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

class Config extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<iframe id="gdw_website" width="100%" height="1000" frameBorder="0" src="https://gestiondigitalweb.com"></iframe>';
    }
}