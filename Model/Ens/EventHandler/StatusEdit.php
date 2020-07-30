<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ens\EventHandler;

use Swarming\Kount\Model\Ens\EventHandlerInterface;
use Swarming\Kount\Model\RisService;
use Swarming\Kount\Model\Order\ActionFactory as OrderActionFactory;

/**
 * Class StatusEdit
 * @package Swarming\Kount\Model\Ens\EventHandler
 */
class StatusEdit extends EventHandlerOrder implements EventHandlerInterface
{
    const EVENT_NAME = 'WORKFLOW_STATUS_EDIT';

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Swarming\Kount\Model\Order\ActionFactory
     */
    protected $orderActionFactory;

    /**
     * @var \Swarming\Kount\Model\Order\Ris
     */
    protected $orderRis;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param OrderActionFactory $orderActionFactory
     * @param \Swarming\Kount\Model\Order\Ris $orderRis
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Swarming\Kount\Model\Order\ActionFactory $orderActionFactory,
        \Swarming\Kount\Model\Order\Ris $orderRis,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->orderActionFactory = $orderActionFactory;
        $this->orderRis = $orderRis;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        parent::__construct($orderRepository, $searchCriteriaBuilder);
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
        $this->logger->info('new_value: ' . $newValue);
        $this->logger->info('agent: ' . $event->agent);
        $this->logger->info('occurred: ' . $event->occurred);

        $order = $this->loadOrder($orderId);
        $ris = $this->orderRis->getRis($order);

        $this->validateTransactionId($ris, $transactionId);
        $this->validateStatus($oldValue);
        $this->validateStatus($newValue);

        $this->updateRisResponse($order, $ris, $newValue);
        $this->updateOrderStatus($order, $ris, $oldValue, $newValue);
    }

    /**
     * @param \Swarming\Kount\Api\Data\RisInterface $ris
     * @param int $transactionId
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    protected function validateTransactionId($ris, $transactionId)
    {
        if (empty($transactionId)) {
            throw new \InvalidArgumentException('Invalid Transaction ID.');
        }


        if (empty($ris->getTransactionId())) {
            throw new \InvalidArgumentException('Invalid Order Transaction ID.');
        }

        if ($ris->getTransactionId() !== $transactionId) {
            throw new \InvalidArgumentException('Transaction ID does not match order, 
                event must be for discarded version of order!');
        }

        return true;
    }

    /**
     * @param string $status
     * @return bool
     */
    protected function validateStatus($status)
    {
        if (empty($status) || !in_array($status, RisService::getAutos(), true)) {
            throw new \InvalidArgumentException('Invalid status.');
        }
        return true;
    }

    /**
     * @param string $oldStatus
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\Kount\Api\Data\RisInterface $ris
     * @return bool
     */
    protected function isAllowedAction($oldStatus, $order, $ris)
    {
        if (in_array($oldStatus, [RisService::AUTO_REVIEW, RisService::AUTO_ESCALATE], true)
            && $ris->getResponse() !== $oldStatus
            && $this->isOrderPreHalt($order)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function isOrderPreHalt($order)
    {
        if ($order->getHoldBeforeState() != null) {
            return true;
        }
        $this->logger->info('Pre-hold order state / status not preserved.');
        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\Kount\Api\Data\RisInterface $ris
     * @param string $status
     */
    protected function updateRisResponse($order, $ris, $status)
    {
        $ris->setResponse($status);
        $order->addStatusHistoryComment(__('Kount ENS Notification: Modify status of an order by agent.'));
        $this->orderRepository->save($order);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\Kount\Api\Data\RisInterface $ris
     * @param string $oldStatus
     * @param string $newStatus
     */
    protected function updateOrderStatus($order, $ris, $oldStatus, $newStatus)
    {
        if (!$this->isAllowedAction($oldStatus, $order, $ris)) {
            return;
        }

        switch ($newStatus) {
            case RisService::AUTO_APPROVE:
                $this->approveOrder($order);
                break;
            case RisService::AUTO_DECLINE:
                $this->declineOrder($order);
                break;
            default:
                $this->logger->info("New status {$newStatus}, does not change order status.");
                break;
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    protected function approveOrder($order)
    {
        $this->logger->info('Kount status transitioned from review to allow. Order: '
            . $order->getIncrementId());

        $this->orderActionFactory->create(OrderActionFactory::RESTORE)->process($order);
        $this->orderRepository->save($order);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    protected function declineOrder($order)
    {
        $this->logger->info('Kount status transitioned from review to decline. Order: '
            . $order->getIncrementId());

        $this->orderActionFactory->create(OrderActionFactory::RESTORE)->process($order);
        $this->orderRepository->save($order);
        $this->orderActionFactory->create(OrderActionFactory::DECLINE)->process($order);
        $this->orderRepository->save($order);
    }
}
