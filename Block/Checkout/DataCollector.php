<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Block\Checkout;

class DataCollector extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Swarming\Kount\Model\Session
     */
    private $kountSession;

    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    private $configAccount;

    /**
     * @var \Swarming\Kount\Model\Config\PhoneToWeb
     */
    private $configPhoneToWeb;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swarming\Kount\Model\Session $kountSession
     * @param \Swarming\Kount\Model\Config\Account $configAccount
     * @param \Swarming\Kount\Model\Config\PhoneToWeb $configPhoneToWeb
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Swarming\Kount\Model\Logger $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swarming\Kount\Model\Session $kountSession,
        \Swarming\Kount\Model\Config\Account $configAccount,
        \Swarming\Kount\Model\Config\PhoneToWeb $configPhoneToWeb,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Swarming\Kount\Model\Logger $logger,
        array $data = []
    ) {
        $this->kountSession = $kountSession;
        $this->configAccount = $configAccount;
        $this->configPhoneToWeb = $configPhoneToWeb;
        $this->remoteAddress = $remoteAddress;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {
        $this->logger->info('Data collector block.');

        $this->kountSession->incrementKountSessionId();

        $this->logger->info('Setting Kount session ID: ' . $this->kountSession->getKountSessionId());

        return parent::_prepareLayout();
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
    public function getJsDataCollectorUrl()
    {
        return $this->generateJsUrl();
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

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->isAvailable() && !$this->isIpWhite()
            ? parent::_toHtml()
            : '';
    }

    /**
     * @return bool
     */
    private function isAvailable()
    {
        if (!$this->configAccount->isEnabled()) {
            $this->logger->info('Kount extension is disabled in system configuration, skipping action.');
            return false;
        }

        if (!$this->configAccount->isAvailable()) {
            $this->logger->info('Kount is not configured, skipping action.');
            return false;
        }
        return true;
    }

    /**
     * Checking whether the current IP in whitelist
     *
     * @return bool
     */
    private function isIpWhite()
    {
        $ipAddress = $this->remoteAddress->getRemoteAddress();
        if ($this->configPhoneToWeb->isIpWhite($ipAddress)) {
            $this->logger->info("IP Address {$ipAddress} in white-listed, bypassing Data Collector.");
            return true;
        }
        return false;
    }
    
    /**
     * @return string
     */
    private function generateJsUrl()
    {
        return sprintf(
            '%s/collect/sdk?m=%s&s=%s',
            $this->configAccount->getDataCollectorUrl(),
            $this->configAccount->getMerchantNumber(),
            $this->kountSession->getKountSessionId()
        );
    }
}
