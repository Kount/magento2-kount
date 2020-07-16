<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ens\EventHandler;

use Swarming\Kount\Model\Ens\EventHandlerInterface;

class NotesAdd implements EventHandlerInterface
{
    const EVENT_NAME = 'WORKFLOW_NOTES_ADD';

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    private $logger;

    /**
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Simplexml\Element $event
     */
    public function process($event)
    {
        $this->logger->info('ENS event ' . self::EVENT_NAME . ' received, ignored.');
    }
}
