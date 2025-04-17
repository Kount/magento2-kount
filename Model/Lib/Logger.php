<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Lib;

class Logger implements \Kount_Log_Binding_Logger
{
    /**
     * @var \Kount\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isRisLogger;

    /**
     * @param \Kount\Kount\Model\Logger $logger
     * @param \Kount\Kount\Model\Config\Log $configLog
     */
    public function __construct(
        \Kount\Kount\Model\Logger $logger,
        \Kount\Kount\Model\Config\Log $configLog
    ) {
        $this->logger = $logger;
        $this->isRisLogger = $configLog->isRisMetricsEnabled();
    }

    /**
     * Log a debug level message.
     * @param string $message Message to log
     * @param \Exception $exception Exception to log
     * @return void
     */
    public function debug($message, $exception = null)
    {
        $this->logger->debug($message);
    }

    /**
     * Log an info level message.
     * @param string $message Message to log
     * @param \Exception $exception Exception to log
     * @return void
     */
    public function info($message, $exception = null)
    {
        $this->logger->info($message);
    }

    /**
     * Log a warn level message.
     * @param string $message Message to log
     * @param \Exception $exception Exception to log
     * @return void
     */
    public function warn($message, $exception = null)
    {
        $this->logger->warning($message);
    }

    /**
     * Log an error level message.
     * @param string $message Message to log
     * @param \Exception $exception Exception to log
     * @return void
     */
    public function error($message, $exception = null)
    {
        $this->logger->error($message);
    }

    /**
     * Log a fatal level message.
     * @param string $message Message to log
     * @param \Exception $exception Exception to log
     * @return void
     */
    public function fatal($message, $exception = null)
    {
        $this->logger->critical($message);
    }

    /**
     * Getter function for receiving the value for configurable ris metrics log.
     *
     * @return bool
     */
    public function getRisLogger()
    {
        return $this->isRisLogger;
    }
}
