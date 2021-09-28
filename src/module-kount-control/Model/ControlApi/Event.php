<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Model\ControlApi;

use Kount\KountControl\Api\ServiceInterface;

class Event extends AbstractService implements ServiceInterface
{
    const ENDPOINT_URI = '/events';
    const SUCCESS_2FA_RESULT = "SUCCESS";
    const FAILED_2FA_RESULT = "FAILED";

    /**
     * @var array
     */
    private $loginResult;

    /**
     * @var string
     */
    private $tfaAuthenticationResult;

    /**
     * @inheritdoc
     */
    public function preparePayload($sessionId, $clientId)
    {
        $userId = $this->customerSession->getCustomerId();
        if ($this->tfaAuthenticationResult === self::SUCCESS_2FA_RESULT) {
            return $this->getSuccessEventPayload($sessionId, $userId, $clientId, $this->getLoginResult());
        }

        return $this->getFailedEventPayload($sessionId, $userId, $clientId);
    }

    /**
     * @param $sessionId
     * @param $clientId
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     */
    public function successApiCall($sessionId, $clientId)
    {
        $this->tfaAuthenticationResult = self::SUCCESS_2FA_RESULT;
        $this->executeApiRequest($sessionId, $clientId);
    }

    /**
     * @param $sessionId
     * @param $clientId
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     */
    public function failedApiCall($sessionId, $clientId)
    {
        $this->tfaAuthenticationResult = self::FAILED_2FA_RESULT;
        $this->executeApiRequest($sessionId, $clientId);
    }

    /**
     * @param $sessionId
     * @param $userId
     * @param $clientId
     * @param $loginResult
     * @return array
     */
    private function getSuccessEventPayload($sessionId, $userId, $clientId, $loginResult)
    {
        $loginDecisionCorrelationId = isset($loginResult['loginDecisionCorrelationId'])
            ? $loginResult['loginDecisionCorrelationId']
            : '';

        return [
            'body' => json_encode(
                [
                    'challengeOutcome' => [
                        'clientId' => $clientId,
                        'loginDecisionCorrelationId' => $loginDecisionCorrelationId,
                        'challengeType' => 'Captcha',
                        'challengeStatus' => \Kount\KountControl\Model\CustomerLogin::SUCCESS_CHALLENGE,
                        'sessionId' => $sessionId,
                        'userId' => $userId,
                        'sentTimestamp' => '',
                        'completedTimestamp' => '',
                        'failureType' => 'TimedOut'
                    ]
                ]
            )
        ];
    }

    /**
     * @param $sessionId
     * @param $userId
     * @param $clientId
     * @return array
     */
    private function getFailedEventPayload($sessionId, $userId, $clientId)
    {
        return [
            'body' => json_encode(
                [
                    'failedAttempt' => [
                        'clientId' => $clientId,
                        'sessionId' => $sessionId,
                        'userId' => $userId,
                        'username' => $this->customerSession->getCustomer()->getFirstname()
                            . ' ' . $this->customerSession->getCustomer()->getLastname(),
                        'userPassword' => $this->customerSession->getCustomer()->getPasswordHash(),
                        'userIp' => $this->remoteAddress->getRemoteAddress(),
                        'loginUrl' => $this->customerUrl->getLoginUrl()
                    ]
                ]
            )
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return self::ENDPOINT_URI;
    }

    /**
     * @return array
     */
    public function getLoginResult()
    {
        return $this->loginResult;
    }

    /**
     * @param $loginResult
     */
    public function setLoginResult($loginResult)
    {
        $this->loginResult = $loginResult;
    }
}
