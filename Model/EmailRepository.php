<?php
namespace Gabrielqs\TransactionalEmails\Model;

use \Gabrielqs\TransactionalEmails\Api\EmailRepositoryInterface;
use \Magento\Framework\Api\DataObjectHelper;
use \Magento\Framework\Api\SortOrder;
use \Magento\Framework\Exception\CouldNotDeleteException;
use \Magento\Framework\Exception\CouldNotSaveException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Reflection\DataObjectProcessor;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\Search\FilterGroup;
use \Gabrielqs\TransactionalEmails\Model\Email as Email;
use \Gabrielqs\TransactionalEmails\Model\ResourceModel\Email as EmailResource;
use \Gabrielqs\TransactionalEmails\Model\EmailFactory as EmailFactory;
use \Gabrielqs\TransactionalEmails\Model\ResourceModel\Email\CollectionFactory as EmailCollectionFactory;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailInterfaceFactory;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailSearchResultsInterface;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailSearchResultsInterfaceFactory;
use \Gabrielqs\TransactionalEmails\Api\Data\EmailInterface;

/**
 * Class EmailRepository
 * @package Gabrielqs\TransactionalEmails\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EmailRepository implements EmailRepositoryInterface
{
    /**
     * Email Resource
     * @var EmailResource
     */
    protected $resource;

    /**
     * Email Factory
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * Email Collection Factory
     * @var EmailCollectionFactory
     */
    protected $emailCollectionFactory;

    /**
     * Email Search Results Interface
     * @var EmailSearchResultsInterface
     */
    protected $searchResultsFactory;

    /**
     * Data Object Helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Data Object Processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Email Interface Factory
     * @var EmailInterfaceFactory
     */
    protected $emailInterfaceFactory;

    /**
     * Email Repository
     * @param EmailResource $resource
     * @param EmailFactory $emailFactory
     * @param EmailInterfaceFactory $emailInterfaceFactory
     * @param EmailCollectionFactory $emailCollectionFactory
     * @param EmailSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        EmailResource $resource,
        EmailFactory $emailFactory,
        EmailInterfaceFactory $emailInterfaceFactory,
        EmailCollectionFactory $emailCollectionFactory,
        EmailSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->emailFactory = $emailFactory;
        $this->emailCollectionFactory = $emailCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->emailInterfaceFactory = $emailInterfaceFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     * @param FilterGroup $filterGroup
     * @param EmailSearchResultsInterface $searchResult
     * @return void
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        EmailSearchResultsInterface $searchResult
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }
        if ($fields) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Delete Email
     * @param EmailInterface $email
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(EmailInterface $email)
    {
        try {
            $this->resource->delete($email);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Email by given Email Identity
     * @param string $emailId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($emailId)
    {
        return $this->delete($this->getById($emailId));
    }

    /**
     * Load Email data by given Email Identity
     * @param string $emailId
     * @return Email
     * @throws NoSuchEntityException
     */
    public function getById($emailId)
    {
        $email = $this->emailFactory->create();
        $this->resource->load($email, $emailId);
        if (!$email->getId()) {
            throw new NoSuchEntityException(__('E-mail with id "%1" does not exist.', $emailId));
        }
        return $email;
    }

    /**
     * Load Email data collection by given search criteria
     * @param SearchCriteriaInterface $searchCriteria
     * @return EmailSearchResultsInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var EmailSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setCurPage($searchCriteria->getCurrentPage());
        $searchResult->setPageSize($searchCriteria->getPageSize());
        return $searchResult;
    }

    /**
     * Save Email data
     * @param EmailInterface $email
     * @return Email
     * @throws CouldNotSaveException
     */
    public function save(EmailInterface $email)
    {
        try {
            $this->resource->save($email);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $email;
    }
}
