<?php
/**
 * Copyright (c) 2023 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Model;

class CustomerLogin
{
    const SUCCESS_CHALLENGE = "Success";

    /**
     * @var \Kount\Kount\Model\Config\Account
     */
    private $kountConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Kount\KountControl\Model\ControlApi\Login
     */
    private $loginService;

    /**
     * @var \Kount\KountControl\Model\ControlApi\Event
     */
    private $eventService;

    /**
     * @var \Kount\KountControl\Helper\Config
     */
    private $kountControlConfig;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Kount\Kount\Model\Config\Account $kountConfig
     * @param \Kount\KountControl\Model\ControlApi\Login $loginService
     * @param \Kount\KountControl\Model\ControlApi\Event $eventService
     * @param \Kount\KountControl\Helper\Config $kountControlConfig
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Kount\Kount\Model\Config\Account $kountConfig,
        \Kount\KountControl\Model\ControlApi\Login $loginService,
        \Kount\KountControl\Model\ControlApi\Event $eventService,
        \Kount\KountControl\Helper\Config $kountControlConfig
    ) {
        $this->customerSession = $customerSession;
        $this->kountConfig = $kountConfig;
        $this->loginService = $loginService;
        $this->eventService = $eventService;
        $this->kountControlConfig = $kountControlConfig;
    }

    /**
     * Goes through KountControl diagram and initiate API requests to KountControl
     *
     * @param $sessionId
     * @throws \Kount\KountControl\Exception\ChallengeApiResponse
     * @throws \Kount\KountControl\Exception\ConfigException
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     * @throws \Kount\KountControl\Exception\ParamsException
     */
    public function login($sessionId)
    {
        if (!$this->kountControlConfig->isLoginServiceEnabled()) {
            throw new \Kount\KountControl\Exception\ConfigException(__('KountControl: Login service disabled'));
        }

        $userId = $this->customerSession->getCustomerId();
        $clientId = $this->kountConfig->getMerchantNumber();
        if ($sessionId === '' || $userId === '' || $this->kountConfig->getMerchantNumber() === '') {
            throw new \Kount\KountControl\Exception\ParamsException(__('KountControl: lost POST params. '
                . '$sessionId = "%1"; $userId = "%2"; $clientId', $sessionId, $userId, $clientId));
        }

        $loginResult = $this->loginService->executeApiRequest($sessionId, $clientId);
        $this->customerSession->setLoginResult($loginResult);
        if ($loginResult['decision'] === \Kount\KountControl\Model\ControlApi\Login::BLOCK_DECISION) {
            $this->eventService->setLoginResult($loginResult);
            $this->eventService->failedApiCall($sessionId, $clientId);
            throw new \Kount\KountControl\Exception\NegativeApiResponse(__(
                'KountControl: API Login decision is "%1"',
                $loginResult['decision']
            ));
        }

        if ($loginResult['decision'] === \Kount\KountControl\Model\ControlApi\Login::CHALLENGE_DECISION) {
            if (!isset($loginResult['deviceId'])) {
                throw new \Kount\KountControl\Exception\ParamsException(__(
                    'KountControl: lost POST params. API login decision is "%1". $deviceId is not set.',
                    $loginResult['decision']
                ));
            }
            throw new \Kount\KountControl\Exception\ChallengeApiResponse(__(
                'KountControl: API login decision is "%1".',
                $loginResult['decision']
            ));
        }
    }
}
