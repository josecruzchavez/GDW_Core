<?php
declare(strict_types=1);

namespace GDW\Core\Helper;

use GDW\Core\Util\GdwLog;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    private const GDW_MODULE_CODE = 'gdwcore/';
    private const ACTION_PRODUCT_VIEW = 'catalog_product_view';
    private const ACTION_CATEGORY_VIEW = 'catalog_category_view';

    protected StoreManagerInterface $storeManager;
    protected Registry $registry;

    private Http $request;
    private ProductRepositoryInterface $productRepository;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Http $request,
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getModuleCode(): string
    {
        return self::GDW_MODULE_CODE;
    }

    public function getStoreData(): StoreInterface
    {
        return $this->storeManager->getStore();
    }

    public function getStoreId(): int
    {
        return (int)$this->getStoreData()->getId();
    }

    /** @return mixed */
    public function getConfigValue(string $field, ?int $storeId = null)
    {
        $idStore = ($storeId === null ? $this->getStoreId() : (int)$storeId);
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $idStore);
    }

    public function getCurrentProduct(): ?ProductInterface
    {
        $product = $this->registry->registry('current_product');
        if ($product instanceof ProductInterface) {
            return $product;
        }

        if ($this->request->getFullActionName() !== self::ACTION_PRODUCT_VIEW) {
            return null;
        }

        $productId = (int) ($this->request->getParam('id') ?? $this->request->getParam('product_id'));
        if ($productId <= 0) {
            return null;
        }

        try {
            return $this->productRepository->getById($productId, false, $this->getStoreId());
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    public function getCurrentCategory(): ?CategoryInterface
    {
        $category = $this->registry->registry('current_category');
        if ($category instanceof CategoryInterface) {
            return $category;
        }

        if ($this->request->getFullActionName() !== self::ACTION_CATEGORY_VIEW) {
            return null;
        }

        $categoryId = (int) ($this->request->getParam('id') ?? $this->request->getParam('category_id'));
        if ($categoryId <= 0) {
            return null;
        }

        try {
            return $this->categoryRepository->get($categoryId, $this->getStoreId());
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    public function log($message, ?string $file = null, string $level = 'info'): void
    {
        GdwLog::log($message, $file, $level);
    }
}