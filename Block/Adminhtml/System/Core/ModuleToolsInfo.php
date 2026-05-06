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
    <p><strong>Comando:</strong> <code>gdw:run:function</code></p>
    <p>Ejecuta un método público sin argumentos obligatorios de una clase Magento.</p>
    <p><strong>Uso:</strong></p>
    <p><code>php bin/magento gdw:run:function --class="Vendor\Module\Model\Example" --function="execute" --area="frontend"</code></p>
    <p><strong>Opciones:</strong></p>
    <p><code>--class</code> (requerido), <code>--function</code> (requerido), <code>--area</code> (opcional: <code>frontend</code> o <code>adminhtml</code>).</p>
    <p><strong>Ejemplos:</strong></p>
    <p><code>php bin/magento gdw:run:function --class="GDW\Core\Test\Index" --function="anyFunction"</code></p>
    <p><code>php bin/magento gdw:run:function --class="Magento\Catalog\Cron\SynchronizeWebsiteAttributes" --function="execute" --area="adminhtml"</code></p>
    <p><strong>Nota:</strong> Solo ejecuta métodos públicos sin parámetros obligatorios. Usar con precaución en producción.</p>
HTML;
        return $html;
    }
}