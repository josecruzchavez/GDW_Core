<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class GlobalAds extends Fieldset
{

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
        return $this->getDesc();
    }

    public function getDesc()
    {
        $html  = '<div style="background:#f1f1f1;padding: 20px 10px;">';
        $html .= '<div><strong>GDW | Gestion Digital Web | Soporte y desarrollo para Magento 2.</strong></div><hr/>';
        $html .= '<strong>Autor: </strong> <a href="https://www.linkedin.com/in/jose-cruz-chavez" target="_blank" rel="nofollow">José Cruz Chávez</a> |';
		$html .= '<strong>Donaciones: </strong> <a href="https://www.paypal.me/gestiondigitalweb" target="_blank" rel="nofollow">Mediante Paypal</a> |';
		$html .= '<strong>Sitio Web: </strong> <a href="https://gdw.mx" target="_blank">https://gdw.mx</a> |';
		$html .= '<strong>Dudas o requerimientos: </strong> <a href="mailto:jcruz@gdw.mx" target="_blank">jcruz@gdw.mx</a>';
        $html .= '</div>';
		return $html;
    }
}