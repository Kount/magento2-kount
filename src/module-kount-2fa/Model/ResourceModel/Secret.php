<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Secret extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('kount_kount2fa_secrets', 'secret_id');
    }
}
