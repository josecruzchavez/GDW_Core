<?php

namespace GDW\Core\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;


abstract class AbstractButtonsData extends Action
{
    const ACTION_RESOURCE = 'GDW_Core::buttonsdata';

    protected $coreRegistry;
    protected $resultPageFactory;
    protected $resultForwardFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->coreRegistry          = $registry;
        $this->resultPageFactory     = $resultPageFactory;
        $this->resultForwardFactory  = $resultForwardFactory;
        parent::__construct($context);
    }
}
