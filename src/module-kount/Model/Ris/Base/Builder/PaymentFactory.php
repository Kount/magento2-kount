<?php
/**
 * Copyright (c) 2023 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Base\Builder;

class PaymentFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $paymentClass
     * @return \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface
     * @throws \InvalidArgumentException
     */
    public function create($paymentClass)
    {
        $payment = $this->objectManager->create($paymentClass);
        if (!$payment instanceof PaymentInterface) {
            throw new \InvalidArgumentException(
                get_class($payment) . ' must be an instance of \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface.'
            );
        }
        return $payment;
    }
}
