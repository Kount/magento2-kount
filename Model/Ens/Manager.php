<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ens;

class Manager
{
    /**
     * @var \Kount\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Kount\Kount\Model\Ens\EventHandlerFactory
     */
    protected $eventHandlerFactory;

    /**
     * @var \Kount\Kount\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Kount\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $supportedEvents;

    /**
     * @param \Kount\Kount\Model\Config\Account $configAccount
     * @param \Kount\Kount\Model\Ens\EventHandlerFactory $eventHandlerFactory
     * @param \Kount\Kount\Helper\Data $helperData
     * @param \Kount\Kount\Model\Logger $logger
     * @param array $supportedEvents
     */
    public function __construct(
        \Kount\Kount\Model\Config\Account $configAccount,
        \Kount\Kount\Model\Ens\EventHandlerFactory $eventHandlerFactory,
        \Kount\Kount\Helper\Data $helperData,
        \Kount\Kount\Model\Logger $logger,
        array $supportedEvents
    ) {
        $this->configAccount = $configAccount;
        $this->eventHandlerFactory = $eventHandlerFactory;
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->supportedEvents = $supportedEvents;
    }

    /**
     * @param string $xmlString
     * @return string
     */
    public function handleRequest($xmlString)
    {
        $xml = $this->asXmlObject($xmlString);
        if (!$xml instanceof \Magento\Framework\Simplexml\Element) {
            throw new \InvalidArgumentException('Invalid Xml request.');
        }

        if ($xml->getAttribute('merchant') != $this->configAccount->getMerchantNumber()) {
            throw new \InvalidArgumentException('Invalid Merchant Id in event Xml.');
        }

        $this->logger->info('Kount extension version: ' . $this->helperData->getModuleVersion());

        $successes = $failures = 0;

        foreach ($xml->children() as $event) {
            try {
                if (!$this->validateWebsiteCode($event)) {
                    $successes++;
                    $this->logger->info("Site code does't match, ignored.");
                    continue;
                }

                $this->handleEvent($event);
                $successes++;
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $failures++;
            }
        }

        return $this->generateResponse($successes, $failures);
    }

    /**
     * @param int $successes
     * @param int $failures
     * @return string
     */
    public function generateResponse($successes, $failures)
    {
        $response = $this->createResponse();
        $response->addAttribute('successes', $successes);
        $response->addAttribute('failures', $failures);
        return $response->asXML();
    }

    /**
     * @param \Magento\Framework\Simplexml\Element $event
     * @return bool
     */
    protected function validateWebsiteCode($event)
    {
        return $event->descend('key')
            && $event->descend('key')->getAttribute('site') === $this->configAccount->getWebsite();
    }

    /**
     * @param \Magento\Framework\Simplexml\Element $event
     */
    protected function handleEvent($event)
    {
        $eventName = (string)$event->descend('name');
        if (empty($eventName)) {
            throw new \InvalidArgumentException('Invalid Event name.');
        }

        if (!in_array($eventName, $this->supportedEvents, true)) {
            $this->logger->info("DMC event {$eventName} received, ignored.");
            return;
        }

        $eventHandler = $this->eventHandlerFactory->create($eventName);
        $eventHandler->process($event);
    }

    /**
     * @param string $xmlString
     * @return \Magento\Framework\Simplexml\Element
     */
    protected function asXmlObject($xmlString)
    {
        return simplexml_load_string($xmlString, \Magento\Framework\Simplexml\Element::class);
    }

    /**
     * @return \Magento\Framework\Simplexml\Element
     */
    protected function createResponse()
    {
        return simplexml_load_string('<eventResponse/>', \Magento\Framework\Simplexml\Element::class);
    }
}
