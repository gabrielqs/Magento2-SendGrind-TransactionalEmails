<?php

namespace Gabrielqs\TransactionalEmails\Model;

use \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use \Magento\Ui\DataProvider\AbstractDataProvider;
use \Gabrielqs\TransactionalEmails\Model\ResourceModel\Email\Collection as EmailCollection;
use \Gabrielqs\TransactionalEmails\Model\ResourceModel\Email\CollectionFactory as EmailCollectionFactory;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * Email Collection
     * @var EmailCollection
     */
    protected $collection;

    /**
     * Filter Pool
     * @var FilterPool
     */
    protected $filterPool;

    /**
     * Data Provider Constructor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param EmailCollectionFactory $collectionFactory
     * @param FilterPool $filterPool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EmailCollectionFactory $collectionFactory,
        FilterPool $filterPool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->filterPool = $filterPool;
    }
}