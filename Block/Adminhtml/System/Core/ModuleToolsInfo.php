<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ModuleToolsInfo extends Fieldset
{
    const GDW_MODULE_COMMAND = 'gdw:run:function';
    
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
        $command = static::GDW_MODULE_COMMAND;
        return $this->helperInternal->getCommandInfoFull($command, $desc);
    }

    public function getDescFull()
    {
        $html = 
<<<HTML
    <p>Run any anonymous function when pass Class and Function.</p>
    <p>examples:</p>
    <p>php bin/magento gdw:run:function --class="GDW\Core\Test\Index" --function="anyFunction"</p>
    <p>php bin/magento gdw:run:function --class="Magento\Catalog\Cron\SynchronizeWebsiteAttributes" --function="execute"</p>
HTML;
        return $html;
    }
}