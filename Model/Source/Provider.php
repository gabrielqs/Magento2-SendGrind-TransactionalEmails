<?php

namespace Gabrielqs\TransactionalEmails\Model\Source;

use \Magento\Framework\Option\ArrayInterface;

class Provider implements ArrayInterface
{
    /**
     * Sendmail Provider
     */
    const PROVIDER_SENDMAIL = 'sendmail';

    /**
     * SendGrid Provider
     */
    const PROVIDER_SENDGRID = 'sendgrid';

    /**
     * Based on the provider code, returns the class name for the Api Model
     * Returns null in case no provider corresponds to the given provider code
     * @param string $providerCode
     * @return mixed|null
     */
    public function getApiClassName($providerCode)
    {
        $return = null;
        foreach ($this->toOptionArray() as $provider) {
            if ($provider['value'] == $providerCode) {
                $return = $provider['class'];
                break;
            }
        }
        return $return;
    }

    /**
     * Returns all providers we can use to send transactional emails
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('sendmail'),
                'value' => self::PROVIDER_SENDMAIL,
                'class' => '\Magento\Framework\Mail\Transport'
            ],
            [
                'label' => __('SendGrid'),
                'value' => self::PROVIDER_SENDGRID,
                'class' => '\Gabrielqs\TransactionalEmails\Model\Transports\SendGrid'
            ],
        ];
    }
}