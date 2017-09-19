<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Ens url for backend configuration
 */
class Ens extends Field
{
    /**
     * @var \Magento\Framework\Url
     */
    protected $frontendUrlBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Url $frontendUrlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Url $frontendUrlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->frontendUrlBuilder = $frontendUrlBuilder;
    }

    /**
     * Override method to output our custom HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return String
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return '<span id="' . $element->getHtmlId() . '">' . $this->getEnsUrl() . '</span>';
    }

    /**
     * @return string
     */
    protected function getEnsUrl()
    {
        $websiteId = $this->getRequest()->getParam('website');
        if (!empty($websiteId)) {
            $store = $this->_storeManager->getWebsite($websiteId)->getDefaultStore();
            $this->frontendUrlBuilder->setScope($store);
        }
        return $this->frontendUrlBuilder->getUrl('swarming_kount/ens', ['_forced_secure' => true, '_nosid' => true]);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderInheritCheckbox(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<td/>';
    }
}
