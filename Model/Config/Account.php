<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Account
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
     * @return bool
     */
    public function isEnabled($websiteCode = null)
    {
        return $this->scopeConfig->isSetFlag('swarming_kount/account/enabled', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
    }

    /**
     * @param string|null $websiteCode
     * @return bool
     */
    public function isAvailable($websiteCode = null)
    {
        return $this->isEnabled($websiteCode)
            && !empty($this->getMerchantNumber($websiteCode))
            && !empty($this->getWebsite($websiteCode))
            && !empty($this->getApiKey($websiteCode));
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->scopeConfig->getValue(Store::XML_PATH_PRICE_SCOPE, ScopeInterface::SCOPE_STORE) == Store::PRICE_SCOPE_GLOBAL
            ? $this->scopeConfig->getValue(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE)
            : $this->scopeConfig->getValue('swarming_kount/account/currency');
    }

    /**
     * @param string|null $websiteCode
     * @return string
     */
    public function getMerchantNumber($websiteCode = null)
    {
        return $this->scopeConfig->getValue('swarming_kount/account/merchantnum', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
    }

    /**
     * @param string|null $websiteCode
     * @return string
     */
    public function getWebsite($websiteCode = null)
    {
        return $this->scopeConfig->getValue('swarming_kount/account/website', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
    }

    /**
     * @param string|null $websiteCode
     * @return string
     */
    public function getApiKey($websiteCode = null)
    {
        $env = $this->isTestMode($websiteCode) ? 'test' : 'production';
        return $this->scopeConfig->getValue("swarming_kount/account/api_key_{$env}", ScopeInterface::SCOPE_WEBSITE, $websiteCode);
    }

    /**
     * @return string
     */
    public function getConfigKey()
    {
        return $this->scopeConfig->getValue('swarming_kount/account/config_key');
    }

    /**
     * @param string|null $websiteCode
     * @return bool
     */
    public function isTestMode($websiteCode = null)
    {
        return $this->scopeConfig->isSetFlag('swarming_kount/account/test', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
    }

    /**
     * @param string|null $websiteCode
     * @return string
     */
    public function getAwcUrl($websiteCode = null)
    {
        return $this->scopeConfig->getValue('swarming_kount/account/awc_url' . $this->getModeSuffix($websiteCode));
    }

    /**
     * @param string|null $websiteCode
     * @return string
     */
    public function getRisUrl($websiteCode = null)
    {
        return $this->scopeConfig->getValue('swarming_kount/account/ris_url' . $this->getModeSuffix($websiteCode));
    }

    /**
     * @param string|null $websiteCode
     * @return string
     */
    public function getDataCollectorUrl($websiteCode = null)
    {
        return $this->scopeConfig->getValue('swarming_kount/account/data_collector_url' . $this->getModeSuffix($websiteCode));
    }

    /**
     * @return int
     */
    public function getDataCollectorWidth()
    {
        return (int)$this->scopeConfig->isSetFlag('swarming_kount/account/data_collector_width');
    }

    /**
     * @return int
     */
    public function getDataCollectorHeight()
    {
        return (int)$this->scopeConfig->isSetFlag('swarming_kount/account/data_collector_height');
    }

    /**
     * @param string|null $websiteCode
     * @return string
     */
    protected function getModeSuffix($websiteCode = null)
    {
        return $this->isTestMode($websiteCode) ? '_test_mode' : '';
    }
}
