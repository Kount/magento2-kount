<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Model;

use Swarming\Kount\Model\Order\ActionFactory as OrderActionFactory;

abstract class WorkflowAbstract implements WorkflowInterface
{
    /**
     * @var \Swarming\Kount\Model\Config\Workflow
     */
    protected $configWorkflow;

    /**
     * @var \Swarming\Kount\Model\RisService
     */
    protected $risService;

    /**
     * @var \Swarming\Kount\Model\Order\ActionFactory
     */
    protected $orderActionFactory;

    /**
     * @var \Swarming\Kount\Model\Order\Ris
     */
    protected $orderRis;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Swarming\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Swarming\Kount\Model\Config\Workflow $configWorkflow
     * @param \Swarming\Kount\Model\RisService $risService
     * @param \Swarming\Kount\Model\Order\ActionFactory $orderActionFactory
     * @param \Swarming\Kount\Model\Order\Ris $orderRis
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Swarming\Kount\Model\Logger $logger
     */
    public function __construct(
        \Swarming\Kount\Model\Config\Workflow $configWorkflow,
        \Swarming\Kount\Model\RisService $risService,
        \Swarming\Kount\Model\Order\ActionFactory $orderActionFactory,
        \Swarming\Kount\Model\Order\Ris $orderRis,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Swarming\Kount\Model\Logger $logger
    ) {
        $this->configWorkflow = $configWorkflow;
        $this->risService = $risService;
        $this->orderActionFactory = $orderActionFactory;
        $this->orderRis = $orderRis;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    protected function updaterOrderStatus($order)
    {
        $kountRisResponse = $this->orderRis->getRis($order)->getResponse();
        switch ($kountRisResponse) {
            case RisService::AUTO_DECLINE:
                $this->orderActionFactory->create(OrderActionFactory::DECLINE)->process($order);
                break;
            case RisService::AUTO_REVIEW:
            case RisService::AUTO_ESCALATE:
                $this->orderActionFactory->create(OrderActionFactory::REVIEW)->process($order);
                break;
        }

        $this->orderRepository->save($order);
    }
}
