<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Config\Source;

class PaymentMethods implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Kount\Kount\Model\Config\Backend\Scope
     */
    protected $configScope;

    /**
     * @var \Kount\Kount\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @param \Kount\Kount\Model\Config\Backend\Scope $configScope
     * @param \Kount\Kount\Helper\Payment $paymentHelper
     */
    public function __construct(
        \Kount\Kount\Model\Config\Backend\Scope $configScope,
        \Kount\Kount\Helper\Payment $paymentHelper
    ) {
        $this->configScope = $configScope;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $paymentMethods = $this->paymentHelper->getActiveMethods(
            $this->configScope->getScope(),
            $this->configScope->getScopeValue()
        );

        $options = [
            ['value' => '', 'label' => __('None')]
        ];

        /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
        foreach ($paymentMethods as $method) {
            $options[] = [
                'value' => $method->getCode(),
                'label' => $method->getTitle() . " ({$method->getCode()})"
            ];
        }

        return $options;
    }
}
