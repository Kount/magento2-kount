<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Order;

use Swarming\Kount\Api\Data\RisInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Ris
{
    const STATUS_KOUNT_REVIEW = 'review_kount';
    const STATUS_KOUNT_DECLINE = 'decline_kount';

    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * @var \Swarming\Kount\Api\Data\RisInterfaceFactory
     */
    protected $risFactory;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Swarming\Kount\Api\Data\RisInterfaceFactory $risFactory
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Swarming\Kount\Api\Data\RisInterfaceFactory $risFactory,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->risFactory = $risFactory;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Swarming\Kount\Api\Data\RisInterface
     */
    public function getRis(OrderInterface $order)
    {
        $extensionAttributes = $this->retrieveOrderExtensionAttributes($order);
        $ris = $extensionAttributes->getKountRis();
        if (empty($ris)) {
            $ris = $this->risFactory->create();
            $ris->load($order->getEntityId(), RisInterface::ORDER_ID);
            $extensionAttributes->setKountRis($ris);
        }
        return $ris;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderExtensionInterface
     */
    protected function retrieveOrderExtensionAttributes(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->orderExtensionFactory->create();
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @param \Swarming\Kount\Api\Data\RisInterface $ris
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function linkRis(RisInterface $ris, OrderInterface $order)
    {
        $ris->setOrderId($order->getEntityId());
        $ris->save();
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Kount_Ris_Response $response
     */
    public function updateRis(OrderInterface $order, \Kount_Ris_Response $response)
    {
        $ris = $this->getRis($order);

        $ris->setScore($response->getScore());
        $ris->setResponse($response->getAuto());
        $ris->setRule($this->getTriggeredRules($response));
        $ris->setDescription($response->getReasonCode());
        $ris->setTransactionId($response->getTransactionId());
        $ris->setGeox($response->getGeox());
        $ris->setCountry($response->getCountry());
        $ris->setKaptcha($response->getKaptcha());
        $ris->setCards($response->getCards());
        $ris->setEmails($response->getEmails());
        $ris->setDevices($response->getDevices());

        if ($order->getEntityId()) {
            $this->linkRis($ris, $order);
        }

        $this->logger->info('Setting RIS Response to order:');
        $this->logger->info('Response: ' . $ris->getResponse());
        $this->logger->info('Score: ' . $ris->getScore());
        $this->logger->info('Rules: ' . $ris->getRule());
        $this->logger->info('Description: ' . $ris->getDescription());
        $this->logger->info('TransactionId: ' . $ris->getTransactionId());
        $this->logger->info('Geox: ' . $ris->getGeox());
        $this->logger->info('Country: ' . $ris->getCountry());
        $this->logger->info('Kaptcha: ' . $ris->getKaptcha());
        $this->logger->info('Cards: ' . $ris->getCards());
        $this->logger->info('Emails: ' . $ris->getEmails());
        $this->logger->info('Devices: ' . $ris->getDevices());
    }

    /**
     * @param \Kount_Ris_Response $response
     * @return string
     */
    protected function getTriggeredRules(\Kount_Ris_Response $response)
    {
        $rules = '';
        if ($response->getNumberRulesTriggered() > 0) {
            foreach ($response->getRulesTriggered() as $curRuleId => $curRuleDesc) {
                $rules .= 'Rule ID ' . $curRuleId . ': ' . $curRuleDesc . "\n";
            }
        } else {
            $rules = 'No Rules';
        }
        return $rules;
    }
}
