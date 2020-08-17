<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Inquiry\Builder;

use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Swarming\Kount\Model\Config\Account as ConfigAccount;

class Order
{
    const FIELD_CARRIER = 'CARRIER';
    const FIELD_METHOD = 'METHOD';
    const FIELD_COUPON_CODE = 'COUPON_CODE';
    const FIELD_ACCOUNT_NAME = 'ACCOUNT_NAME';

    const LOCAL_IP = '10.0.0.1';

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Swarming\Kount\Model\Ris\Inquiry\Builder\Order\CartItemFactory
     */
    protected $cartItemFactory;

    /**
     * @var \Swarming\Kount\Model\Config\Account
     */
    protected $configAccount;

    /**
     * @var \Swarming\Kount\Model\Config\PhoneToWeb
     */
    protected $configPhoneToWeb;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $httpHeader;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Swarming\Kount\Model\Ris\Inquiry\Builder\Order\CartItemFactory $cartItemFactory
     * @param \Swarming\Kount\Model\Config\PhoneToWeb $configPhoneToWeb
     * @param \Magento\Framework\HTTP\Header $httpHeader
     * @param \Swarming\Kount\Model\Logger $logger
     * @param \Swarming\Kount\Model\Config\Account|null $configAccount
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Swarming\Kount\Model\Ris\Inquiry\Builder\Order\CartItemFactory $cartItemFactory,
        \Swarming\Kount\Model\Config\PhoneToWeb $configPhoneToWeb,
        \Magento\Framework\HTTP\Header $httpHeader,
        \Swarming\Kount\Model\Logger $logger,
        \Swarming\Kount\Model\Config\Account $configAccount = null
    ) {
        $this->appState = $appState;
        $this->customerRegistry = $customerRegistry;
        $this->directoryHelper = $directoryHelper;
        $this->cartItemFactory = $cartItemFactory;
        $this->configAccount = $configAccount ?: ObjectManager::getInstance()->get(ConfigAccount::class);
        $this->configPhoneToWeb = $configPhoneToWeb;
        $this->httpHeader = $httpHeader;
        $this->logger = $logger;
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    public function process(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $request->setOrderNumber($order->getIncrementId());

        $this->processOrderTotal($request, $order);
        $this->processShippingMethod($request, $order);
        $this->processCouponCode($request, $order);
        $this->processAccountName($request, $order);
        $this->processCustomerInfo($request, $order);
        $this->processBillingInfo($request, $order);
        $this->processShippingInfo($request, $order);
        $this->processCart($request, $order);
        $this->processIpAndUserAgent($request, $order);
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processOrderTotal(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $baseGrandTotal = $this->convertAndRoundAmount($order->getBaseGrandTotal(), $order->getBaseCurrencyCode());
        $request->setTotal($baseGrandTotal);

        $currency = $this->configAccount->getCurrency();
        $request->setCurrency($currency);

        $this->logger->info('Base Currency: ' . $order->getBaseCurrencyCode());
        $this->logger->info("Base Grand Total ({$currency}): " . $baseGrandTotal);
    }

    /**
     * @param float $amount
     * @param string $baseCurrencyCode
     * @return float
     */
    protected function convertAndRoundAmount($amount, $baseCurrencyCode)
    {
        $currency = $this->configAccount->getCurrency();
        $amount = $currency === $baseCurrencyCode
            ? $amount
            : $this->directoryHelper->currencyConvert($amount, $baseCurrencyCode, $currency);
        return round($amount * 100);
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processShippingMethod(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $shippingMethod = $order->getShippingMethod(true);
        if (!$shippingMethod) {
            return;
        }
        if ($shippingMethod->getData('carrier_code')) {
            $request->setUserDefinedField(self::FIELD_CARRIER, $shippingMethod->getData('carrier_code'));
        }
        if ($shippingMethod->getData('method')) {
            $request->setUserDefinedField(self::FIELD_METHOD, $shippingMethod->getData('method'));
        }
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processCouponCode(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        if (!empty($order->getCouponCode())) {
            $request->setUserDefinedField(self::FIELD_COUPON_CODE, $order->getCouponCode());
        }
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processAccountName(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        if (!empty($order->getCustomerName())) {
            $request->setUserDefinedField(self::FIELD_ACCOUNT_NAME, $order->getCustomerName());
        }
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processCustomerInfo(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $billingAddress = $order->getBillingAddress();
        $name = !empty($billingAddress)
            ? $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname()
            : $order->getCustomerName();

        $request->setName($name);
        $request->setEmail($order->getCustomerEmail());

        if (!$order->getCustomerId()) {
            $request->setEpoch(time());
            return;
        }

        $customer = $this->customerRegistry->retrieve($order->getCustomerId());
        $request->setUnique($order->getCustomerId());
        $request->setEpoch(strtotime($customer->getCreatedAt()));
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processBillingInfo(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $billingAddress = $order->getBillingAddress();
        if (!empty($billingAddress)) {
            $request->setBillingPhoneNumber($billingAddress->getTelephone());
            $request->setBillingAddress(
                $billingAddress->getStreetLine(1),
                ($billingAddress->getStreetLine(2) ?: ''),
                $billingAddress->getCity(),
                $billingAddress->getRegion(),
                $billingAddress->getPostcode(),
                $billingAddress->getCountryId()
            );
        }
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processShippingInfo(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $shippingAddress = $order->getShippingAddress();
        if (!empty($shippingAddress)) {
            $request->setShippingName($shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname());
            $request->setShippingPhoneNumber($shippingAddress->getTelephone());
            $request->setShippingEmail($order->getCustomerEmail());
            $request->setShippingAddress(
                $shippingAddress->getStreetLine(1),
                ($shippingAddress->getStreetLine(2) ?: ''),
                $shippingAddress->getCity(),
                $shippingAddress->getRegion(),
                $shippingAddress->getPostcode(),
                $shippingAddress->getCountryId()
            );
        }
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processCart(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $realOrderItems = [];
        $orderItems = $order->getAllItems();
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getParentItem()) {
                continue;
            }
            $realOrderItems[] = $orderItem;
        }

        $cart = [];
        /** @var \Magento\Sales\Model\Order\Item $realOrderItem */
        foreach ($realOrderItems as $realOrderItem) {
            $cart[] = $this->cartItemFactory->create([
                'productType' => $realOrderItem->getSku(),
                'itemName' => $realOrderItem->getName(),
                'description' => ($realOrderItem->getDescription() ? $realOrderItem->getDescription() : ''),
                'quantity' => round($realOrderItem->getQtyOrdered()),
                'price' => $this->convertAndRoundAmount($realOrderItem->getBasePrice(), $order->getBaseCurrencyCode()),
            ]);
        }
        $request->setCart($cart);
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function processIpAndUserAgent(\Kount_Ris_Request_Inquiry $request, \Magento\Sales\Model\Order $order)
    {
        $request->setUserAgent($this->httpHeader->getHttpUserAgent());
        $websiteId = $order->getStore()->getWebsiteId();

        $ipAddress = $this->getIpAddress($order);
        if ($this->isBackend($ipAddress) || $this->configPhoneToWeb->isIpWhite($ipAddress, $websiteId)) {
            $request->setIpAddress(self::LOCAL_IP);
        } else {
            $request->setIpAddress($ipAddress);
        }
    }

    /**
     * @param string|null $ipAddress
     * @return bool
     */
    protected function isBackend($ipAddress)
    {
        return $this->appState->getAreaCode() === Area::AREA_ADMINHTML || empty($ipAddress);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    protected function getIpAddress(\Magento\Sales\Model\Order $order)
    {
        $ipAddress = ($order->getXForwardedFor() ?: ($this->getRequestXForwardedFor() ?: $order->getRemoteIp()));
        if (false !== strpos($ipAddress, ',')) {
            $ipAddress = explode(',', $ipAddress);
            $ipAddress = array_shift($ipAddress);
        }
        return $ipAddress;
    }

    /**
     * @deprecated
     *
     * As temporary fix
     *
     * @return \Magento\Framework\App\Request\Http
     */
    private function getRequestXForwardedFor()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\Request\Http::class);

        return $request->getServer('HTTP_X_FORWARDED_FOR');
    }
}
