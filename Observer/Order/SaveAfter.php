<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Observer\Order;

use Magento\Framework\Event\Observer;

class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\Kount\Model\Order\Ris
     */
    protected $orderRis;

    /**
     * @param \Swarming\Kount\Model\Order\Ris $orderRis
     */
    public function __construct(\Swarming\Kount\Model\Order\Ris $orderRis)
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
