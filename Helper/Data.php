<?php
namespace GDW\Core\Helper;

use GDW\Core\Util\GdwLog;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManager\FactoryInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const GDW_MODULE_CODE = 'gdwcore/';

    protected $storeManager;
    protected $registry;
    protected $factory;

    public function __construct(
        Context $context,
        Registry $registry,
        FactoryInterface $factory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->factory = $factory;
    }

    public function createObject(string $class, array $arguments = [])
    {
        return $this->factory->create($class, $arguments);
    }

    public function getModuleCode(): string
    {
        return static::GDW_MODULE_CODE;
    }

    public function getObject(string $class)
    {
        throw new \LogicException(
            'GDW_Core::getObject() está prohibido. Inyecta la dependencia por DI.'
        );
    }

    public function getStoreData()
    {
        return $this->storeManager->getStore();
    }

    public function getStoreId(): int
    {
        return (int)$this->getStoreData()->getId();
    }

    /** @return mixed */
    public function getConfigValue($field, $storeId = null)
    {
        $idStore = ($storeId === null ? $this->getStoreId() : (int)$storeId);
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $idStore);
    }

    /** @return mixed */
    public function getVal($group, $code, $storeId = null)
    {
        return $this->getConfigValue($this->getModuleCode() . $group . '/' . $code, $storeId);
    }

    /** @return mixed */
    public function getDirectVal($code, $storeId = null)
    {
        return $this->getConfigValue($this->getModuleCode() . $code, $storeId);
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    public function log($message, $file = null, $level = 'info')
    {
        GdwLog::log($message, $file, $level);
    }
}