<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Base\Builder;

use Magento\Sales\Api\Data\OrderPaymentInterface;

class Payment implements \Swarming\Kount\Model\Ris\Base\Builder\PaymentInterface
{
    /**
     * @var \Swarming\Kount\Model\Ris\Base\Builder\PaymentFactory
     */
    protected $paymentBuilderFactory;

    /**
     * @var array
     */
    protected $payments;

    /**
     * @var string
     */
    protected $defaultPayment;

    /**
     * @param \Swarming\Kount\Model\Ris\Base\Builder\PaymentFactory $paymentBuilderFactory
     * @param string $defaultPayment
     * @param array $payments
     */
    public function __construct(
        \Swarming\Kount\Model\Ris\Base\Builder\PaymentFactory $paymentBuilderFactory,
        $defaultPayment,
        array $payments
    ) {
        $this->paymentBuilderFactory = $paymentBuilderFactory;
        $this->defaultPayment = $defaultPayment;
        $this->payments = $payments;
    }

    /**
     * @param string $paymentCode
     * @return string
     */
    protected function getClassByCode($paymentCode)
    {
        return isset($this->payments[$paymentCode]) ? $this->payments[$paymentCode] : $this->defaultPayment;
    }

    /**
     * @param \Kount_Ris_Request $request
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     */
    public function process(\Kount_Ris_Request $request, OrderPaymentInterface $payment)
    {
        $paymentBuilderClass = $this->getClassByCode($payment->getMethod());
        $paymentBuilder = $this->paymentBuilderFactory->create($paymentBuilderClass);
        $paymentBuilder->process($request, $payment);
    }
}
