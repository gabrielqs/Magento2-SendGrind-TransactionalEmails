<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml\Config;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\App\Area;
use \Magento\Framework\Controller\Result\Redirect;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;

class Test extends Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::trans_email';

    /**
     * Template id for the test email
     */
    const EMAIL_TEMPLATE_TEST_ID = 'gabrielqs_transactionalemails_test_template';

    /**
     * Transport Builder
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Transactional Emails Helper
     * @var Helper
     */
    protected $_helper;

    /**
     * Test constructor.
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        Helper $helper
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->_helper           = $helper;
    }

    /**
     * Sends a test e-mail and returns to configuration page
     * @return Redirect
     */
    public function execute()
    {
        try {
            $this->_transportBuilder->setTemplateIdentifier(self::EMAIL_TEMPLATE_TEST_ID);
            $this->_transportBuilder->setFrom($this->_helper->getTestSender());
            $this->_transportBuilder->addTo($this->_helper->getTestEmailRecipient());
            $this->_transportBuilder->setTemplateVars([]);
            $this->_transportBuilder->setTemplateOptions([
                'area'  => Area::AREA_ADMINHTML,
                'store' => 1
            ]);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->messageManager->addSuccessMessage(__('Test e-mail successfully sent, please check your inbox.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('There was an error while sending your test e-mail: %1', $e->getMessage())
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }
}