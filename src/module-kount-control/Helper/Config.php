<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Helper;

class Config extends \Kount\Kount\Model\Config\Account
{
    const XML_IS_LOGIN_SERVICE_ENABLED = 'kount_kount_control/general/login_enabled';
    const XML_IS_TRUSTED_DEVICE_ENABLED = 'kount_kount_control/general/trusted_device_enabled';
    const XML_2FA_FAILED_ATTEMPTS_AMOUNT = 'kount_kount_control/general/2fa_failed_attempts_amount';
    const XML_IS_SIGNUP_ENABLED = 'kount_kount_control/general/sign_up_enabled';
    const XML_API_KEY = 'kount_kount_control/general/api_key';

    /**
     * @return bool
     */
    public function isLoginServiceEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_IS_LOGIN_SERVICE_ENABLED);
    }

    /**
     * @return bool
     */
    public function isTrustedDeviceEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_IS_TRUSTED_DEVICE_ENABLED);
    }

    /**
     * @return int
     */
    public function get2faFailedAttemptsAmount()
    {
        return (int) $this->scopeConfig->getValue(self::XML_2FA_FAILED_ATTEMPTS_AMOUNT);
    }

    /**
     * @return bool
     */
    public function isSignupEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_IS_SIGNUP_ENABLED);
    }

    /**
     * @return string
     */
    public function getControlApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_API_KEY);
    }
}
