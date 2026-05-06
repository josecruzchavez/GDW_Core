<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Internal;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class ModuleInfo extends Fieldset
{
    const GDW_MODULE_CODE = 'GDW_Core';

    public function __construct(
        Context $context,
        AuthSession $authSession,
        Js $jsHelper,
        private readonly Internal $helperInternal,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    public function render(AbstractElement $element): string
    {
        $desc = $this->getDesc();
        $name = static::GDW_MODULE_CODE;
        $version = static::GDW_MODULE_CODE;
        return $this->helperInternal->getInfo($name, $version, $desc);
    }

    public function getDesc(): string
    {
        return 'Necesario para las funciones generales de GDW.';
    }
}