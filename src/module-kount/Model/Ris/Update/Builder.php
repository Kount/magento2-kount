<?php
/**
 * Copyright (c) 2023 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Update;

use Kount\Kount\Model\RisService;
use Magento\Sales\Model\Order;

class Builder
{
    /**
     * @var \Kount\Kount\Model\Ris\UpdateFactory
     */
    protected $updateFactory;

    /**
     * @var \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface
     */
    protected $paymentBuilder;

    /**
     * @var \Kount\Kount\Model\Ris\Base\Builder\Session
     */
    protected $sessionBuilder;

    /**
     * @param \Kount\Kount\Model\Ris\UpdateFactory $updateFactory
     * @param \Kount\Kount\Model\Ris\Base\Builder\Session $sessionBuilder
     * @param \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface $paymentBuilder
     */
    public function __construct(
        \Kount\Kount\Model\Ris\UpdateFactory $updateFactory,
        \Kount\Kount\Model\Ris\Base\Builder\Session $sessionBuilder,
        \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface $paymentBuilder
    ) {
        $this->updateFactory = $updateFactory;
        $this->sessionBuilder = $sessionBuilder;
        $this->paymentBuilder = $paymentBuilder;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string $risTransactionId
     * @param bool $processorAuthorized
     * @return \Kount_Ris_Request_Update
     */
    public function build(Order $order, $risTransactionId, $processorAuthorized)
    {
        $updateRequest = $this->updateFactory->create($order->getStore()->getWebsiteId());

        $updateRequest->setAuth(($processorAuthorized ? RisService::AUTO_APPROVE : RisService::AUTO_DECLINE));
        $updateRequest->setMack(($processorAuthorized ? RisService::MACK_YES : RisService::MACK_NO));
        $updateRequest->setTransactionId($risTransactionId);

        $this->sessionBuilder->process($updateRequest);
        $this->paymentBuilder->process($updateRequest, $order->getPayment());

        return $updateRequest;
    }
}
