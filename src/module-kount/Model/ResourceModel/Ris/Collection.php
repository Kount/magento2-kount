<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\ResourceModel\Ris;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Kount\Kount\Model\Ris::class, \Kount\Kount\Model\ResourceModel\Ris::class);
    }
}
