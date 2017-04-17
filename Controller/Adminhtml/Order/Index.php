<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml\Order;

use \Magento\Framework\View\Result\LayoutFactory;
use \Magento\Framework\View\Result\Layout;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Magento\Backend\App\Action;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\RegistryConstants;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Gabrielqs_TransactionalEmails::orderEmailsList';

    /**
     * Core Registry
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Result Layout Factory
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Constructor
     * @param Context $context
     * @param Registry $registry
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $registry;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Execute
     * @return Layout
     */
    public function execute()
    {
        $orderId = $this->_request->getParam('order_id');
        $this->coreRegistry->register(RegistryConstants::CURRENT_ORDER_ID, $orderId);
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}