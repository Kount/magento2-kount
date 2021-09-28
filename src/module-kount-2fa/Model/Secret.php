<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Kount\Kount2FA\Model\ResourceModel\Secret as SecretResourceModel;
use Kount\Kount2FA\Model\ResourceModel\Secret\Collection as SecretCollection;

/**
 * @method SecretResourceModel getResource()
 * @method SecretCollection getCollection()
 */
class Secret extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'kount_kount2fa_secret';
    protected $_cacheTag = 'kount_kount2fa_secret';
    protected $_eventPrefix = 'kount_kount2fa_secret';

    protected function _construct()
    {
        $this->_init('Kount\Kount2FA\Model\ResourceModel\Secret');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
