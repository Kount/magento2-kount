<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;

class Currency extends Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->getPriceScope() == \Magento\Store\Model\Store::PRICE_SCOPE_GLOBAL
            ? ''
            : parent::render($element);
    }

    /**
     * @return string
     */
    private function getPriceScope()
    {
        return $this->_scopeConfig->getValue(
            \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
