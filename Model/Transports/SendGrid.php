<?php
namespace Gabrielqs\TransactionalEmails\Model\Transports;

use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Mail\MessageInterface;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;
use \Gabrielqs\TransactionalEmails\Api\TransportInterface;
use \Gabrielqs\TransactionalEmails\Model\Message as GabrielqsMessage;
use \SendGrid as SendGridApi;
use \SendGrid\Response as SendGridResponse;
use \SendGrid\Mail as SendGridMailHelper;
use \SendGrid\MailFactory as SendGridMailHelperFactory;
use \SendGrid\Email as SendGridEmail;
use \SendGrid\EmailFactory as SendGridEmailFactory;
use \SendGrid\Content as SendGridContent;
use \SendGrid\ContentFactory as SendGridContentFactory;
use \SendGrid\Personalization as SendGridPersonalization;

class SendGrid implements TransportInterface
{
    /**
     * Success Status Code
     */
    const SUCCESS_STATUS_CODE = 202;

    /**
     * Content Type HTML
     */
    const CONTENT_TYPE_HTML = 'text/html';

    /**
     * Content Type Plain
     */
    const CONTENT_TYPE_PLAIN = 'text/plain';

    /**
     * SendGrid Api
     * @var SendGrid
     */
    protected $_api;

    /**
     * Data Helper
     * @var Helper
     */
    protected $_helper;

    /**
     * SendGrid Content Factory
     * @var SendGridContentFactory
     */
    protected $_sendGridContentFactory;

    /**
     * SendGrid Email Factory
     * @var SendGridEmailFactory
     */
    protected $_sendGridEmailFactory;

    /**
     * SendGrid Mail Helper Factory
     * @var SendGridMailHelperFactory
     */
    protected $_sendGridMailHelperFactory;

    /**
     * SendGrid constructor.
     * @param Helper $helper
     * @param SendGridEmailFactory $sendGridEmailFactory
     * @param SendGridContentFactory $sendGridContentFactory
     * @param SendGridMailHelperFactory $sendGridMailHelperFactory
     */
    public function __construct(
        Helper $helper,
        SendGridEmailFactory $sendGridEmailFactory,
        SendGridContentFactory $sendGridContentFactory,
        SendGridMailHelperFactory $sendGridMailHelperFactory
    ) {
        $this->_helper = $helper;
        $this->_sendGridEmailFactory = $sendGridEmailFactory;
        $this->_sendGridContentFactory = $sendGridContentFactory;
        $this->_sendGridMailHelperFactory = $sendGridMailHelperFactory;
    }

    /**
     * Retrieves BCCs from MessageInterface object and adds it to the Mail instance
     * @param SendGridMailHelper $mail
     * @param MessageInterface $message
     * @return SendGridMailHelper
     */
    protected function _addBccs(SendGridMailHelper $mail, MessageInterface $message)
    {
        /** @var GabrielqsMessage $message */
        $bccHeaders = (array) $message->getHeader(GabrielqsMessage::HEADER_BCC);
        foreach ($bccHeaders as $bcc) {
            if (is_string($bcc)) {
                $bccObject = new SendGridEmail(null, $bcc);
                $this->_getSendGridPersonalization($mail)->addBcc($bccObject);
            }
        }
        return $mail;
    }

    /**
     * Instantiates and returns the SendGrid Api
     * @return SendGridApi
     */
    protected function _getApi()
    {
        if ($this->_api === null) {
            # Couldn't get to make it work through a factory, don't know why. Had to instantiate it directly instead
            $this->_api = new SendGridApi($this->_helper->getSendGridApiKey());
        }
        return $this->_api;
    }

    /**
     * Returns a SendGridContent instance built from the MessageInterface object
     * @param MessageInterface $message
     * @return SendGridContent
     */
    protected function _getContent(MessageInterface $message)
    {
        /** @var GabrielqsMessage $message */
        $content = $message->getBody();
        if (preg_match('/text\/html/i', $content->getHeaders())) {
            $type = self::CONTENT_TYPE_HTML;
        } else {
            $type = self::CONTENT_TYPE_PLAIN;
        }
        return new SendGridContent($type, $content->getRawContent());
    }

    /**
     * Returns a SendGridEmail instance built from the 'From' header in the given MessageInterface object
     * @param MessageInterface $message
     * @return SendGridEmail
     */
    protected function _getFrom(MessageInterface $message)
    {
        /** @var GabrielqsMessage $message */
        $splitFrom = $this->_getSplitFromEmail($message);
        $fromName = $splitFrom->name;
        $fromEmail = $splitFrom->email;
        return new SendGridEmail($fromName, $fromEmail);
    }

    /**
     * Retrieves Personalization from SendGrid Mail Helper
     * @param SendGridMailHelper $mail
     * @param int $personalizationIndex
     * @return SendGridPersonalization
     */
    protected function _getSendGridPersonalization(SendGridMailHelper $mail, $personalizationIndex = 0)
    {
        return $mail->personalization[$personalizationIndex];
    }

    /**
     * Splits a From header of the form Name <email@email.com> into an \stdClass object with name and email
     * @param MessageInterface $message
     * @return \stdClass
     */
    protected function _getSplitFromEmail(MessageInterface $message)
    {
        $return = new \stdClass();
        /** @var GabrielqsMessage $message */
        $fromHeader = $message->getHeader(GabrielqsMessage::HEADER_FROM);
        $fromHeader = $fromHeader[0];
        if (strstr($fromHeader, '<')) {
            $matches = [];
            preg_match('/(.*)<(.+)>/i', $fromHeader, $matches);
            if (count($matches) == 3) {
                $return->name = trim($matches[1]);
                $return->email = trim($matches[2]);
            } else {
                $return->name = '';
                $return->email = $fromHeader;
            }
        } else {
            $return->name = '';
            $return->email = $fromHeader;
        }
        return $return;
    }

    /**
     * Returns a SendGridEmail instance built from the 'To' header in the given MessageInterface object
     * @param MessageInterface $message
     * @return SendGridEmail
     */
    protected function _getTo(MessageInterface $message)
    {
        /** @var GabrielqsMessage $message */
        $to = $message->getHeader(GabrielqsMessage::HEADER_TO);
        # Looks strange, but that's how Zend_Mail works. When you retrieve a header, it returns an array.
        $to = $to[0];
        return new SendGridEmail(null, $to);
    }

    /**
     * Returns a SendGridMailHelper instance created using the provided parameters
     * @param MessageInterface $message
     * @return SendGridMailHelper
     */
    protected function _getMail(MessageInterface $message)
    {
        $mail = new SendGridMailHelper(
            $this->_getFrom($message),
            $message->getSubject(),
            $this->_getTo($message),
            $this->_getContent($message)
        );
        $this->_addBccs($mail, $message);
        return $mail;
    }

    /**
     * Makes the actual request using the API
     * @param SendGridMailHelper $mail
     * @return SendGridResponse
     */
    protected function _makeSendGridRequest(SendGridMailHelper $mail)
    {
        return $this->_getApi()->client->mail()->send()->post($mail);
    }

    /**
     * Sends an e-mail using SendGrid's Api
     * @param MessageInterface $message
     * @return void
     * @throws LocalizedException
     */
    public function send(MessageInterface $message)
    {
        /** @var GabrielqsMessage $message */
        $mail = $this->_getMail($message);
        /** @var SendGridResponse $response */
        $response = $this->_makeSendGridRequest($mail);
        $message->setRequest(json_encode($mail));
        $message->setResponse(json_encode($response));
        if ($response->statusCode() != self::SUCCESS_STATUS_CODE) {
            $responseErrors = json_decode($response->body());
            $exceptionErrors = [];
            foreach ((array) $responseErrors->errors as $responseError) {
                $exceptionErrors[] = $responseError->message . ' Field: ' . $responseError->field;
            }
            $exceptionErrorMessage = implode(', ', $exceptionErrors);
            throw new LocalizedException(__('SendGrid returned some error message(s): %1', $exceptionErrorMessage));
        }
    }
}
