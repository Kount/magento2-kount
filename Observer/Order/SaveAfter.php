<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Observer\Order;

use Magento\Framework\Event\Observer;

class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Kount\Kount\Model\Order\Ris
     */
    protected $orderRis;

    /**
     * @param \Kount\Kount\Model\Order\Ris $orderRis
     */
    public function __construct(\Kount\Kount\Model\Order\Ris $orderRis)
    {
        $this->orderRis = $orderRis;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getData('order');
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes && $extensionAttributes->getKountRis()) {
            $ris = $extensionAttributes->getKountRis();
            $this->orderRis->linkRis($ris, $order);
        }
    }
}
