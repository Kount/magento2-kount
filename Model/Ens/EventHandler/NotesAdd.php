<?php
/**
 * Copyright (c) 2020 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ens\EventHandler;

use Swarming\Kount\Model\Ens\EventHandlerInterface;

class NotesAdd extends EventHandlerOrder implements EventHandlerInterface
{
    const EVENT_NAME = 'WORKFLOW_NOTES_ADD';

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    private $logger;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param \Swarming\Kount\Model\Logger $logger
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Swarming\Kount\Model\Logger $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        parent::__construct($orderRepository, $criteria, $filterGroup, $filterBuilder);
    }

    /**
     * @param \Magento\Framework\Simplexml\Element $event
     */
    public function process($event)
    {
        list($transactionId, $orderId, $oldValue, $newValue) = $this->fetchVars($event);

        $this->logger->info('ENS Event Details');
        $this->logger->info('Name: ' . self::EVENT_NAME);
        $this->logger->info('order_number: ' . $orderId);
        $this->logger->info('transaction_id: ' . $transactionId);
        $this->logger->info('old_value: ' . $oldValue);
        $this->logger->info('new_value: ' . $newValue[0]);
        $this->logger->info('agent: ' . $event->agent);
        $this->logger->info('occurred: ' . $event->occurred);

        // Create a new comment for the order
        $newComment = "Reason Code: " . $newValue['@']['reason_code'] . "<br>"
                      . "Comment: " . $newValue[0];

        // Add the comment to the order
        $order = $this->loadOrder($orderId);
        $order->addCommentToStatusHistory($newComment);
        $this->orderRepository->save($order);
    }
}
