<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Log
{
    const FILENAME = 'kount.log';

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
        return $this->scopeConfig->isSetFlag('kount/log/enabled', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
    }

    /**
     * @return bool
     */
    public function isRisMetricsEnabled()
    {
        return $this->scopeConfig->isSetFlag('kount/log/ris_metrics_enabled');
    }
}
