<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Base\Builder;

use Magento\Sales\Api\Data\OrderPaymentInterface;

interface PaymentInterface
{
    /**
     * @param \Kount_Ris_Request $request
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return void
     */
    public function process(\Kount_Ris_Request $request, OrderPaymentInterface $payment);
}
