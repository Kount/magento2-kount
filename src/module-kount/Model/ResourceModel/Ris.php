<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\ResourceModel;

class Ris extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'kount_ris';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('kount_ris', 'ris_id');
    }
}
