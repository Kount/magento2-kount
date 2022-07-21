<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Order;

interface ActionInterface
{
    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function process($order);
}
