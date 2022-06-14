<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Module\PackageInfo
     */
    protected $packageInfo;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\PackageInfo $packageInfo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\PackageInfo $packageInfo
    ) {
        $this->packageInfo = $packageInfo;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->packageInfo->getVersion($this->getModuleName());
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->_getModuleName();
    }
}
