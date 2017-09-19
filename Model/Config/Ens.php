<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Ens
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
     * @return array
     */
    public function getKountIps()
    {
        $ips = $this->scopeConfig->getValue('swarming_kount/ens/kount_ips');
        return $this->explodeIps($ips);
    }

    /**
     * @param string|null $websiteCode
     * @return array
     */
    public function getAdditionIps($websiteCode = null)
    {
        $ips = $this->scopeConfig->getValue('swarming_kount/ens/addition_ips', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
        return $this->explodeIps($ips);
    }

    /**
     * @param string|null $websiteCode
     * @return array
     */
    public function getAllowedIps($websiteCode = null)
    {
        return array_merge($this->getKountIps(), $this->getAdditionIps($websiteCode));
    }

    /**
     * @param string $ip
     * @param string|null $websiteCode
     * @return bool
     */
    public function isAllowedIp($ip, $websiteCode = null)
    {
        return in_array($ip, $this->getAllowedIps($websiteCode), true);
    }
}
