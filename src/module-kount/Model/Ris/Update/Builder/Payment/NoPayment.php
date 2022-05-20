<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Update\Builder\Payment;

use Kount\Kount\Model\Ris\Base\Builder\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class NoPayment implements PaymentInterface
{
    /**
     * @param \Kount_Ris_Request $request
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return $this|void
     */
    public function process(\Kount_Ris_Request $request, OrderPaymentInterface $payment)
    {
        return $this;
    }
}
