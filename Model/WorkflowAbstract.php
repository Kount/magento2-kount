<?php
/**
 * Copyright (c) 2024 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Kount\Kount\Model;

use Kount\Kount\Model\Order\ActionFactory as OrderActionFactory;

abstract class WorkflowAbstract implements WorkflowInterface
{
    /**
     * @var \Kount\Kount\Model\Config\Workflow
     */
    protected $configWorkflow;

    /**
     * @var \Kount\Kount\Model\RisService
     */
    protected $risService;

    /**
     * @var \Kount\Kount\Model\Order\ActionFactory
     */
    protected $orderActionFactory;

    /**
     * @var \Kount\Kount\Model\Order\Ris
     */
    protected $orderRis;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Kount\Kount\Model\Logger
     */
    protected $logger;

    /**
     * @param \Kount\Kount\Model\Config\Workflow $configWorkflow
     * @param \Kount\Kount\Model\RisService $risService
     * @param \Kount\Kount\Model\Order\ActionFactory $orderActionFactory
     * @param \Kount\Kount\Model\Order\Ris $orderRis
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Kount\Kount\Model\Logger $logger
     */
    public function __construct(
        \Kount\Kount\Model\Config\Workflow $configWorkflow,
        \Kount\Kount\Model\RisService $risService,
        \Kount\Kount\Model\Order\ActionFactory $orderActionFactory,
        \Kount\Kount\Model\Order\Ris $orderRis,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Kount\Kount\Model\Logger $logger
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
