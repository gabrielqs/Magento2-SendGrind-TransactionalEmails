<?php

namespace Gabrielqs\TransactionalEmails\Model\Source;

use \Magento\Framework\Option\ArrayInterface;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailInterface;

class Status implements ArrayInterface
{
    /**
     * Returns all emails' possible statuses
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Success'),
                'value' => EmailInterface::STATUS_SUCCESS,
            ],
            [
                'label' => __('Failure'),
                'value' => EmailInterface::STATUS_FAILURE,
            ],
        ];
    }
}