<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Controller\DataCollector;

use Magento\Framework\Controller\ResultFactory;

class Gif extends \Swarming\Kount\Controller\DataCollector
{
    const MODE_GIF = 'gif';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->logger->info('Data collector gif action.');

        if (!$this->isAvailable() || $this->isIpWhite()) {
            return $this->resultFactory->create(ResultFactory::TYPE_RAW);
        }

        $this->kountSession->incrementKountSessionId();

        $this->logger->info('Setting Kount session ID: ' . $this->kountSession->getKountSessionId());

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->generateUrl(self::MODE_GIF));
        return $resultRedirect;
    }
}
