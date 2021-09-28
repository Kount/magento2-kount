<?php
/**
 * Copyright (c) 2021 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris\Inquiry\Builder;

class VersionInfo
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Kount\Kount\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Kount\Kount\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Kount\Kount\Helper\Data $helperData
    ) {
        $this->productMetadata = $productMetadata;
        $this->helperData = $helperData;
    }

    /**
     * @param \Kount_Ris_Request_Inquiry $request
     */
    public function process(\Kount_Ris_Request_Inquiry $request)
    {
        $request->setUserDefinedField('PLATFORM', $this->productMetadata->getEdition() . ':' . $this->productMetadata->getVersion());
        $request->setUserDefinedField('EXT', $this->helperData->getModuleVersion());
    }
}
