<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Ris\Inquiry\Builder\Order;

class CartItemFactory
{
    /**
     * @param array $data
     * @return \Kount_Ris_Data_CartItem
     */
    public function create($data)
    {
        return new \Kount_Ris_Data_CartItem(
            $data['productType'],
            $data['itemName'],
            $data['description'],
            $data['quantity'],
            $data['price']
        );
    }
}
