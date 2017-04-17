<?php

namespace Gabrielqs\TransactionalEmails\Block\Adminhtml\Order\View\Tab;

use \Magento\Backend\Block\Template\Context;
use \Magento\Backend\Block\Widget\Tab\TabInterface;
use \Magento\Framework\Registry;
use \Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use \Magento\Sales\Helper\Admin;
use \Magento\Authorization\Model\Acl\AclRetriever;
use \Magento\Backend\Model\Auth\Session as AuthSession;

/**
 * Class TransactionalEmails
 *
 * @package Gabrielqs\Tiny\Block\Adminhtml\Order\View\Tab
 */
class TransactionalEmails extends AbstractOrder implements TabInterface
{
    /**
     * ACL Retriever
     * @var AclRetriever
     */
    protected $aclRetriever;

    /**
     * Auth Session
     * @var AuthSession
     */
    protected $authSession;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param AclRetriever $aclRetriever
     * @param AuthSession $authSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        AclRetriever $aclRetriever,
        AuthSession $authSession,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->authSession = $authSession;
        $this->aclRetriever = $aclRetriever;
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $return = false;
        $resources = $this->getAllowedResources();
        if (
            in_array('Gabrielqs_TransactionalEmails::orderEmailsList', $resources) ||
            in_array('Magento_Backend::all', $resources)
        ) {
            $return = true;
        }
        return $return;
    }

    /**
     * Current User Allowed Resources
     * @return \string[]
     */
    public function getAllowedResources()
    {
        $user = $this->authSession->getUser();
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        return $resources;
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
        return $this->getUrl('transactionalemails/order/index', ['_current' => true]);
    }
}