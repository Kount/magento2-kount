<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Inquiry;

use Magento\Sales\Model\Order;
use Kount\Kount\Model\RisService;

class Builder
{
    /**
     * @var \Kount\Kount\Model\Ris\InquiryFactory
     */
    protected $inquiryFactory;

    /**
     * @var \Kount\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Kount\Kount\Model\Ris\Inquiry\Builder\VersionInfo
     */
    protected $versionBuilder;

    /**
     * @var \Kount\Kount\Model\Ris\Base\Builder\Session
     */
    protected $sessionBuilder;

    /**
     * @var \Kount\Kount\Model\Ris\Inquiry\Builder\Order
     */
    protected $orderBuilder;

    /**
     * @var \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface
     */
    protected $paymentBuilder;

    /**
     * @param \Kount\Kount\Model\Ris\InquiryFactory $inquiryFactory
     * @param \Kount\Kount\Model\Config\Account $configAccount
     * @param \Kount\Kount\Model\Ris\Inquiry\Builder\VersionInfo $versionBuilder
     * @param \Kount\Kount\Model\Ris\Base\Builder\Session $sessionBuilder
     * @param \Kount\Kount\Model\Ris\Inquiry\Builder\Order $orderBuilder
     * @param \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface $paymentBuilder
     */
    public function __construct(
        \Kount\Kount\Model\Ris\InquiryFactory $inquiryFactory,
        \Kount\Kount\Model\Config\Account $configAccount,
        \Kount\Kount\Model\Ris\Inquiry\Builder\VersionInfo $versionBuilder,
        \Kount\Kount\Model\Ris\Base\Builder\Session $sessionBuilder,
        \Kount\Kount\Model\Ris\Inquiry\Builder\Order $orderBuilder,
        \Kount\Kount\Model\Ris\Base\Builder\PaymentInterface $paymentBuilder
    ) {
        $this->inquiryFactory = $inquiryFactory;
        $this->configAccount = $configAccount;
        $this->versionBuilder = $versionBuilder;
        $this->sessionBuilder = $sessionBuilder;
        $this->orderBuilder = $orderBuilder;
        $this->paymentBuilder = $paymentBuilder;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string $auth
     * @param string $mack
     * @return \Kount_Ris_Request_Inquiry
     */
    public function build(Order $order, $auth = RisService::AUTH_AUTHORIZED, $mack = RisService::MACK_YES)
    {
        $inquiry = $this->inquiryFactory->create($order->getStore()->getWebsiteId());

        $inquiry->setWebsite($this->configAccount->getWebsite($order->getStore()->getWebsiteId()));
        $inquiry->setAuth($auth);
        $inquiry->setMack($mack);

        $this->versionBuilder->process($inquiry);
        $this->sessionBuilder->process($inquiry);
        $this->orderBuilder->process($inquiry, $order);
        $this->paymentBuilder->process($inquiry, $order->getPayment());

        return $inquiry;
    }
}
