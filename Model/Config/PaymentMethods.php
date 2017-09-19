<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PaymentMethods
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string|null $websiteCode
     * @return array
     */
    public function getDisableMethods($websiteCode = null)
    {
        $methods = $this->scopeConfig->getValue(
            'swarming_kount/paymentmethods/disable_methods',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteCode
        );
        return !empty($methods) ? explode(',', $methods) : [];
    }
}
