<?php
declare(strict_types=1);

namespace GDW\Core\Block\Adminhtml\System\Core;

use GDW\Core\Helper\Internal;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\Js;

class ModuleToolsInfo extends Fieldset
{
    const GDW_MODULE_COMMAND = 'gdw:run:function';

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
        $desc = $this->getDescFull();
        $command = static::GDW_MODULE_COMMAND;
        return $this->helperInternal->getCommandInfoFull($command, $desc);
    }

    public function getDescFull(): string
    {
        $html = 
<<<HTML
    <p><strong>Comando:</strong> <code>gdw:run:function</code></p>
    <p>Ejecuta un metodo publico sin argumentos de una clase Magento.</p>
    <p><strong>Uso:</strong></p>
    <p><code>php bin/magento gdw:run:function --class="Vendor\Module\Model\Example" --function="execute" --area="frontend"</code></p>
    <p><strong>Opciones:</strong></p>
    <p><code>--class</code> (requerido), <code>--function</code> (requerido), <code>--area</code> (opcional: <code>frontend</code> o <code>adminhtml</code>).</p>
    <p><strong>Ejemplos:</strong></p>
    <p><code>php bin/magento gdw:run:function --class="GDW\Core\Test\Index" --function="anyFunction"</code></p>
    <p><code>php bin/magento gdw:run:function --class="Magento\Catalog\Cron\SynchronizeWebsiteAttributes" --function="execute" --area="adminhtml"</code></p>
    <p><strong>Nota:</strong> Solo ejecuta metodos publicos sin parametros obligatorios. Usar con precaucion en produccion.</p>
HTML;
        return $html;
    }
}