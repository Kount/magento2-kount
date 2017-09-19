<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Order\Action;

use Swarming\Kount\Model\Order\ActionInterface;

class Restore implements ActionInterface
{
    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function process($order)
    {
        $this->logger->info('Restore order status/state by ENS Kount request.');

        $order->setState($order->getHoldBeforeState());
        $order->addStatusToHistory($order->getHoldBeforeStatus(), __('Order status updated from Kount.'), false);

        $order->setHoldBeforeState(null);
        $order->setHoldBeforeStatus(null);
    }
}
