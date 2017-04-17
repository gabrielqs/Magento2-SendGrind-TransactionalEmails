<?php

namespace Gabrielqs\TransactionalEmails\Model;

use Magento\Framework\Mail\Message as OriginalMessage;

class Message extends OriginalMessage
{
    /**
     * Header - Bcc
     */
    const HEADER_BCC = 'Bcc';

    /**
     * Header - From
     */
    const HEADER_FROM = 'From';

    /**
     * Header - To
     */
    const HEADER_TO = 'To';

    /**
     * Order Id
     * @var int
     */
    protected $_orderId;

    /**
     * Request
     * @var string
     */
    protected $_request;

    /**
     * Response
     * @var string
     */
    protected $_response;

    /**
     * Return BCCs as a string
     * @return string
     */
    public function getBccAsAString()
    {
        $bcc = [];
        $bccHeaders = $this->getHeader(self::HEADER_BCC);
        foreach ((array) $bccHeaders as $bccHeader) {
            if (is_string($bccHeader)) {
                $bcc[] = $bccHeader;
            }
        }
        return implode(', ', $bcc);
    }

    /**
     * Retrieves a header from a MessageInterface Object
     * @param string $headerName
     * @return array
     */
    public function getHeader($headerName)
    {
        $return = null;
        $headers = $this->getHeaders();
        foreach ($headers as $name => $value) {
            if ($name == $headerName) {
                $return = $value;
                break;
            }
        }
        return $return;
    }

    /**
     * Order Id Getter
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->_orderId;
    }

    /**
     * Request Getter
     * @return string|null
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Response Getter
     * @return string|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * To Getter
     * @return string|null
     */
    public function getTo()
    {
        $to = $this->getHeader(self::HEADER_TO);
        return $to[0];
    }

    /**
     * Returns only the email from the To Header
     * @return string|null
     */
    public function getToEmail()
    {
        $return = $to = $this->getTo();
        if (strstr($to, '<')) {
            $matches = [];
            preg_match('/(.*)<(.+)>/i', $to, $matches);
            if (count($matches) == 3) {
                $return = $matches[2];
            }
        }
        return $return;
    }

    /**
     * Order Id Setter
     * @param int $orderId
     * @return Message
     */
    public function setOrderId($orderId)
    {
        $this->_orderId = (int) $orderId;
        return $this;
    }

    /**
     * Request Setter
     * @param string $request
     * @return Message
     */
    public function setRequest($request)
    {
        $this->_request = (string) $request;
        return $this;
    }

    /**
     * Response Setter
     * @param string $response
     * @return Message
     */
    public function setResponse($response)
    {
        $this->_response = (string) $response;
        return $this;
    }
}