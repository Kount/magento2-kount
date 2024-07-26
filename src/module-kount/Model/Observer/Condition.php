<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Observer;

use Magento\Sales\Model\Order\Payment;

class Condition implements ConditionInterface
{
    /**
     * @var \Kount\Kount\Model\Observer\ConditionFactory
     */
    protected $conditionFactory;

    /**
     * @var bool
     */
    protected $default;

    /**
     * @var array
     */
    protected $conditions;

    /**
     * @param \Kount\Kount\Model\Observer\ConditionFactory $conditionFactory
     * @param bool $default
     * @param array $conditions
     */
    public function __construct(
        \Kount\Kount\Model\Observer\ConditionFactory $conditionFactory,
        $default = true,
        array $conditions = []
    ) {
        $this->conditionFactory = $conditionFactory;
        $this->default = (bool)$default;
        $this->conditions = $conditions;
    }

    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param int|null $storeId
     * @return bool
     */
    public function is(Payment $payment, $storeId = null)
    {
        $methodCode = $payment->getMethod();
        if (isset($this->conditions[$methodCode])) {
            $condition = $this->conditionFactory->create($this->conditions[$methodCode]);
            return $condition->is($payment, $storeId);
        }
        return $this->default;
    }
}
