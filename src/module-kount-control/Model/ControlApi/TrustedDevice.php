<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Model\ControlApi;

use Kount\KountControl\Api\ServiceInterface;

class TrustedDevice extends AbstractService implements ServiceInterface
{
    const ENDPOINT_URI = '/trusted-device';
    const TRUSTED_DEVICE = "TRUSTED";
    const BANNED_DEVICE = "BANNED";

    /**
     * @var string
     */
    private $deviceId;

    /**
     * @var string
     */
    private $trustState;

    /**
     * @inheritdoc
     */
    public function preparePayload($sessionId, $clientId)
    {
        $userId = $this->customerSession->getCustomerId();

        return [
            'body' => json_encode(
                [
                    'clientId' => $clientId,
                    'sessionId' => $sessionId,
                    'userId' => $userId,
                    'trustState' => $this->trustState
                ]
            )
        ];
    }

    /**
     * @param $sessionId
     * @param $clientId
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     */
    public function trustedApiCall($sessionId, $clientId)
    {
        $this->trustState = self::TRUSTED_DEVICE;
        $this->executeApiRequest($sessionId, $clientId);
    }

    /**
     * @param $sessionId
     * @param $clientId
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     */
    public function bannedApiCall($sessionId, $clientId)
    {
        $this->trustState = self::BANNED_DEVICE;
        $this->executeApiRequest($sessionId, $clientId);
    }

    /**
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param $deviceId
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @inheritdoc
     * @throws \Kount\KountControl\Exception\ParamsException
     */
    public function postData(array $payload)
    {
        $response = null;
        $deviceId = $this->getDeviceId();
        $apiKey = $this->getApiKey();
        $payload = $this->addAuthToPayload($payload);
        $payloadArray = json_decode($payload['body'], true);
        if (
            isset($payloadArray['clientId'])
            && isset($payloadArray['sessionId'])
            && isset($payloadArray['userId'])
        ) {
            $clientId = $payloadArray['clientId'];
            $sessionId = $payloadArray['sessionId'];
            $userId = $payloadArray['userId'];
        } else {
            throw new \Kount\KountControl\Exception\ParamsException(__(
                'KountControl: lost GET params. $customerId, $sessionId or $userId not set'
            ));
        }

        // Check whether exists trusted Device record with this device ID for merchant with this client ID
        $responseBody = '';
        try {
            $response = $this->client->request(
                'get',
                'https://api-sandbox.kount.com/trusted-device/sessions/' . $sessionId
                    . '/users/' . $userId . '/clients/' . $clientId,
                ['headers' => ['Authorization' => 'Bearer ' . $apiKey]]
            );
            $responseBody = $response->getBody()->getContents();

            $this->logger->info(__('KountControl: GET response from ' . $this->getUri() . ' got '
                . $response->getStatusCode() . ' status code and body: ' . $responseBody));
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $this->handleException($e, 'KountControl: Response from ', $payload);
        }

        // If this Trusted Device record exists, sends update request
        if ($this->isExistTrustedDevice($deviceId, $responseBody)) {
            try {
                $response = $this->client->request('put', $this->getUri(), $payload);
                $responseBody = $response->getBody()->getContents();
                $this->logger->info(__('KountControl: PUT response from ' . $this->getUri() . ' got '
                    . $response->getStatusCode() . ' status code and body: ' . $responseBody));
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                $this->handleException($e, 'KountControl: PUT response from ', $payload);
            }
        } else {
            // Otherwise sends create request
            try {
                $response = $this->client->request('post', $this->getUri(), $payload);
                $responseBody = $response->getBody()->getContents();
                $this->logger->info(__('KountControl: POST response from ' . $this->getUri() . ' got '
                    . $response->getStatusCode() . ' status code and body: ' . $responseBody));
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                $this->handleException($e, 'KountControl: POST response from ', $payload);
            }
        }

        // If response has invalid type, throw an exception
        if (!$response instanceof \Psr\Http\Message\ResponseInterface) {
            throw new \Kount\KountControl\Exception\NegativeApiResponse(__(
                'KountControl: API call to "%1" did not return success response',
                $this->getUri()
            ));
        }

        return $response;
    }

    /**
     * Build exception message
     *
     * @param $e
     * @param $startOfMessage
     * @param $payload
     * @return void
     */
    private function handleException($e, $startOfMessage, $payload)
    {
        if ($e->getResponse()->getStatusCode() && $e->getMessage()) {
            $this->logger->error(__($startOfMessage . $this->getUri() . ' got '
                . $e->getResponse()->getStatusCode() . ' status code '
                . '. Payload: ' . json_encode($payload)
                . '. Error message: ' . $e->getMessage()));
        }
    }

    /**
     * Check whether contains trusted device ID in response
     *
     * @param $deviceId
     * @param $responseBody
     * @return bool
     */
    private function isExistTrustedDevice($deviceId, $responseBody)
    {
        $responseContent = json_decode($responseBody, true);
        if (isset($responseContent['deviceId']) && $responseContent['deviceId'] === $deviceId) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        return self::ENDPOINT_URI;
    }
}
