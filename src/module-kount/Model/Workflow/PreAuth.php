<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Workflow;

use Kount\Kount\Model\RisService;
use Kount\Kount\Model\WorkflowInterface;
use Kount\Kount\Model\WorkflowAbstract;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\Exception\LocalizedException;

class PreAuth extends WorkflowAbstract implements WorkflowInterface
{
    /**
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @throws LocalizedException
     */
    public function start(Payment $payment)
    {
        $order = $payment->getOrder();

        $this->logger->info('Before Magento Order Placement');
        $this->logger->info('Implementing Pre-Authorization Workflow for order:');
        $this->logger->info('Order Id: ' . $order->getIncrementId());
        $this->logger->info('Order Store Id: ' . $order->getStoreId());

        $this->risService->inquiryRequest($order, false);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function failure(Order $order)
    {
        if (!$this->configWorkflow->isNotifyProcessorDecline($order->getStoreId())) {
            return;
        }

        $this->logger->info('On Magento Order Fail');
        $this->logger->info('Order failed, sending update to Kount RIS.');
        $this->logger->info('Order Id: ' . $order->getIncrementId());
        $this->logger->info('Order Store Id: ' . $order->getStoreId());

        $this->risService->inquiryRequest($order, true, RisService::AUTH_DECLINED, RisService::MACK_NO);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @throws LocalizedException
     */
    public function success(Order $order)
    {
        $this->logger->info('After Magento Order Placement');
        $this->logger->info('Order succeeded, sending update to Kount RIS.');
        $this->logger->info('Order Id: ' . $order->getIncrementId());
        $this->logger->info('Order Store Id: ' . $order->getStoreId());

        $this->risService->updateRequest($order, true);
        $this->updaterOrderStatus($order);
    }
}
