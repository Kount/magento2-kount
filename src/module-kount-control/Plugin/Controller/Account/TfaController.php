<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Plugin\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Redirect;

class TfaController
{
    /**
     * @var \Kount\Kount\Model\Logger
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Kount\KountControl\Helper\Config
     */
    private $kountControlConfig;

    /**
     * @var \Kount\KountControl\Model\Customer2FA
     */
    private $customer2FA;

    /**
     * @var LoginPost
     */
    private $loginPost;

    /**
     * @param \Kount\Kount\Model\Logger $logger
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Kount\KountControl\Helper\Config $kountControlConfig
     * @param \Kount\KountControl\Model\Customer2FA $customer2FA
     * @param LoginPost $loginPost
     */
    public function __construct(
        \Kount\Kount\Model\Logger $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Kount\KountControl\Helper\Config $kountControlConfig,
        \Kount\KountControl\Model\Customer2FA $customer2FA,
        \Kount\KountControl\Plugin\Controller\Account\LoginPost $loginPost
    ) {
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->kountControlConfig = $kountControlConfig;
        $this->customer2FA = $customer2FA;
        $this->loginPost = $loginPost;
    }

    /**
     * Checks if 2FA succeeded after /setup or /authenticate controller's call of Kount2FA module
     */
    public function afterExecute()
    {
        try {
            if ($this->customerSession->get2faSuccessful()) {
                $this->customer2FA->twoFactorAuthenticate(1);
            } elseif ($this->customerSession->get2faAttemptCount()
                >= $this->kountControlConfig->get2faFailedAttemptsAmount()) {
                $this->customer2FA->twoFactorAuthenticate(0);
            }
        } catch (
            \Kount\KountControl\Exception\ConfigException
            | \Kount\KountControl\Exception\PositiveApiResponse $e
        ) {
            $this->logger->info($e->getMessage());
        } catch (
            \Kount\KountControl\Exception\ParamsException
            | \Kount\KountControl\Exception\NegativeApiResponse $e
        ) {
            $this->loginPost->logoutCustomer();
            $this->logger->warning($e->getMessage());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error(__('KountControl: ' . $e->getMessage()));
        }
    }
}
