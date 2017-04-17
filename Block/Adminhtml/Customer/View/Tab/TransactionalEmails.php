<?php

namespace Gabrielqs\TransactionalEmails\Block\Adminhtml\Customer\View\Tab;

use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Ui\Component\Layout\Tabs\TabWrapper;
use Magento\Customer\Controller\RegistryConstants;


/**
 * Class TransactionalEmails
 *
 * @package Gabrielqs\Tiny\Block\Adminhtml\Order\View\Tab
 */
class TransactionalEmails extends TabWrapper
{

    /**
     * Is Ajax Loaded
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * Core Registry
     * @var Registry|null
     */
    protected $registry = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    public function canShowTab()
    {
        return $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('E-mails');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('E-mails');
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('transactionalemails/customer/index', ['_current' => true]);
    }
}