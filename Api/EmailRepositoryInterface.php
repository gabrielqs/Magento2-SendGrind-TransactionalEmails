<?php

namespace Gabrielqs\TransactionalEmails\Api;

use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailInterface;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailSearchResultsInterface;

/**
 * Email CRUD interface.
 * @api
 */
interface EmailRepositoryInterface
{
    /**
     * Save Email.
     *
     * @param EmailInterface $file
     * @return EmailInterface
     * @throws LocalizedException
     */
    public function save(EmailInterface $file);

    /**
     * Retrieve Email.
     *
     * @param int $emailId
     * @return EmailInterface
     * @throws LocalizedException
     */
    public function getById($emailId);

    /**
     * Retrieve Emails matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return EmailSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Email.
     *
     * @param EmailInterface $file
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(EmailInterface $file);

    /**
     * Delete Email by ID.
     *
     * @param int $emailId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($emailId);
}
