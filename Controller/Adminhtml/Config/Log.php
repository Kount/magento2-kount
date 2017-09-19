<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Swarming\Kount\Model\Config\Log as ConfigLog;

class Log extends Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        return $this->fileFactory->create(
            ConfigLog::FILENAME,
            ['type' => 'filename', 'value' => 'log/' . ConfigLog::FILENAME, 'rm' => false],
            DirectoryList::VAR_DIR
        );
    }
}
