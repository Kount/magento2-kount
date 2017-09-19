<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\Exception\LocalizedException;

interface WorkflowInterface
{
    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @throws LocalizedException
     */
    public function start(Payment $payment);

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws LocalizedException
     */
    public function failure(Order $order);

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws LocalizedException
     */
    public function success(Order $order);
}
