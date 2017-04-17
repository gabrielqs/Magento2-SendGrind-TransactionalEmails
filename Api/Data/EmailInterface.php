<?php

namespace Gabrielqs\TransactionalEmails\Api\Data;

interface EmailInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const EMAIL_ID              = 'email_id';
    const FROM                  = 'from';
    const TO                    = 'to';
    const BCC                   = 'bcc';
    const STATUS                = 'status';
    const SUBJECT               = 'subject';
    const BODY                  = 'body';
    const REQUEST               = 'request';
    const RESPONSE              = 'response';
    const ORDER_ID              = 'order_id';
    const CUSTOMER_ID           = 'customer_id';
    const CREATION_TIME         = 'creation_time';
    /**#@-*/

    /**
     * Possible Email statuses
     */
    const STATUS_SUCCESS        = 1;
    const STATUS_FAILURE        = 2;

    /**
     * Get Bcc
     *
     * @return string|null
     */
    public function getBcc();

    /**
     * Get Body
     *
     * @return string|null
     */
    public function getBody();

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get CustomerId
     *
     * @return integer|null
     */
    public function getCustomerId();

    /**
     * Get From
     *
     * @return string|null
     */
    public function getFrom();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Order Id
     *
     * @return integer|null
     */
    public function getOrderId();

    /**
     * Get Request
     *
     * @return string|null
     */
    public function getRequest();

    /**
     * Get Response
     *
     * @return string|null
     */
    public function getResponse();

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Get Subject
     *
     * @return string|null
     */
    public function getSubject();

    /**
     * Get To
     *
     * @return string|null
     */
    public function getTo();

    /**
     * Set Bcc
     *
     * @param string $bcc
     * @return $this
     */
    public function setBcc($bcc);

    /**
     * Set Body
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body);

    /**
     * Set Customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Set From
     *
     * @param string $from
     * @return $this
     */
    public function setFrom($from);

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set Order ID
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set Subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * Set To
     *
     * @param string $to
     * @return $this
     */
    public function setTo($to);

    /**
     * Set Creation Time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime($creationTime);
}
