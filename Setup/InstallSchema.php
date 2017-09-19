<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Swarming\Kount\Api\Data\RisInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable($setup->getTable('swarming_kount_ris'));
        $table->addColumn(
            RisInterface::RIS_ID, Table::TYPE_INTEGER,
            null,
            ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
            'Id'
        );
        $table->addColumn(RisInterface::ORDER_ID, Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Order Id');
        $table->addColumn(RisInterface::SCORE, Table::TYPE_TEXT, 10, [], 'Ris Score');
        $table->addColumn(RisInterface::RESPONSE, Table::TYPE_TEXT, 1, [], 'Ris Response');
        $table->addColumn(RisInterface::RULE, Table::TYPE_TEXT, null, [], 'Ris Rule');
        $table->addColumn(RisInterface::DESCRIPTION, Table::TYPE_TEXT, null, [], 'Ris Description');
        $table->addColumn(RisInterface::TRAN, Table::TYPE_TEXT, 15, [], 'Ris Transaction Id');
        $table->addColumn(RisInterface::GEOX, Table::TYPE_TEXT, 15, [], 'Ris GEOX');
        $table->addColumn(RisInterface::COUNTRY, Table::TYPE_TEXT, 15, [], 'Ris DVCC');
        $table->addColumn(RisInterface::KAPTCHA, Table::TYPE_TEXT, 15, [], 'Ris KAPT');
        $table->addColumn(RisInterface::CARDS, Table::TYPE_TEXT, 15, [], 'Ris CARDS');
        $table->addColumn(RisInterface::EMAILS, Table::TYPE_TEXT, 15, [], 'Ris EMAILS');
        $table->addColumn(RisInterface::DEVICES, Table::TYPE_TEXT, 15, [], 'Ris DEVICES');
        $table->addIndex($setup->getIdxName('swarming_kount_ris', [RisInterface::ORDER_ID]), [RisInterface::ORDER_ID]);
        $table->setComment('Kount Ris Data');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
