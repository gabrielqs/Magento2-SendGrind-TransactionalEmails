<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent;

use \Magento\Backend\Model\View\Result\PageFactory;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Controller\ResultInterface;
use \Magento\Backend\Model\View\Result\Page;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent as AbstractSent;

class Index extends AbstractSent
{
    /**
     * Result Page Factory
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Index action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage);
        $this->_prependTitle($resultPage, __('Sent E-mails'));
        return $resultPage;
    }
}
