<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Model;

class Customer2FA
{
    /**
     * @var \Kount\Kount\Model\Config\Account
     */
    private $kountConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Kount\KountControl\Model\ControlApi\Event
     */
    private $eventService;

    /**
     * @var \Kount\KountControl\Model\ControlApi\TrustedDevice
     */
    private $trustedDeviceService;

    /**
     * @var \Kount\KountControl\Helper\Config
     */
    private $kountControlConfig;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Kount\Kount\Model\Config\Account $kountConfig
     * @param \Kount\KountControl\Model\ControlApi\Event $eventService
     * @param \Kount\KountControl\Model\ControlApi\TrustedDevice $trustedDeviceService
     * @param \Kount\KountControl\Helper\Config $kountControlConfig
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Kount\Kount\Model\Config\Account $kountConfig,
        \Kount\KountControl\Model\ControlApi\Event $eventService,
        \Kount\KountControl\Model\ControlApi\TrustedDevice $trustedDeviceService,
        \Kount\KountControl\Helper\Config $kountControlConfig
    ) {
        $this->customerSession = $customerSession;
        $this->kountConfig = $kountConfig;
        $this->eventService = $eventService;
        $this->trustedDeviceService = $trustedDeviceService;
        $this->kountControlConfig = $kountControlConfig;
    }

    /**
     * Goes through KountControl diagram and initiate API requests to KountControl
     *
     * @param $sessionId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Kount\KountControl\Exception\ConfigException
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     * @throws \Kount\KountControl\Exception\ParamsException
     * @throws \Kount\KountControl\Exception\PositiveApiResponse
     */
    public function twoFactorAuthenticate($result)
    {
        if (!$this->kountControlConfig->isTrustedDeviceEnabled()) {
            throw new \Kount\KountControl\Exception\ConfigException(__('KountControl: Login service disabled'));
        }

        $userId = $this->customerSession->getCustomerId();
        $clientId = $this->kountConfig->getMerchantNumber();
        $sessionId = $this->customerSession->getKountSessionId();
        if ($sessionId === '' || $userId === '' || $this->kountConfig->getMerchantNumber() === '') {
            throw new \Kount\KountControl\Exception\ParamsException(__('KountControl: lost POST params. '
                . '$sessionId = "%1"; $userId = "%2"; $clientId', $sessionId, $userId, $clientId));
        }

        $loginResult = $this->customerSession->getLoginResult();
        $this->eventService->setLoginResult($loginResult);
        $this->trustedDeviceService->setDeviceId($loginResult['deviceId']);
        if ($result === 1) {
            $this->eventService->successApiCall($sessionId, $clientId);
            $this->trustedDeviceService->trustedApiCall($sessionId, $clientId);
            throw new \Kount\KountControl\Exception\PositiveApiResponse(__(
                'KountControl: Kount2FA decision is TRUSTED'
            ));
        } else {
            $this->eventService->failedApiCall($sessionId, $clientId);
            $this->trustedDeviceService->bannedApiCall($sessionId, $clientId);
            throw new \Kount\KountControl\Exception\NegativeApiResponse(__(
                'KountControl: Kount2FA decision is BANNED'
            ));
        }
    }
}
