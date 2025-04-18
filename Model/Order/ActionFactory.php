<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Order;

class ActionFactory
{
    const DECLINE = 'decline';
    const REVIEW = 'review';
    const RESTORE = 'restore';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $actions = [
        self::DECLINE => \Kount\Kount\Model\Order\Action\Decline::class,
        self::REVIEW => \Kount\Kount\Model\Order\Action\Review::class,
        self::RESTORE => \Kount\Kount\Model\Order\Action\Restore::class
    ];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $action
     * @return \Kount\Kount\Model\Order\ActionInterface
     * @throws \InvalidArgumentException
     */
    public function create($action)
    {
        if (empty($this->actions[$action])) {
            throw new \InvalidArgumentException("{$action} order action isn't defined.");
        }

        $actionObject = $this->objectManager->create($this->actions[$action]);
        if (!$actionObject instanceof ActionInterface) {
            throw new \InvalidArgumentException(
                get_class($actionObject) . ' must be an instance of ' . \Kount\Kount\Model\Order\ActionInterface::class
            );
        }
        return $actionObject;
    }
}
