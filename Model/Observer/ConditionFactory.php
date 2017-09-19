<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Observer;

class ConditionFactory
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
     * @param string $className
     * @return \Swarming\Kount\Model\Observer\ConditionInterface
     * @throws \InvalidArgumentException
     */
    public function create($className)
    {
        $skipCondition = $this->objectManager->create($className);
        if (!$skipCondition instanceof ConditionInterface) {
            throw new \InvalidArgumentException(get_class($skipCondition) . ' must be an instance of \Swarming\Kount\Model\Observer\ConditionInterface.');
        }
        return $skipCondition;
    }
}
