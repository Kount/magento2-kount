<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Sales\Model\Order;
use Swarming\Kount\Model\Order\Ris as OrderRis;

class InstallData implements InstallDataInterface
{
    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /* Add Kount order statuses */
        $status = [
            ['status' => OrderRis::STATUS_KOUNT_REVIEW , 'label' => __('Review')],
            ['status' => OrderRis::STATUS_KOUNT_DECLINE , 'label' => __('Decline')],
        ];
        $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $status);

        /* Attach Kount statuses to holded order state */
        $states = [
            ['status' => OrderRis::STATUS_KOUNT_REVIEW, 'state' => Order::STATE_HOLDED, 'is_default' => 0],
            ['status' => OrderRis::STATUS_KOUNT_DECLINE, 'state' => Order::STATE_HOLDED, 'is_default' => 0],
        ];
        $setup->getConnection()->insertArray(
            $setup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default'],
            $states
        );
    }
}
