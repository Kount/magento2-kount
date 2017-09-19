<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Logger\Handler;

use Monolog\Logger;

class Kount extends \Magento\Framework\Logger\Handler\System
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/kount.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
