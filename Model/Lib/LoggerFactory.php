<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Lib;

use Magento\Framework\App\ObjectManager;

class LoggerFactory implements \Kount_Log_Factory_LoggerFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Kount\Kount\Model\Lib\Settings
     */
    protected $libSettings;

    /**
     * @var string
     */
    protected $websiteId;

    /**
     * @var array
     */
    protected $loggerTypes = [
        'SIMPLE' => \Kount\Kount\Model\Lib\Logger::class,
        'NOP' => \Kount\Kount\Model\Lib\NopLogger::class
    ];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Kount\Kount\Model\Lib\Settings $libSettings
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Kount\Kount\Model\Lib\Settings $libSettings
    ) {
        $this->objectManager = $objectManager;
        $this->libSettings = $libSettings;
    }

    /**
     * @param string $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    /**
     * @return \Kount_Log_Binding_Logger
     */
    public function create()
    {
        $logger = $this->objectManager->create($this->getLoggerClass());
        if (!$logger instanceof \Kount_Log_Binding_Logger) {
            throw new \InvalidArgumentException(
                get_class($logger) . ' must be an instance of \Kount_Log_Binding_Logger.'
            );
        }
        return $logger;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getLoggerClass()
    {
        $loggerType = $this->libSettings->getConfigSetting('LOGGER', $this->websiteId);
        if (empty($this->loggerTypes[$loggerType])) {
            throw new \InvalidArgumentException("Unknown logger [{$loggerType}] defined.");
        }
        return $this->loggerTypes[$loggerType];
    }

    /**
     * @return \Kount\Kount\Model\Lib\LoggerFactory
     */
    protected static function getInstance()
    {
        return ObjectManager::getInstance()->get(self::class);
    }

    /**
     * @param string $name Name of logger
     * @return \Kount_Log_Binding_Logger
     */
    public static function getLogger($name)
    {
        return self::getInstance()->create();
    }
}
