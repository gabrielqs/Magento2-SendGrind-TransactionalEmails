<?php

namespace Gabrielqs\TransactionalEmails\Controller\Adminhtml;

/**
 * Declarations of core registry keys used by the TransactionalEmails module
 *
 */
class RegistryConstants
{

    /**
     * Registry key where current customer ID is stored
     */
    const CURRENT_CUSTOMER_ID = 'transactionalemails_current_customer_id';

    /**
     * Registry key where current email ID is stored
     */
    const CURRENT_EMAIL_ID = 'current_email_id';

    /**
     * Registry key where current order ID is stored
     */
    const CURRENT_ORDER_ID = 'transactionalemails_current_order_id';

}
