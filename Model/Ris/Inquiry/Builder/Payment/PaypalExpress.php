<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Inquiry\Builder\Payment;

use Swarming\Kount\Model\Ris\Base\Builder\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Paypal\Model\Express\Checkout as PaypalExpressCheckout;
use Magento\Framework\Exception\LocalizedException;

class PaypalExpress implements PaymentInterface
{
    /**
     * @param \Kount_Ris_Request|\Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @throws LocalizedException
     */
    public function process(\Kount_Ris_Request $request, OrderPaymentInterface $payment)
    {
        $payPalId = $payment->getAdditionalInformation(PaypalExpressCheckout::PAYMENT_INFO_TRANSPORT_PAYER_ID);
        if (empty($payPalId)) {
            throw  new LocalizedException(__('Invalid Payer Id for PayPal payment.'));
        }
        $request->setPayPalPayment($payPalId);
    }
}
