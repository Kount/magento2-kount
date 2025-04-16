<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Inquiry\Builder;

class VersionInfo
{
    const SDK_VALUE = 'CUST';

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Kount\Kount\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $moduleResource;

    /**
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Kount\Kount\Helper\Data $helperData
     * @param \Magento\Framework\Module\ResourceInterface $moduleResource
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Kount\Kount\Helper\Data $helperData,
        \Magento\Framework\Module\ResourceInterface $moduleResource
    ) {
        $this->productMetadata = $productMetadata;
        $this->helperData = $helperData;
        $this->moduleResource = $moduleResource;
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     */
    public function process(\Kount_Ris_Request_Inquiry $request)
    {
        $request->setUserDefinedField(
            'PLATFORM',
            $this->productMetadata->getEdition() . ':' . $this->productMetadata->getVersion()
        );
        $request->setUserDefinedField('EXT', $this->helperData->getModuleVersion());
        $request->setParm('SDK', self::SDK_VALUE);
        $request->setParm(
            'SDK_VERSION',
            sprintf('TPA-Magento-%s', $this->moduleResource->getDataVersion($this->helperData->getModuleName()))
        );
    }
}
