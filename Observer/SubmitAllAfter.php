<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Swarming\Kount\Model\Config\Source\DeclineAction;
use Magento\Sales\Model\Order;

class SubmitAllAfter implements \Magento\Framework\Event\ObserverInterface
{
    const REVIEW_STATUSES = [
        DeclineAction::ACTION_HOLD,
        DeclineAction::ACTION_CANCEL,
        DeclineAction::ACTION_REFUND,
        Order::STATE_CANCELED
    ];

    /**
     * @var \Swarming\Kount\Helper\Workflow
     */
    protected $helperWorkflow;

    /**
     * @var \Swarming\Kount\Model\Config\Workflow
     */
    protected $configWorkflow;

    /**
     * @var \Swarming\Kount\Model\WorkflowFactory
     */
    protected $workflowFactory;

    /**
     * @var \Swarming\Kount\Model\Observer\ConditionInterface
     */
    protected $condition;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Swarming\Kount\Helper\Workflow $helperWorkflow
     * @param \Swarming\Kount\Model\Config\Workflow $configWorkflow
     * @param \Swarming\Kount\Model\WorkflowFactory $workflowFactory
     * @param \Swarming\Kount\Model\Observer\ConditionInterface $condition
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Swarming\Kount\Helper\Workflow $helperWorkflow,
        \Swarming\Kount\Model\Config\Workflow $configWorkflow,
        \Swarming\Kount\Model\WorkflowFactory $workflowFactory,
        \Swarming\Kount\Model\Observer\ConditionInterface $condition,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->helperWorkflow = $helperWorkflow;
        $this->configWorkflow = $configWorkflow;
        $this->workflowFactory = $workflowFactory;
        $this->condition = $condition;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->logger->info('checkout_submit_all_after Start');

        $event = $observer->getEvent();
        $orders = $event->getOrders() ?: [$event->getOrder()];

        foreach ($orders as $order) {
            $payment = $order->getPayment();
            if (!$this->helperWorkflow->isProcessable($order)) {
                continue;
            }
            if (!$this->condition->is($payment, $order->getStoreId())) {
                $this->logger->info("Skip for {$payment->getMethod()} payment method.");
                continue;
            }

            $workflow = $this->workflowFactory->create($this->configWorkflow->getWorkflowMode($order->getStoreId()));
            $workflow->success($order);
            $status = $order->getStatus();
            if (in_array($status, self::REVIEW_STATUSES)) {
                throw new LocalizedException(__('Order declined. Please ensure your information is correct. If the problem persists, please contact us for assistance.'));
            }
        }

        $this->logger->info('checkout_submit_all_after Done');
    }
}
