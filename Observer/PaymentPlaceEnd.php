<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Observer;

use Magento\Framework\Event\Observer;

class PaymentPlaceEnd implements \Magento\Framework\Event\ObserverInterface
{
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
     * @param array $allowedMethods
     */
    public function __construct(
        \Swarming\Kount\Helper\Workflow $helperWorkflow,
        \Swarming\Kount\Model\Config\Workflow $configWorkflow,
        \Swarming\Kount\Model\WorkflowFactory $workflowFactory,
        \Swarming\Kount\Model\Observer\ConditionInterface $condition,
        \Swarming\Kount\Model\Logger $logger,
        array $allowedMethods = []
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
        $this->logger->info('sales_order_payment_place_end Start');

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getData('payment');
        $order = $payment->getOrder();

        if (!$this->helperWorkflow->isProcessable($order)) {
            return;
        }

        if (!$this->condition->is($payment, $order->getStoreId())) {
            $this->logger->info("Skip for {$payment->getMethod()} payment method.");
            return;
        }

        $workflow = $this->workflowFactory->create($this->configWorkflow->getWorkflowMode($order->getStoreId()));
        $workflow->success($payment->getOrder());

        $this->logger->info('sales_order_payment_place_end Done ');
    }
}
