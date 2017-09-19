<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Plugin\PayPal\Controller\Payflow;

use Magento\Sales\Model\Order;

class ReturnUrl
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

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
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $successOrderStates = [
        Order::STATE_PROCESSING,
        Order::STATE_COMPLETE,
    ];

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Swarming\Kount\Helper\Workflow $helperWorkflow
     * @param \Swarming\Kount\Model\Config\Workflow $configWorkflow
     * @param \Swarming\Kount\Model\WorkflowFactory $workflowFactory
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Swarming\Kount\Helper\Workflow $helperWorkflow,
        \Swarming\Kount\Model\Config\Workflow $configWorkflow,
        \Swarming\Kount\Model\WorkflowFactory $workflowFactory,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->helperWorkflow = $helperWorkflow;
        $this->configWorkflow = $configWorkflow;
        $this->workflowFactory = $workflowFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Paypal\Controller\Payflow\ReturnUrl $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterExecute($subject, $result)
    {
        $orderId = $this->checkoutSession->getLastRealOrderId();
        if (!$orderId) {
            return $result;
        }

        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        if (!$order->getIncrementId()) {
            return $result;
        }

        $this->logger->info('paypal_payflow_return_url Start');

        if (!$this->helperWorkflow->isProcessable($order)) {
            return $result;
        }

        $postAuthWorkFlow = $this->workflowFactory->create($this->configWorkflow->getWorkflowMode($order->getStoreId()));

        if ($this->isOrderSuccess($order)) {
            $postAuthWorkFlow->success($order);
        } else {
            $postAuthWorkFlow->failure($order);
        }

        $this->logger->info('paypal_payflow_return_url Done');

        return $result;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function isOrderSuccess(Order $order)
    {
        return in_array($order->getState(), $this->successOrderStates, true);
    }
}
