<?php

namespace Gabrielqs\TransactionalEmails\Block\Adminhtml\Customer\View\Tab\TransactionalEmails;

use \Magento\Backend\Block\Widget\Grid as BackendGrid;
use \Magento\Backend\Block\Widget\Grid\Extended;
use \Magento\Backend\Block\Template\Context;
use \Magento\Backend\Helper\Data as BackendHelper;
use \Magento\Framework\Registry;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Gabrielqs\TransactionalEmails\Controller\Adminhtml\RegistryConstants;
use \Gabrielqs\TransactionalEmails\Model\EmailRepository;
use \Gabrielqs\TransactionalEmails\Model\Email;
use \Gabrielqs\TransactionalEmails\Block\Adminhtml\StatusRenderer;

class Grid extends Extended
{
    /**
     * Core Registry
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Email  Repository
     * @var EmailRepository|null
     */
    protected $_emailRepository = null;

    /**
     * Search Criteria Builder
     * @var SearchCriteriaBuilder|null
     */
    protected $_searchCriteriaBuilder = null;

    /**
     * Constructor
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param EmailRepository $emailRepository
     * @param Registry $coreRegistry
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        EmailRepository $emailRepository,
        Registry $coreRegistry,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->_emailRepository = $emailRepository;
        $this->_coreRegistry = $coreRegistry;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
    }


    /**
     * Retrieves current customer id from registry
     * @return int
     */
    protected function _getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Prepares grid collection
     * @return BackendGrid
     */
    protected function _prepareCollection()
    {
        $searchCriteria = $this
            ->_searchCriteriaBuilder
            ->addFilter(Email::CUSTOMER_ID, $this->_getCustomerId(), 'eq')
            ->create();
        $collection = $this->_emailRepository->getList($searchCriteria);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepares Columns
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            Email::EMAIL_ID,
            [
                'header' => __('Id'),
                'sortable' => true,
                'index' => Email::EMAIL_ID,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            Email::FROM,
            [
                'header' => __('Sender'),
                'index' => Email::FROM
            ]
        );
        $this->addColumn(
            Email::TO,
            [
                'header' => __('Recipient'),
                'index' => Email::TO
            ]
        );
        $this->addColumn(
            Email::SUBJECT,
            [
                'header' => __('Subject'),
                'index' => Email::SUBJECT
            ]
        );
        $this->addColumn(
            Email::STATUS,
            [
                'header' => __('Status'),
                'index' => Email::STATUS,
                'renderer' => StatusRenderer::class
            ]
        );

        return parent::_prepareColumns();
    }


    /**
     * Retrieve the Url for a specified email row.
     *
     * @param \Magento\Sales\Model\Order|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('transactionalemails/sent/view', ['email_id' => $row->getData(Email::EMAIL_ID)]);
    }

    /**
     * Current Grid URL
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('transactionalEmails/customer/index', ['_current' => true]);
    }

}