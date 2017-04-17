<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent;

use \Magento\Backend\Model\View\Result\PageFactory;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Controller\ResultInterface;
use \Magento\Backend\Model\View\Result\Page;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\Sent as AbstractSent;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\RegistryConstants;
use \Gabrielqs\TransactionalEmails\Model\EmailRepository;

class View extends AbstractSent
{
    /**
     * Email Repository
     * @var EmailRepository
     */
    protected $emailRepository;

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
     * @param EmailRepository $emailRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        EmailRepository $emailRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->emailRepository = $emailRepository;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Index action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $emailId = $this->_getEmailIdFromRequest();
        try {
            $this->emailRepository->getById($emailId);
            $this->_coreRegistry->register(RegistryConstants::CURRENT_EMAIL_ID, $emailId);
            /** @var Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $this->initPage($resultPage);
            $this->_prependTitle($resultPage, __('E-mail'));
            return $resultPage;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('There was a problem while loading the e-mail.')
            );
            return $this->_getResultRedirect()->setPath('transactionalemails/returns/index');
        }
    }
}
