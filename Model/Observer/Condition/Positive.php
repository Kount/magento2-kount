<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Observer\Condition;

use Magento\Sales\Model\Order\Payment;
use Kount\Kount\Model\Observer\ConditionInterface;

class Positive implements ConditionInterface
{
    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param int|null $storeId
     * @return bool
     */
    public function is(Payment $payment, $storeId = null)
    {
        return true;
    }
}
