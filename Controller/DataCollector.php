<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

abstract class DataCollector extends Action
{
    /**
     * @var \Swarming\Kount\Model\Session
     */
    protected $kountSession;

    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Swarming\Kount\Model\Config\PhoneToWeb
     */
    protected $configPhoneToWeb;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Swarming\Kount\Model\Session $kountSession
     * @param \Swarming\Kount\Model\Config\Account $configAccount
     * @param \Swarming\Kount\Model\Config\PhoneToWeb $configPhoneToWeb
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swarming\Kount\Model\Session $kountSession,
        \Swarming\Kount\Model\Config\Account $configAccount,
        \Swarming\Kount\Model\Config\PhoneToWeb $configPhoneToWeb,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->kountSession = $kountSession;
        $this->configAccount = $configAccount;
        $this->configPhoneToWeb = $configPhoneToWeb;
        $this->remoteAddress = $remoteAddress;
        $this->logger = $logger;
        parent::__construct($context);
    }


    /**
     * @return bool
     */
    protected function isAvailable()
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
    protected function isIpWhite()
    {
        $ipAddress = $this->remoteAddress->getRemoteAddress();
        if ($this->configPhoneToWeb->isIpWhite($ipAddress)) {
            $this->logger->info("IP Address {$ipAddress} in white-listed, bypassing Data Collector.");
            return true;
        }
        return false;
    }

    /**
     * @param string $mode
     * @return string
     */
    protected function generateUrl($mode)
    {
        return sprintf(
            '%s/logo.%s?m=%s&s=%s',
            $this->configAccount->getDataCollectorUrl(),
            $mode,
            $this->configAccount->getMerchantNumber(),
            $this->kountSession->getKountSessionId()
        );
    }
}
