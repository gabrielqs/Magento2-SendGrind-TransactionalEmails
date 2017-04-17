<?php

namespace Gabrielqs\TransactionalEmails\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Store\Model\ScopeInterface;
use \Psr\Log\LoggerInterface;
use \Gabrielqs\TransactionalEmails\Model\Source\Provider;
use \Gabrielqs\TransactionalEmails\Model\Email;
use \Gabrielqs\TransactionalEmails\Model\Source\Status;

class Data extends AbstractHelper
{
    /**
     * Config Paths - Provider
     */
    const XML_PATH_PROVIDER = 'system/smtp/provider';

    /**
     * Config Paths - Test Email Recipient
     */
    const XML_PATH_TEST_EMAIL_RECIPIENT = 'system/smtp/test_email_recipient';

    /**
     * Config Paths - SendGrid Api Key
     */
    const XML_PATH_SENDGRID_API_KEY = 'system/smtp/sendgrid_apikey';

    /**
     * Config Paths - Sender Identity
     */
    const SENDER_IDENTITY = 'checkout/payment_failed/identity';

    /**
     * Logger
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * Provider Source
     * @var Provider
     */
    protected $_providerSource;

    /**
     * Status Source
     * @var Provider
     */
    protected $_statusSource;

    /**
     * Helper Constructor
     * @param Context $context
     * @param Provider $provider
     * @param Status $status
     */
    public function __construct(
        Context $context,
        Provider $provider,
        Status $status
    ) {
        parent::__construct($context);
        $this->_logger = $context->getLogger();
        $this->_providerSource = $provider;
        $this->_statusSource = $status;
    }

    /**
     * Retrieves Provider From Configuration
     * @param null|int $store
     * @return string
     */
    public function getProviderCode($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROVIDER, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Retrieves Provider From Configuration
     * @param null|int $store
     * @return string
     * @throws LocalizedException
     */
    public function getProviderApiClassName($store = null)
    {
        $providerCode = $this->getProviderCode($store);
        if (!$providerCode) {
            throw new LocalizedException(__('No transport provider defined.'));
        }
        return $this->_providerSource->getApiClassName($providerCode);
    }

    /**
     * Retrieves SendGrid Api Key From Configuration
     * @param null|int $store
     * @return string
     */
    public function getSendGridApiKey($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SENDGRID_API_KEY, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Returns the status label for a given Email instance
     * @param Email $email
     * @return string
     */
    public function getStatusLabel(Email $email)
    {
        $return = '';
        $statuses = $this->_statusSource->toOptionArray();
        foreach ($statuses as $status) {
            if ($email->getStatus() == $status['value']) {
                $return = $status['label'];
                break;
            }
        }
        return $return;
    }

    /**
     * Retrieves Test Email Recipient From Configuration
     * @param null|int $store
     * @return string
     */
    public function getTestEmailRecipient($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TEST_EMAIL_RECIPIENT, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Retrieves Test Sender From Configuration
     * @return string
     */
    public function getTestSender()
    {
        return $this->scopeConfig->getValue(self::SENDER_IDENTITY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Logger
     * @param string $msg
     * @return void
     * @codeCoverageIgnore
     */
    public function log($msg)
    {
        $this->_logger->info($msg);
    }
}