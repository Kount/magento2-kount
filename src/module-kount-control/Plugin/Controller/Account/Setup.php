<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
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
     * @param \Kount\Kount\Model\Logger $logger
     * @param \Kount\KountControl\Model\Customer2FA $customer2FA
     */
    public function __construct(
        \Kount\Kount\Model\Logger $logger,
        \Kount\KountControl\Model\Customer2FA $customer2FA
    ) {
        $this->logger = $logger;
        $this->customer2FA = $customer2FA;
    }

    /**
     * Checks if 2FA succeeded after /setup or /authenticate controller's call of Kount2FA module
     *
     * @param \Kount\Kount2FA\Controller\Account\Setup $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Kount\Kount2FA\Controller\Account\Setup $subject, $result)
    {
        if ($subject->getRequest()->getPostValue()) {
           $this->customer2FA->twoFactorAuthenticate();
        }

        return $result;
    }
}
