<?php

namespace Gabrielqs\TransactionalEmails\Block\Adminhtml;

use \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text as TextRenderer;
use \Magento\Backend\Block\Context;
use \Magento\Framework\DataObject;
use \Gabrielqs\TransactionalEmails\Helper\Data as Helper;
use \Gabrielqs\TransactionalEmails\Model\Email;

class StatusRenderer extends TextRenderer
{
    /**
     * Order Repository
     * @var Helper
     */
    protected $helper = null;

    /**
     * IdRenderer constructor.
     * @param Context $context
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * Gets an Increment id from an order entity_id
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        /** @var Email $row $label */
        $label = $this->helper->getStatusLabel($row);
        return $label;
    }
}
