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

    /** @var Registry */
    protected $coreRegistry;

    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var ForwardFactory */
    protected $resultForwardFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
}
