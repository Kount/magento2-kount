<?php
/**
 * Copyright (c) 2017 KOUNT, INC.
 * See COPYING.txt for license details.
 */
namespace Swarming\Kount\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->encryptApiKeyValue($setup, 'swarming_kount/account/api_key_production');
            $this->encryptApiKeyValue($setup, 'swarming_kount/account/api_key_test');
        }

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param string $configPath
     * @return void
     */
    protected function encryptApiKeyValue(ModuleDataSetupInterface $setup, $configPath)
    {
        $select = $setup->getConnection()->select()
            ->from($setup->getTable('core_config_data'), ['config_id', 'value'])
            ->where('path = ?', $configPath);

        foreach ($setup->getConnection()->fetchAll($select) as $configRow) {
            $setup->getConnection()->update(
                $setup->getTable('core_config_data'),
                ['value' => $this->encryptor->encrypt($configRow['value'])],
                ['config_id = ?' => $configRow['config_id']]
            );
        }
    }
}
