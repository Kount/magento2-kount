<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Observer;

use Magento\Framework\Event\Observer;

class PaymentPlaceEnd implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Kount\Kount\Helper\Workflow
     */
    protected $helperWorkflow;

    /**
     * @var \Kount\Kount\Model\Config\Workflow
     */
    protected $configWorkflow;

    /**
     * @var \Kount\Kount\Model\WorkflowFactory
     */
    protected $workflowFactory;

    /**
     * @var \Kount\Kount\Model\Observer\ConditionInterface
     */
    protected $condition;

    /**
     * @var \Kount\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Kount\Kount\Helper\Workflow $helperWorkflow
     * @param \Kount\Kount\Model\Config\Workflow $configWorkflow
     * @param \Kount\Kount\Model\WorkflowFactory $workflowFactory
     * @param \Kount\Kount\Model\Observer\ConditionInterface $condition
     * @param \Kount\Kount\Model\Logger $logger
     * @param array $allowedMethods
     */
    public function __construct(
        \Kount\Kount\Helper\Workflow $helperWorkflow,
        \Kount\Kount\Model\Config\Workflow $configWorkflow,
        \Kount\Kount\Model\WorkflowFactory $workflowFactory,
        \Kount\Kount\Model\Observer\ConditionInterface $condition,
        \Kount\Kount\Model\Logger $logger,
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
