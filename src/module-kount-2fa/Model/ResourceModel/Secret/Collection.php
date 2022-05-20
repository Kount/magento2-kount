<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Model\ResourceModel\Secret;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'secret_id';

    protected function _construct()
    {
        $this->_init('Kount\Kount2FA\Model\Secret', 'Kount\Kount2FA\Model\ResourceModel\Secret');
    }
}
