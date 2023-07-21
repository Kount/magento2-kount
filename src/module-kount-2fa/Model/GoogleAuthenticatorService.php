<?php
/**
 * Copyright (c) 2023 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Kount\Kount2FA\Model;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Kount\Kount2FA\Lib\PHPGangsta\GoogleAuthenticator;
use Magento\Framework\App\ProductMetadataInterface;

class GoogleAuthenticatorService extends GoogleAuthenticator
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetaData;

    public function __construct(ProductMetadataInterface $productMetaData)
    {
        $this->productMetaData = $productMetaData;
    }

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
        $size = !empty($params['size']) && (int)$params['size'] > 0 ? (int)$params['size'] : 200;

        $text = sprintf('otpauth://totp/%s?secret=%s', $name, $secret);
        if (true === is_string($title)) {
            $text = sprintf('%s&issuer=%s', $text, $title);
        }
        $writer = new PngWriter();
        $qrCode = new QrCode($text);

        if (strpos($this->productMetaData->getVersion(), '2.4.3') === false) {
            $qrCode->setData($text);
            $qrCode->setSize($size);
            $qrCode->setMargin(0);
            $qrCode->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'));
            $qrCode->setSize($size);
            return $writer->write($qrCode)->getString();
        } else {
            $qrCode->setWriterByName('png');
            $qrCode->setText($text);
            $qrCode->setSize($size);
            $qrCode->setMargin(0);
            $qrCode->setEncoding('UTF-8');
            $qrCode->setSize($size);
            return $writer->writeString($qrCode);
        }
    }
}
