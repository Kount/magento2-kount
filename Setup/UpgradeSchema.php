<?php
/**
 * Copyright (c) 2020 KOUNT, INC.
 * See COPYING.txt for license details.
 */

namespace Swarming\Kount\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Swarming\Kount\Api\Data\RisInterface;
use Swarming\Kount\Model\ResourceModel\Ris as RisResource;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '4.2.2', '<')) {
            $this->addAdditionalResponseFields($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function addAdditionalResponseFields(SchemaSetupInterface $setup): void
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(RisResource::TABLE_NAME),
            RisInterface::OMNISCORE,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => '']
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(RisResource::TABLE_NAME),
            RisInterface::IP_ADDRESS,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => '']
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(RisResource::TABLE_NAME),
            RisInterface::IP_CITY,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => '']
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(RisResource::TABLE_NAME),
            RisInterface::NETW,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => '']
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(RisResource::TABLE_NAME),
            RisInterface::MOBILE_DEVICE,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => '']
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(RisResource::TABLE_NAME),
            RisInterface::MOBILE_TYPE,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['default' => '']
        );
    }
}
