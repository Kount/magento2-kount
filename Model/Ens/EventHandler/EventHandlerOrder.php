<?php
/**
 * Copyright (c) 2020 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ens\EventHandler;

/**
 * Parent class for WORKFLOW events. The class contains common methods.
 */
class EventHandlerOrder
{
    const ORDER_INCREMENT_ID_FIELD = 'increment_id';

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param \Magento\Framework\Simplexml\Element $event
     * @return array
     */
    public function fetchVars($event)
    {
        $eventData = $event->asArray();
        return [
            (empty($eventData['key'][0]) ? '' : $eventData['key'][0]),
            (empty($eventData['key']['@']['order_number']) ? '' : $eventData['key']['@']['order_number']),
            (empty($eventData['old_value']) ? '' : $eventData['old_value']),
            (empty($eventData['new_value']) ? '' : $eventData['new_value'])
        ];
    }

    /**
     * @param string $orderId
     * @return \Magento\Sales\Model\Order
     *
     * @throws \InvalidArgumentException
     */
    public function loadOrder($orderId)
    {
        if (empty($orderId)) {
            throw new \InvalidArgumentException('Invalid Order number.');
        }

        // $orderId is an increment ID. That why it have to use the method getFirstItem() on search result
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(self::ORDER_INCREMENT_ID_FIELD, $orderId)
            ->create();
        $order = $this->orderRepository->getList($searchCriteria)->getFirstItem();

        if (!$order->getId()) {
            throw new \InvalidArgumentException("Unable to locate order for: {$orderId}");
        }
        return $order;
    }
}
