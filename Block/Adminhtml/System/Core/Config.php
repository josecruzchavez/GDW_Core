<?php
namespace GDW\Core\Block\Adminhtml\System\Core;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\View\Helper\Js;

class Config extends Fieldset
{
    private const REMOTE_URL = 'https://php.gdw.mx/gdw-modulos/index.php';
    private const REQUEST_TIMEOUT = 5;

    /** @var Curl */
    private $curl;

    public function __construct(
        Context $context,
        AuthSession $authSession,
        Js $jsHelper,
        Curl $curl,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->curl = $curl;
    }

    public function render(AbstractElement $element)
    {
        try {
            $this->curl->setTimeout(self::REQUEST_TIMEOUT);
            $this->curl->get(self::REMOTE_URL);
            $status = $this->curl->getStatus();

            if ($status === 200) {
                return $this->curl->getBody();
            }
        } catch (\Exception $e) {
            // Servidor remoto no disponible, no interrumpir el admin
        }

        return '';
    }
}