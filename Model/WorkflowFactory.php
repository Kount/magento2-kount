<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model;

use Swarming\Kount\Model\Config\Source\WorkflowMode;

class WorkflowFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $workflowModes = [
        WorkflowMode::MODE_PRE_AUTH => \Swarming\Kount\Model\Workflow\PreAuth::class,
        WorkflowMode::MODE_POST_AUTH => \Swarming\Kount\Model\Workflow\PostAuth::class
    ];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $mode
     * @return \Swarming\Kount\Model\WorkflowInterface
     * @throws \InvalidArgumentException
     */
    public function create($mode)
    {
        if (empty($this->workflowModes[$mode])) {
            throw new \InvalidArgumentException("{$mode}: isn't allowed as Kount workflow mode");
        }

        $workflow = $this->objectManager->create($this->workflowModes[$mode]);
        if (!$workflow instanceof WorkflowInterface) {
            throw new \InvalidArgumentException(get_class($workflow) . ' must be an instance of \Swarming\Kount\Model\WorkflowInterface.');
        }
        return $workflow;
    }
}
