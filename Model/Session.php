<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model;

class Session extends \Magento\Framework\Session\SessionManager
{
    const KOUNT_SESSION_ID = 'kount_session_id';

    /**
     * @return $this
     */
    public function incrementKountSessionId()
    {
        $kountSessionId = hash('sha512', rand(0, 100000) . '-' . microtime());
        $this->storage->setData(self::KOUNT_SESSION_ID, $kountSessionId);

        return $this;
    }

    /**
     * @return string
     */
    public function getKountSessionId()
    {
        if (empty($this->storage->getData(self::KOUNT_SESSION_ID))) {
            $this->incrementKountSessionId();
        }

        return $this->storage->getData(self::KOUNT_SESSION_ID);
    }
}
