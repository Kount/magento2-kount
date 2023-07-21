<?php
/**
 * Copyright (c) 2023 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\KountControl\Exception;

class ChallengeApiResponse extends \Magento\Framework\Exception\LocalizedException
{
    const PHRASE = 'Challenge API response';

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
