<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Exception;

class ConfigException extends \Magento\Framework\Exception\LocalizedException
{
    const PHRASE = 'Login control is disabled in Config';

    /**
     * @param \Magento\Framework\Phrase|null $phrase
     * @param \Exception|null $cause
     * @param int $code
     */
    public function __construct(
        \Magento\Framework\Phrase $phrase = null,
        \Exception $cause = null,
        $code = 0
    ) {
        if ($phrase === null) {
            $phrase = new \Magento\Framework\Phrase(self::PHRASE);
        }
        parent::__construct($phrase, $cause, $code);
    }
}
