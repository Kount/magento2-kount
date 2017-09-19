<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Inquiry;

use Magento\Sales\Model\Order;
use Swarming\Kount\Model\RisService;

class Builder
{
    /**
     * @var \Swarming\Kount\Model\Ris\InquiryFactory
     */
    protected $inquiryFactory;

    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Swarming\Kount\Model\Ris\Inquiry\Builder\VersionInfo
     */
    protected $versionBuilder;

    /**
     * @var \Swarming\Kount\Model\Ris\Base\Builder\Session
     */
    protected $sessionBuilder;

    /**
     * @var \Swarming\Kount\Model\Ris\Inquiry\Builder\Order
     */
    protected $orderBuilder;

    /**
     * @var \Swarming\Kount\Model\Ris\Base\Builder\PaymentInterface
     */
    protected $paymentBuilder;

    /**
     * @param \Swarming\Kount\Model\Ris\InquiryFactory $inquiryFactory
     * @param \Swarming\Kount\Model\Config\Account $configAccount
     * @param \Swarming\Kount\Model\Ris\Inquiry\Builder\VersionInfo $versionBuilder
     * @param \Swarming\Kount\Model\Ris\Base\Builder\Session $sessionBuilder
     * @param \Swarming\Kount\Model\Ris\Inquiry\Builder\Order $orderBuilder
     * @param \Swarming\Kount\Model\Ris\Base\Builder\PaymentInterface $paymentBuilder
     */
    public function __construct(
        \Swarming\Kount\Model\Ris\InquiryFactory $inquiryFactory,
        \Swarming\Kount\Model\Config\Account $configAccount,
        \Swarming\Kount\Model\Ris\Inquiry\Builder\VersionInfo $versionBuilder,
        \Swarming\Kount\Model\Ris\Base\Builder\Session $sessionBuilder,
        \Swarming\Kount\Model\Ris\Inquiry\Builder\Order $orderBuilder,
        \Swarming\Kount\Model\Ris\Base\Builder\PaymentInterface $paymentBuilder
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
