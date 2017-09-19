<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PhoneToWeb
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
     * @param string $ips
     * @return array
     */
    protected function explodeIps($ips)
    {
        return empty($ips) ? [] : explode(',', str_replace(' ', '', $ips));
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag('swarming_kount/admin/phonetoweb_enabled');
    }

    /**
     * @param string|null $websiteCode
     * @return array
     */
    public function getIpAddresses($websiteCode = null)
    {
        $ipAddresses = $this->scopeConfig->getValue(
            'swarming_kount/admin/phonetoweb_ipaddresses',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteCode
        );
        return $this->explodeIps($ipAddresses);
    }

    /**
     * @param string $ipAddress
     * @return bool
     */
    public function isIp6($ipAddress)
    {
        return strlen($ipAddress) > 15;
    }

    /**
     * @param string $ipAddress
     * @param string|null $websiteCode
     * @return bool
     */
    public function isIpWhite($ipAddress, $websiteCode = null)
    {
        return $this->isEnabled() && in_array($ipAddress, $this->getIpAddresses($websiteCode), true);
    }
}
