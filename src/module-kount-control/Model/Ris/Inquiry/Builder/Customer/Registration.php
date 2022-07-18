<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Model\Ris\Inquiry\Builder\Customer;

use Kount\Kount\Model\RisService;

class Registration
{
    const SITE_NEW_ACC = 'NEWACCT';
    const PRODUCT_TYPE = 'qwerty';
    const PRODUCT_ITEM_NAME = 'standard';
    const PRODUCT_DESCRIPTION = 'New account';
    const PRODUCT_QTY = 1;
    const PRODUCT_PRICE = 0;
    const TOTL = 0;

    /**
     * @var \Kount\Kount\Model\Ris\InquiryFactory
     */
    private $inquiryFactory;

    /**
     * @var \Kount\Kount\Model\Ris\Base\Builder\Session
     */
    private $sessionBuilder;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Kount\Kount\Model\Ris\Inquiry\Builder\Order\CartItemFactory
     */
    private $cartItemFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Kount\Kount\Model\Ris\InquiryFactory $inquiryFactory
     * @param \Kount\Kount\Model\Ris\Base\Builder\Session $sessionBuilder
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Kount\Kount\Model\Ris\Inquiry\Builder\Order\CartItemFactory $cartItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Kount\Kount\Model\Ris\InquiryFactory $inquiryFactory,
        \Kount\Kount\Model\Ris\Base\Builder\Session $sessionBuilder,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Kount\Kount\Model\Ris\Inquiry\Builder\Order\CartItemFactory $cartItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->inquiryFactory = $inquiryFactory;
        $this->sessionBuilder = $sessionBuilder;
        $this->remoteAddress = $remoteAddress;
        $this->cartItemFactory = $cartItemFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Build request body
     *
     * @param $customerEmail
     * @param string $auth
     * @param string $mack
     * @return \Kount_Ris_Request_Inquiry
     * @throws \Kount_Ris_IllegalArgumentException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build($customerEmail, $auth = RisService::AUTH_AUTHORIZED, $mack = RisService::MACK_NO)
    {
        /** @var \Kount_Ris_Request_Inquiry $inquiry */
        $inquiry = $this->inquiryFactory->create($this->storeManager->getStore()->getWebsiteId());
        $inquiry->setWebsite(self::SITE_NEW_ACC);
        $inquiry->setAuth($auth);
        $inquiry->setMack($mack);
        $inquiry->setIpAddress($this->remoteAddress->getRemoteAddress());
        $this->sessionBuilder->process($inquiry);
        // Customer registration request should contains some order like params
        $cart[] = $this->cartItemFactory->create([
            'productType' => self::PRODUCT_TYPE,
            'itemName' => self::PRODUCT_ITEM_NAME,
            'description' => self::PRODUCT_DESCRIPTION,
            'quantity' => self::PRODUCT_QTY,
            'price' => self::PRODUCT_PRICE
        ]);
        $inquiry->setCart($cart);
        $inquiry->setNoPayment();
        $inquiry->setTotal(self::TOTL);
        $inquiry->setEmail($customerEmail);

        return $inquiry;
    }
}
