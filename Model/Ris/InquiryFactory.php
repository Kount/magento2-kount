<?php
/**
 * Copyright (c) 2025 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model\Ris;

class InquiryFactory
{
    /**
     * @var \Kount\Kount\Model\Lib\Settings
     */
    protected $libSettings;

    /**
     * @param \Kount\Kount\Model\Lib\Settings $libSettings
     */
    public function __construct(
        \Kount\Kount\Model\Lib\Settings $libSettings
    ) {
        $this->libSettings = $libSettings;
    }

    /**
     * @param string|null $websiteCode
     * @return \Kount_Ris_Request_Inquiry
     */
    public function create($websiteCode = null)
    {
        $settings = new \Kount_Ris_ArraySettings($this->libSettings->getSettings($websiteCode));
        return new \Kount_Ris_Request_Inquiry($settings);
    }
}
