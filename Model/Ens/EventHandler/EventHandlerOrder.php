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
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    private $filterGroup;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteria = $criteria;
        $this->filterGroup = $filterGroup;
        $this->filterBuilder = $filterBuilder;
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
        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('increment_id')
                ->setConditionType('eq')
                ->setValue($orderId)
                ->create()
            ]);
        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $order = $this->orderRepository->getList($this->searchCriteria)->getFirstItem();

        if (!$order->getId()) {
            throw new \InvalidArgumentException("Unable to locate order for: {$orderId}");
        }
        return $order;
    }
}
