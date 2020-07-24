<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ens\EventHandler;

class EventHandlerOrder
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param \Magento\Framework\Simplexml\Element $event
     * @return array
     */
    public function fetchVars($event)
    {
        $eventData = $event->asArray();
        return [
            (empty($eventData['key'][0]) ? '' : $eventData['key'][0]),
            (empty($eventData['key']['@']['order_number']) ? '' : $eventData['key']['@']['order_number']),
            (empty($eventData['old_value']) ? '' : $eventData['old_value']),
            (empty($eventData['new_value']) ? '' : $eventData['new_value'])
        ];
    }

    /**
     * @param string $orderId
     * @return \Magento\Sales\Model\Order
     *
     * @throws \InvalidArgumentException
     */
    public function loadOrder($orderId)
    {
        if (empty($orderId)) {
            throw new \InvalidArgumentException('Invalid Order number.');
        }

        $order = $this->orderFactory->create()->loadByIncrementId($orderId);

        if (!$order->getId()) {
            throw new \InvalidArgumentException("Unable to locate order for: {$orderId}");
        }
        return $order;
    }
}
