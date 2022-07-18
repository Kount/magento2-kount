<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Model\ControlApi;

use Kount\KountControl\Api\ServiceInterface;

abstract class AbstractService
{
    const UNKNOW_DECISION = 'unknow';

    /**
     * @var \GuzzleHttp\Client
     */
    public $client;

    /**
     * @var \Kount\KountControl\Helper\Config
     */
    public $config;

    /**
     * @var \Kount\Kount\Model\Logger
     */
    public $logger;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    public $encryptor;

    /**
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    public $customerUrl;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public $remoteAddress;

    /**
     * @return string
     */
    abstract public function getEndpoint();

    /**
     * Prepare data for request
     *
     * @param string $sessionId
     * @param string $clientId
     * @return array
     */
    abstract public function preparePayload($sessionId, $clientId);

    /**
     * @param \GuzzleHttp\Client $client
     * @param \Kount\KountControl\Helper\Config $config
     * @param \Kount\Kount\Model\Logger $logger
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        \GuzzleHttp\Client $client,
        \Kount\KountControl\Helper\Config $config,
        \Kount\Kount\Model\Logger $logger,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->client = $client;
        $this->config = $config;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * Return API URI depending on the request type
     *
     * @return string
     */
    public function getUri()
    {
        $baseUri = $this->config->isTestMode()
            ? ServiceInterface::BASE_SANDBOX_URI
            : ServiceInterface::BASE_PRODUCTION_URI;
        return $baseUri . $this->getEndpoint();
    }

    /**
     * Execute API request to KOUNT
     *
     * @param $sessionId
     * @param $clientId
     * @return array
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     */
    public function executeApiRequest($sessionId, $clientId)
    {
        $payload = $this->preparePayload($sessionId, $clientId);
        $response = $this->postData($payload);
        return $this->processResult($response);
    }

    /**
     * Parse API response
     *
     * @param $response
     * @return mixed
     */
    public function processResult($response)
    {
        $responseContent = json_decode($response->getBody()->getContents(), true);
        $this->logger->info(__('KountControl: POST response from ' . $this->getUri() . ' has body '
            . json_encode($responseContent)));

        if (isset($responseContent['decision'])) {
            $decision = $responseContent['decision'];
        } else {
            $decision = self::UNKNOW_DECISION;
        }
        $result['decision'] = $decision;

        if (isset($response->getHeaders()['X-Correlation-Id'][0])) {
            $result['loginDecisionCorrelationId'] = $response->getHeaders()['X-Correlation-Id'][0];
        }

        if (isset($responseContent['deviceId'])) {
            $result['deviceId'] = $responseContent['deviceId'];
        }

        return $result;
    }

    /**
     * Post request
     *
     * @param array $payload
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Kount\KountControl\Exception\NegativeApiResponse
     */
    public function postData(array $payload)
    {
        $response = null;
        $logInfo = json_encode($payload);
        $payload = $this->addAuthToPayload($payload);

        try {
            $response = $this->client->request('post', $this->getUri(), $payload);
            $this->logger->info(__('KountControl: POST response from ' . $this->getUri() . ' got '
                . $response->getStatusCode() . ' status code.'));
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            if ($e->getResponse()->getStatusCode() && $e->getMessage()) {
                $this->logger->error(__('KountControl: POST response from ' . $this->getUri() . ' got '
                    . $e->getResponse()->getStatusCode() . ' status code '
                    . '. Payload: ' . $logInfo
                    . '. Error message: ' . $e->getMessage()));
            }
        }

        if ($response instanceof \Psr\Http\Message\ResponseInterface) {
            return $response;
        } else {
            throw new \Kount\KountControl\Exception\NegativeApiResponse(__(
                'KountControl: API call to "%1" did not return success response. Payload: "%2"',
                $this->getUri(),
                json_encode($payload)
            ));
        }
    }

    /**
     * Add authentication to header
     *
     * @param $payload
     * @return array
     */
    public function addAuthToPayload($payload)
    {
        $apiKey = $this->getApiKey();
        $payload = array_merge(
            $payload,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ]
            ]
        );

        return $payload;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        $encryptedApiKey = $this->config->getControlApiKey();
        return $this->encryptor->decrypt($encryptedApiKey);
    }
}
