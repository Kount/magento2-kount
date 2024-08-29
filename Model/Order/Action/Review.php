<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Order\Action;

use Magento\Sales\Model\Order;
use Kount\Kount\Model\Order\ActionInterface;
use Kount\Kount\Model\Order\Ris as OrderRis;

class Review implements ActionInterface
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
        $orderState = $order->getState();
        $orderStatus = $order->getStatus();
        if ($orderState === Order::STATE_HOLDED && $orderStatus === OrderRis::STATUS_KOUNT_REVIEW) {
            $this->logger->info('Setting order to Kount Review status/state - already set, skipping');
            return;
        }

        $this->logger->info('Setting order to Kount Review status/state');

        $order->setHoldBeforeState($orderState);
        $order->setHoldBeforeStatus($orderStatus);

        $order->setState(Order::STATE_HOLDED);
        $order->addStatusToHistory(OrderRis::STATUS_KOUNT_REVIEW, __('Order on review from Kount.'), false);
    }
}
