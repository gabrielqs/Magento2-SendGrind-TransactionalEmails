<?php

namespace Gabrielqs\TransactionalEmails\Api;

use \Magento\Framework\Mail\MessageInterface;

interface TransportInterface
{
    /**
     * Sends the message
     * @param MessageInterface $message
     * @return bool
     */
    public function send(MessageInterface $message);
}