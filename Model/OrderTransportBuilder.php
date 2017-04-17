<?php

namespace Gabrielqs\TransactionalEmails\Model;

use Magento\Framework\Mail\Template\TransportBuilder;

class OrderTransportBuilder extends TransportBuilder
{
    /**
     * Calls the parent prepare message method. Needed to make it possible to test the Model
     * @return $this
     */
    protected function _callParentPrepareMessage()
    {
        /** @var self $return  */
        $return = parent::prepareMessage();
        return $return;
    }

    /**
     * Calls the parent prepareMessage method and adds the order id to the message object
     * @return $this
     */
    protected function prepareMessage()
    {
        $this->_callParentPrepareMessage();
        if (array_key_exists('order', $this->templateVars)) {
            $order = $this->templateVars['order'];
            $orderId = $order->getId();
            /** @var \Gabrielqs\TransactionalEmails\Model\Message $this->message */
            $this->message->setOrderId($orderId);
        }
        return $this;
    }
}