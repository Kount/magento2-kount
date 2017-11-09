<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Workflow
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
     * @param string|null $storeCode
     * @return string
     */
    public function getWorkflowMode($storeCode = null)
    {
        return $this->scopeConfig->getValue('swarming_kount/workflow/workflow_mode', ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * @param string|null $storeCode
     * @return string
     */
    public function getDeclineAction($storeCode = null)
    {
        return $this->scopeConfig->getValue('swarming_kount/workflow/decline_action', ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * @param string|null $storeCode
     * @return bool
     */
    public function isNotifyProcessorDecline($storeCode = null)
    {
        return $this->scopeConfig->isSetFlag('swarming_kount/workflow/notify_processor_decline', ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * @param string|null $storeCode
     * @return bool
     */
    public function isPreventResettingOrderStatus($storeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            'swarming_kount/workflow/prevent_resetting_order_status',
            ScopeInterface::SCOPE_STORE,
            $storeCode
        );
    }
}
