<?php
declare(strict_types=1);

namespace GDW\Core\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class AbstractButtonsData extends Action
{
    const ACTION_RESOURCE = 'GDW_Core::buttonsdata';

    public function __construct(
        Context $context,
        protected readonly Registry $coreRegistry,
        protected readonly PageFactory $resultPageFactory,
        protected readonly ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
    }
}
