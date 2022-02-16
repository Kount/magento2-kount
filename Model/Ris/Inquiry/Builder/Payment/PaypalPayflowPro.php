<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Inquiry\Builder\Payment;

use Kount\Kount\Model\Ris\Base\Builder\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class PaypalPayflowPro extends \Kount\Kount\Model\Ris\Update\Builder\Payment\PaypalPayflowPro
    implements PaymentInterface
{
    /**
     * @param \Kount_Ris_Request|\Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return void
     */
    public function process(\Kount_Ris_Request $request, OrderPaymentInterface $payment)
    {
        $request->setNoPayment();
        $request->setUserDefinedField('payment_type', 'payflowpro');

        parent::process($request, $payment);
    }
}
