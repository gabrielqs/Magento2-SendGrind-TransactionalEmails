<?php

namespace Gabrielqs\TransactionalEmails\Api\Data;

use \Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Transactional Emails Email search results.
 * @api
 */
interface EmailSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Email list.
     *
     * @return EmailInterface[]
     */
    public function getItems();

    /**
     * Set Email list.
     *
     * @param EmailInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
