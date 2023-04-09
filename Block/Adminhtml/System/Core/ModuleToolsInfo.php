<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Data;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ModuleToolsInfo extends Fieldset
{
    const GDW_MODULE_COMMAND = 'gdw:run:function';
    
    protected $helpData;

    public function __construct(Data $helpData) 
    {
        $this->helpData = $helpData;
    }

    public function render(AbstractElement $element)
    {
        $desc = $this->getDescFull();
        $command = static::GDW_MODULE_COMMAND;
        return $this->helpData->getCommandInfoFull($command, $desc);
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