<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris;

class Sender
{
    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Swarming\Kount\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Swarming\Kount\Model\Config\Account $configAccount
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swarming\Kount\Helper\Data $helperData
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Swarming\Kount\Model\Config\Account $configAccount,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swarming\Kount\Helper\Data $helperData,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->configAccount = $configAccount;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->logger = $logger;
    }

    /**
     * @param \Kount_Ris_Request $request
     * @return bool|\Kount_Ris_Response
     */
    public function send(\Kount_Ris_Request $request)
    {
        try {
            $response = $request->getResponse();
            if (!$response) {
                throw new \Exception('Invalid response from Kount RIS.');
            }

            $this->checkAndLogError($response);
            $this->checkAndLogWarnings($response);
            $this->checkAndLogErrorCode($response);
        } catch (\Exception $e) {
            $this->logger->error('Exception while making RIS request: ' . $e->getMessage());
            return false;
        }

        return $response;
    }

    /**
     * @param \Kount_Ris_Response $response
     * @return $this
     */
    protected function checkAndLogError($response)
    {
        $errors = $response->getErrors();
        foreach ($errors as $error) {
            $this->logger->error('RIS returned error: ' . $error);
        }
        return $this;
    }

    /**
     * @param \Kount_Ris_Response $response
     * @return $this
     */
    protected function checkAndLogErrorCode($response)
    {
        if ($response->getErrorCode() !== null) {
            $this->logger->warning('RIS returned error code: ' . $response->getErrorCode());
        }
    }

    /**
     * @param \Kount_Ris_Response $response
     * @return $this
     */
    protected function checkAndLogWarnings($response)
    {
        $warnings = $response->getWarnings();
        foreach ($warnings as $warning) {
            $this->logger->warning('RIS returned warning: ' . $warning);
        }
        return $this;
    }
}
