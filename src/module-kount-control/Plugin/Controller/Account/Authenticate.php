<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Plugin\Controller\Account;

class Authenticate
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
    public function afterExecute(\Kount\Kount2FA\Controller\Account\Authenticate $subject)
    {
        if ($subject->getRequest()->getPostValue()) {
            $this->customer2FA->twoFactorAuthenticate();
        }
    }
}
