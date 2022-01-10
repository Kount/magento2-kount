<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ens;

class EventHandlerFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $handlers
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $handlers
    ) {
        $this->objectManager = $objectManager;
        $this->handlers = $handlers;
    }

    /**
     * @param string $handlerCode
     * @return \Kount\Kount\Model\Ens\EventHandlerInterface
     * @throws \InvalidArgumentException
     */
    public function create($handlerCode)
    {
        if (empty($this->handlers[$handlerCode])) {
            throw new \InvalidArgumentException("Handler for {$handlerCode} ENS event isn't configured.");
        }

        $eventHandler = $this->objectManager->create($this->handlers[$handlerCode]);
        if (!$eventHandler instanceof EventHandlerInterface) {
            throw new \InvalidArgumentException(
                get_class($eventHandler) . ' must be an instance of \Kount\Kount\Model\Ens\EventHandlerInterface.'
            );
        }
        return $eventHandler;
    }
}
