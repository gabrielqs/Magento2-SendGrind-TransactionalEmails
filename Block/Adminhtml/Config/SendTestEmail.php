<?php

namespace Gabrielqs\TransactionalEmails\Block\Adminhtml\Config;

use \Magento\Config\Block\System\Config\Form\Field;
use \Magento\Framework\Data\Form\Element\AbstractElement;

class SendTestEmail extends Field
{
    /**
     * Button Template
     * @var string
     */
    protected $_template = 'config/sendTestEmail.phtml';

    /**
     * Returns Button HTML
     * @param AbstractElement $element
     * @return string
     * @codeCoverageIgnore
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $label = $originalData['button_label'];
        $this->addData([
            'button_label' => __($label),
            'button_url'   => $this->getUrl('transactionalemails/config/test'),
            'html_id'      => $element->getHtmlId(),
        ]);
        return $this->_toHtml();
    }
}