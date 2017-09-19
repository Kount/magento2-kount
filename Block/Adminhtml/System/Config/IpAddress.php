<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * My Ip address link for backend configuration
 */
class IpAddress extends Field
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        array $data = []
    ) {
        $this->remoteAddress = $remoteAddress;
        parent::__construct($context, $data);
    }

    /**
     * Override method to output our custom HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return String
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return parent::_getElementHtml($element) . $this->getMyIpLink();
    }

    /**
     * @return string
     */
    protected function getMyIpLink()
    {
        return '<p class="note"><span>'
            . __('Current IP: %1', $this->remoteAddress->getRemoteAddress())
            . '</span></p>';
    }
}
