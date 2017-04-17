<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Framework\Registry;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\Page;
use \Magento\Framework\Controller\Result\Redirect;

abstract class Sent extends Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Gabrielqs_TransactionalEmails::sent_emails';

    /**
     * Core registry
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Constructor
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(Context $context, Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage(Page $resultPage)
    {
        $resultPage->setActiveMenu('Gabrielqs_TransactionalEmails::email_log')
            ->addBreadcrumb(__('System'), __('System'))
            ->addBreadcrumb(__('E-mail Log'), __('E-mail Log'));
        return $resultPage;
    }

    /**
     * Retrieves Email Id From Request
     * @return int
     */
    public function _getEmailIdFromRequest()
    {
        return (int) $this->_request->getParam('email_id');
    }

    /**
     * Gets result redirect
     * @return Redirect
     */
    protected function _getResultRedirect()
    {
        return $this->resultRedirectFactory->create();
    }

    /**
     * Prepends Title
     * @param Page $resultPage
     * @param string $title
     * @return Page
     */
    protected function _prependTitle(Page $resultPage, $title)
    {
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
