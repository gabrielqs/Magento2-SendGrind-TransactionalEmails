<?php

namespace Gabrielqs\TransactionalEmails\Model;

use \Magento\Customer\Api\Data\CustomerInterface;
use \Magento\Customer\Model\Customer;
use \Magento\Customer\Model\ResourceModel\CustomerRepository;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Data\Collection\AbstractDb;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Sales\Model\Order;
use \Magento\Sales\Model\OrderRepository as SalesOrderRepository;
use \Magento\Sales\Model\Order as SalesOrder;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailInterface;

/**
 * Class File
 * @package Gabrielqs\TransactionalEmails\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Email extends AbstractModel implements EmailInterface, IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'transactionalemails_email';

    /**
     * Customer
     * @var Customer|null
     */
    protected $_customer = null;

    /**
     * Customer Repository
     * @var CustomerRepository|null
     */
    protected $_customerRepository = null;

    /**
     * Order
     * @var Order|null
     */
    protected $_salesOrder = null;

    /**
     * Sales Order Repository
     * @var SalesOrderRepository|null
     */
    protected $_salesOrderRepository = null;

    /**
     * Search Criteria Builder
     * @var SearchCriteriaBuilder|null
     */
    protected $_searchCriteriaBuilder = null;

    /**
     * Storage Folder Name
     * @var string
     */
    protected $_storageFolderName = '/transactionalEmailsemails/';

    /**
     * Email constructor.
     * @param Context $context
     * @param Registry $registry
     * @param CustomerRepository $customerRepository
     * @param SalesOrderRepository $salesOrderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CustomerRepository $customerRepository,
        SalesOrderRepository $salesOrderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $return = parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_customerRepository = $customerRepository;
        $this->_salesOrderRepository = $salesOrderRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        return $return;
    }

    /**
     * Email Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Gabrielqs\TransactionalEmails\Model\ResourceModel\Email');
    }

    /**
     * Tries to find a customer id based on the email currently set on the model
     * @return $this
     */
    public function findAndSetCustomerIdFromEmail()
    {
        $searchCriteria = $this
            ->_searchCriteriaBuilder
            ->addFilter(CustomerInterface::EMAIL, $this->getTo(), 'eq')
            ->create();
        $searchResult = $this->_customerRepository->getList($searchCriteria);

        if ($searchResult->getTotalCount()) {
            foreach ($searchResult->getItems() as $customer) {
                /** @var CustomerInterface $customer */
                $this->setCustomerId($customer->getId());
            }
        } else {
            $this->setCustomerId(null);
        }

        return $this;
    }

    /**
     * Get Bcc
     *
     * @return string|null
     */
    public function getBcc()
    {
        return $this->getData(self::BCC);
    }

    /**
     * Get Body
     *
     * @return string|null
     */
    public function getBody()
    {
        return $this->getData(self::BODY);
    }

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Returns the Customer object related to this entity
     * @return Customer
     */
    public function getCustomer()
    {
        if ($this->_customer === null) {
            $customer = $this->_customerRepository->getById($this->getCustomerId());
            $this->_customer = $customer;
        }
        return $this->_customer;
    }

    /**
     * Get CustomerId
     *
     * @return integer|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Get From
     *
     * @return string|null
     */
    public function getFrom()
    {
        return $this->getData(self::FROM);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::EMAIL_ID);
    }

    /**
     * Return identities
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Returns the Sales Order object related to this entity
     * @return SalesOrder
     */
    public function getOrder()
    {
        if ($this->_salesOrder === null) {
            $order = $this->_salesOrderRepository->get($this->getOrderId());
            $this->_salesOrder = $order;
        }
        return $this->_salesOrder;
    }

    /**
     * Get Order Id
     *
     * @return integer|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Get Request
     *
     * @return string|null
     */
    public function getRequest()
    {
        return $this->getData(self::REQUEST);
    }

    /**
     * Get Response
     *
     * @return string|null
     */
    public function getResponse()
    {
        return $this->getData(self::RESPONSE);
    }

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get Subject
     *
     * @return string|null
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * Get To
     *
     * @return string|null
     */
    public function getTo()
    {
        return $this->getData(self::TO);
    }

    /**
     * Set Bcc
     *
     * @param string $bcc
     * @return $this
     */
    public function setBcc($bcc)
    {
        return $this->setData(self::BCC, $bcc);
    }

    /**
     * Set Body
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        return $this->setData(self::BODY, $body);
    }

    /**
     * Set Creation Time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set Customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set From
     *
     * @param string $from
     * @return $this
     */
    public function setFrom($from)
    {
        return $this->setData(self::FROM, $from);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::EMAIL_ID, $id);
    }

    /**
     * Set Order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Set Request
     *
     * @param string $request
     * @return $this
     */
    public function setRequest($request)
    {
        return $this->setData(self::REQUEST, $request);
    }

    /**
     * Set Response
     *
     * @param string $response
     * @return $this
     */
    public function setResponse($response)
    {
        return $this->setData(self::RESPONSE, $response);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set Subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * Set To
     *
     * @param string $to
     * @return $this
     */
    public function setTo($to)
    {
        return $this->setData(self::TO, $to);
    }
}
