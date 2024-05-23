<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount2FA\Block\Customer\Account;

class NavLink extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @inherit
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Add check for if 2fa is enabled before showing nav item in customer account
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->scopeConfig->isSetFlag('kount_kount2fa/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return parent::_toHtml();
        }
        return '';
    }
}
