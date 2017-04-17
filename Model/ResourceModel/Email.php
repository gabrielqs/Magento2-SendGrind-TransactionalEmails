<?php

namespace Gabrielqs\TransactionalEmails\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Email extends AbstractDb
{
    /**
     * Email Abstract Resource Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('transactionalemails_email', 'email_id');
    }
}
