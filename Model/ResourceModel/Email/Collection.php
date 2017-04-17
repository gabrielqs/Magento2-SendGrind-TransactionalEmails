<?php
namespace Gabrielqs\TransactionalEmails\Model\ResourceModel\Email;

use \Gabrielqs\TransactionalEmails\Api\Data\EmailSearchResultsInterface;
use \Gabrielqs\TransactionalEmails\Model\ResourceModel\Collection\AbstractCollection;

class Collection extends AbstractCollection implements EmailSearchResultsInterface
{
    /**
     * Email Collection Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Gabrielqs\TransactionalEmails\Model\Email',
            'Gabrielqs\TransactionalEmails\Model\ResourceModel\Email'
        );
    }
}
