<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model\Config\Source;

class WorkflowMode implements \Magento\Framework\Option\ArrayInterface
{
    const MODE_PRE_AUTH = 'pre_auth';
    const MODE_POST_AUTH = 'post_auth';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MODE_PRE_AUTH, 'label' => __('Pre-Authorization Payment Review')],
            ['value' => self::MODE_POST_AUTH, 'label' => __('Post-Authorization Payment Review')]
        ];
    }
}
