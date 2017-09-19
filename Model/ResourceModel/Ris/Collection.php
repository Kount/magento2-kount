<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\ResourceModel\Ris;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Swarming\Kount\Model\Ris::class, \Swarming\Kount\Model\ResourceModel\Ris::class);
    }
}
