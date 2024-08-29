<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Observer;

use Magento\Framework\Event\Observer;

class QuoteSubmitFailure implements \Magento\Framework\Event\ObserverInterface
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
     */
    public function __construct(
        \Kount\Kount\Helper\Workflow $helperWorkflow,
        \Kount\Kount\Model\Config\Workflow $configWorkflow,
        \Kount\Kount\Model\WorkflowFactory $workflowFactory,
        \Kount\Kount\Model\Observer\ConditionInterface $condition,
        \Kount\Kount\Model\Logger $logger
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
        $this->logger->info('sales_model_service_quote_submit_failure Start');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');
        $payment = $order->getPayment();

        if (!$this->helperWorkflow->isProcessable($order)) {
            return;
        }

        if (!$this->condition->is($payment, $order->getStoreId())) {
            $this->logger->info("Skip for {$payment->getMethod()} payment method.");
            return;
        }

        $workflow = $this->workflowFactory->create($this->configWorkflow->getWorkflowMode($order->getStoreId()));
        $workflow->failure($order);

        $this->logger->info('sales_model_service_quote_submit_failure Done');
    }
}
