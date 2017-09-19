<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Block\Checkout;

class DataCollector extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swarming\Kount\Model\Config\Account $configAccount
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swarming\Kount\Model\Config\Account $configAccount,
        array $data = []
    ) {
        $this->configAccount = $configAccount;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->configAccount->isEnabled();
    }

    /**
     * @return bool
     */
    public function isTestMode()
    {
        return $this->configAccount->isTestMode();
    }

    /**
     * @return string
     */
    public function getIFrameUrl()
    {
        return $this->getUrl(
            'swarming_kount/datacollector/iframe',
            ['_forced_secure' => $this->_storeManager->getStore()->isCurrentlySecure()]
        );
    }

    /**
     * @return string
     */
    public function getGifUrl()
    {
        return $this->getUrl(
            'swarming_kount/datacollector/gif',
            ['_forced_secure' => $this->_storeManager->getStore()->isCurrentlySecure()]
        );
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->configAccount->getDataCollectorWidth();
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->configAccount->getDataCollectorHeight();
    }
}
