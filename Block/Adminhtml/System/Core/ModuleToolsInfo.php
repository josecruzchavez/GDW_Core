<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class ModuleToolsInfo extends Fieldset
{
    const GDW_MODULE_COMMAND = 'gdw:run:function';
    
    protected $helperInternal;

    public function __construct(
        Context $context,
        AuthSession $authSession,
        Js $jsHelper,
        \GDW\Core\Helper\Internal $helperInternal,
    array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
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