<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Plugin\Controller\Account;

class Setup
{
    /**
     * @var \Kount\Kount\Model\Logger
     */
    private $logger;

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
     * @param \Kount\KountControl\Model\Customer2FA $customer2FA
     * @param LoginPost $loginPost
     */
    public function __construct(
        \Kount\Kount\Model\Logger $logger,
        \Kount\KountControl\Model\Customer2FA $customer2FA,
        \Kount\KountControl\Plugin\Controller\Account\LoginPost $loginPost
    ) {
        $this->logger = $logger;
        $this->customer2FA = $customer2FA;
        $this->loginPost = $loginPost;
    }

    /**
     * Checks if 2FA succeeded after /setup or /authenticate controller's call of Kount2FA module
     */
    public function afterExecute(\Kount\Kount2FA\Controller\Account\Setup $subject)
    {
        if ($subject->getRequest()->getPostValue()) {
            try {
                $this->customer2FA->twoFactorAuthenticate(true);
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
}
