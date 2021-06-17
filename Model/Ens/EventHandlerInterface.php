<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ens;

interface EventHandlerInterface
{
    /**
     * @param \Magento\Framework\Simplexml\Element $event
     */
    public function process($event);
}
