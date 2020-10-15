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
     *
     * @param $ip
     * @param array $ips
     * @return bool
     */
    private function isInCidrRange($ip, array $ips) : bool
    {
        $cidrRanges = array_filter(
            $ips,
            function ($ipAddress) {
                return strpos($ipAddress, '/') !== false;
            }
        );

        if (empty($cidrRanges)) {
            return false;
        }

        $longIp = ip2long($ip);
        if (!$longIp) {
            return false;
        }

        foreach ($cidrRanges as $cidrIp) {
            $cidr = explode('/', $cidrIp);
            $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
            $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
            $rangeStart = ip2long($range[0]);
            $rangeEnd = ip2long($range[1]);
            if ($rangeStart <= $longIp && $longIp <= $rangeEnd) {
                return true;
            }
        }

        return false;
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
        $allowedIps = $this->getAllowedIps($websiteCode);
        return in_array($ip, $allowedIps, true) || $this->isInCidrRange($ip, $allowedIps) ;
    }

    /**
     * @param string|null $websiteCode
     * @return bool
     */
    public function isEnabled($websiteCode = null)
    {
        return (bool)$this->scopeConfig->getValue('swarming_kount/ens/enabled', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
    }
}
