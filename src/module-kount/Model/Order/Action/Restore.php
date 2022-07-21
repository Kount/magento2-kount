<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Order\Action;

use Kount\Kount\Model\Order\ActionInterface;

class Restore implements ActionInterface
{
    /**
     * @var \Kount\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Kount\Kount\Model\Logger $logger
     */
    public function __construct(
        \Kount\Kount\Model\Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function process($order)
    {
        $orderState = $order->getHoldBeforeState();
        $orderStatus = $order->getHoldBeforeStatus();
        if (!$orderState || !$orderStatus) {
            $this->logger->info('Restore order status/state by ENS Kount request - incomplete data, skipping');
            return;
        }

        $this->logger->info('Restore order status/state by ENS Kount request.');

        $order->setState($orderState);
        $order->addStatusToHistory($orderStatus, __('Order status updated from Kount.'), false);

        $order->setHoldBeforeState(null);
        $order->setHoldBeforeStatus(null);
    }
}
