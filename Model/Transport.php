<?php

namespace Gabrielqs\TransactionalEmails\Model;

use \Magento\Framework\Mail\TransportInterface;
use \Magento\Framework\Mail\Transport as AbstractTransport;
use \Magento\Framework\Mail\MessageInterface;
use \Magento\Framework\ObjectManagerInterface;
use \Psr\Log\LoggerInterface;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;
use \Gabrielqs\TransactionalEmails\Api\TransportInterface as GabrielqsTransportInterface;

class Transport extends AbstractTransport implements TransportInterface
{
    /**
     * To be filled up with the API
     * @var GabrielqsTransportInterface
     */
    protected $_api;

    /**
     * Transactional Emails Factory
     * @var EmailFactory
     */
    protected $_emailFactory;

    /**
     * Transactional Emails Repository
     * @var EmailRepository
     */
    protected $_emailRepository;

    /**
     * Transactional Emails Helper
     * @var Helper
     */
    protected $_helper;

    /**
     * Logger
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * Message
     * @var Message
     */
    protected $_message;

    /**
     * Object Manater
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Transport Constructor
     * @param MessageInterface $message
     * @param LoggerInterface $logger
     * @param Helper $helper
     * @param ObjectManagerInterface $objectManager
     * @param EmailFactory $emailFactory
     * @param EmailRepository $emailRepository
     * @param array|null $parameters
     */
    public function __construct(
        MessageInterface $message,
        LoggerInterface $logger,
        Helper $helper,
        ObjectManagerInterface $objectManager,
        EmailFactory $emailFactory,
        EmailRepository $emailRepository,
        $parameters = null
    ) {
        parent::__construct($message, $parameters);
        $this->_logger = $logger;
        $this->_helper = $helper;
        $this->_objectManager = $objectManager;
        $this->_emailFactory = $emailFactory;
        $this->_emailRepository = $emailRepository;
    }

    /**
     * Initializes and Returns the API model based on the store configuration
     * @return GabrielqsTransportInterface
     */
    protected function _getApi()
    {
        if ($this->_api === null) {
            $this->_api = $this->_objectManager->create($this->_helper->getProviderApiClassName());
        }
        return $this->_api;
    }

    /**
     * Initializes Transport and sends message
     * @return bool
     * @throws \Exception
     */
    public function _sendMail()
    {
        $email = $this->_emailFactory->create()
            ->setBody($this->_message->getBody()->getRawContent())
            ->setFrom($this->_message->getFrom())
            ->setTo($this->_message->getToEmail())
            ->setSubject($this->_message->getSubject())
            ->setBcc($this->_message->getBccAsAString())
            ->setOrderId($this->_message->getOrderId())
            ->findAndSetCustomerIdFromEmail();

        try {
            $status = $this->_getApi()->send($this->_message);
            $email
                ->setStatus(Email::STATUS_SUCCESS)
                ->setRequest($this->_message->getRequest())
                ->setResponse($this->_message->getResponse());
            $this->_emailRepository->save($email);
        } catch (\Exception $e) {
            $email
                ->setStatus(Email::STATUS_FAILURE)
                ->setRequest($this->_message->getRequest())
                ->setResponse($this->_message->getResponse());
            $this->_emailRepository->save($email);
            throw $e;
        }

        return $status;
    }
}