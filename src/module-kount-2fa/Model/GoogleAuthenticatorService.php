<?php
/**
 * Copyright (c) 2022 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Model;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Kount\Kount2FA\Lib\PHPGangsta\GoogleAuthenticator;

class GoogleAuthenticatorService extends GoogleAuthenticator
{
    /**
     * Get QR Code Image.
     *
     * @param string $name
     * @param string $secret
     * @param null $title
     * @param array $params
     * @return string
     */
    public function getQrCodeEndroid(string $name, string $secret, $title = null, $params = [])
    {
        $size = !empty($params['size']) && (int) $params['size'] > 0 ? (int) $params['size'] : 200;

        $text = sprintf('otpauth://totp/%s?secret=%s', $name, $secret);
        if (true === is_string($title)) {
            $text = sprintf('%s&issuer=%s', $text, $title);
        }
        $writer = new PngWriter();
        $qrCode = new QrCode($text);
        $qrCode->setData($text);
        $qrCode->setSize($size);
        $qrCode->setMargin(0);
        $qrCode->setEncoding(new Encoding('UTF-8'));
        $qrCode->setSize($size);
        return $writer->write($qrCode)->getString();
    }
}
