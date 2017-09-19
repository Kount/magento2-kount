<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Controller\Ens;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AuthenticationException;

class Index extends Action
{
    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Swarming\Kount\Model\Config\Ens
     */
    protected $configEns;

    /**
     * @var \Swarming\Kount\Model\Ens\Manager
     */
    protected $ensManager;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Swarming\Kount\Model\Config\Account $configAccount
     * @param \Swarming\Kount\Model\Config\Ens $configEns
     * @param \Swarming\Kount\Model\Ens\Manager $ensManager
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swarming\Kount\Model\Config\Account $configAccount,
        \Swarming\Kount\Model\Config\Ens $configEns,
        \Swarming\Kount\Model\Ens\Manager $ensManager,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->configAccount = $configAccount;
        $this->configEns = $configEns;
        $this->ensManager = $ensManager;
        $this->remoteAddress = $remoteAddress;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            if (!$this->isAllowed()) {
                throw new AuthenticationException(__('Invalid ENS Ip Address.'));
            }

            $xmlString = file_get_contents('php://input');
            $response = $this->ensManager->handleRequest($xmlString);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->critical($e);
            $response = $this->ensManager->generateResponse(0, 1);
        }

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setHeader('Content-Type', 'text/xml');
        $resultRaw->setContents($response);
        return $resultRaw;
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->configAccount->isTestMode()
            ||
            $this->configEns->isAllowedIp($this->remoteAddress->getRemoteAddress());
    }
}
