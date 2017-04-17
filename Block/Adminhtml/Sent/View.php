<?php

namespace Gabrielqs\TransactionalEmails\Block\Adminhtml\Sent;

use \Magento\Backend\Block\Template;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Gabrielqs\TransactionalEmails\Model\EmailRepository;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\RegistryConstants;
use \Gabrielqs\TransactionalEmails\Model\Email;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;

class View extends Template
{
    /**
     * Registry
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * Email
     * @var Email|null
     */
    protected $email = null;

    /**
     * Email Repository
     * @var EmailRepository|null
     */
    protected $emailRepository = null;

    /**
     * Helper
     * @var Helper|null
     */
    protected $helper = null;

    /**
     * Constructor
     * @param Context $context
     * @param Registry $registry
     * @param EmailRepository $emailRepository
     * @param Helper $helper
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EmailRepository $emailRepository,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $registry;
        $this->emailRepository = $emailRepository;
        $this->helper = $helper;
    }

    /**
     * Returns Email Info - Bcc
     * @return null|string
     */
    public function getBcc()
    {
        return $this->_getEmail()->getBcc();
    }

    /**
     * Returns Email Info - Body
     * Will return only the part inside <body> tags if the <body> tag is present
     * @return null|string
     */
    public function getBody()
    {
        $body = $this->_getEmail()->getBody();
        if (preg_match('/<body/i', $body)) {
            $matches = [];
            preg_match('/<body[^>]*>(.*?)<\/body>/is', $body, $matches);
            $return = $matches[1];
        } else {
            $return = $body;
        }
        return $return;
    }

    /**
     * Returns Email Info - CustomerId
     * @return null|string
     */
    public function getCustomerId()
    {
        return $this->_getEmail()->getCustomerId();
    }

    /**
     * Returns Email Info - CustomerId
     * @return null|string
     */
    public function getCustomerName()
    {
        return $this->_getEmail()->getCustomer()->getFirstName() . ' ' .
               $this->_getEmail()->getCustomer()->getLastName();
    }

    /**
     * Returns Email Info - CustomerUrl
     * @return null|string
     */
    public function getCustomerUrl()
    {
        return $this->getUrl('customer/index/edit', ['id' => $this->_getEmail()->getCustomerId()]);
    }

    /**
     * Retrieves current email
     * @return Email
     */
    protected function _getEmail()
    {
        if ($this->email === null) {
            $this->email = $this->emailRepository->getById($this->_getEmailId());
        }
        return $this->email;
    }

    /**
     * Retrieves current email id from registry
     * @return int
     */
    protected function _getEmailId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_EMAIL_ID);
    }

    /**
     * Returns Email Info - From
     * @return null|string
     */
    public function getFrom()
    {
        return $this->_getEmail()->getFrom();
    }

    /**
     * Returns Email Info - Status
     * @return string
     */
    public function getStatus()
    {
        return $this->helper->getStatusLabel($this->_getEmail());
    }

    /**
     * Returns Email Info - Subject
     * @return null|string
     */
    public function getSubject()
    {
        return $this->_getEmail()->getSubject();
    }

    /**
     * Returns Email Info - OrderId
     * @return null|string
     */
    public function getOrderId()
    {
        return $this->_getEmail()->getOrderId();
    }

    /**
     * Returns Email Info - OrderId
     * @return null|string
     */
    public function getOrderIncrementId()
    {
        return $this->_getEmail()->getOrder()->getIncrementId();
    }

    /**
     * Returns Email Info - OrderUrl
     * @return null|string
     */
    public function getOrderUrl()
    {
        return $this->getUrl('sales/order/view', ['order_id' => $this->_getEmail()->getOrderId()]);
    }

    /**
     * Returns Email Info - To
     * @return null|string
     */
    public function getTo()
    {
        return $this->_getEmail()->getTo();
    }

}