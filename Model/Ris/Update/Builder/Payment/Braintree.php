<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Update\Builder\Payment;

use Swarming\Kount\Model\Ris\Base\Builder\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class Braintree implements PaymentInterface
{
    /**
     * @param \Kount_Ris_Request|\Kount_Ris_Request_Update $request
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return void
     */
    public function process(\Kount_Ris_Request $request, OrderPaymentInterface $payment)
    {
        $request->setAvst($this->getValue($payment, 'avsStreetAddressResponseCode'));
        $request->setAvsz($this->getValue($payment, 'avsPostalCodeResponseCode'));
        $request->setCvvr($this->getValue($payment, 'cvvResponseCode'));
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @param string $code
     * @return string
     */
    protected function getValue(OrderPaymentInterface $payment, $code)
    {
        $value = $payment->getAdditionalInformation($code);
        return in_array($value, ['M', 'N', 'X'], true) ? $value : 'X';
    }
}
