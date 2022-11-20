<?php
/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@Magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Customerprice
 * @copyright Copyright (c) 2017 Mage Delight (http://www.Magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@Magedelight.com>
 */

namespace Magedelight\Customerprice\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Upgrade the Partialpayment module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
            // Check if the table already exists
        if (!$installer->tableExists('md_categoryprice')) {
            $table = $installer->getConnection()
            ->newTable($installer->getTable('md_categoryprice'));
            $table->addColumn(
                'categoryprice_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                'identity' => true,
                'nullable' => false,
                'primary' => true
                ],
                'Category Price Id'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '10',
                ['unsigned' => true, 'nullable' => true],
                'Customer Id'
            )->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Customer Name'
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Customer Email'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                '10',
                ['unsigned' => true, 'nullable' => true],
                'Category ID'
            )->addColumn(
                'category_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Category Name'
            )
            ->addColumn(
                'discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['unsigned' => true, 'nullable' => true],
                'Discount ID'
            )
            ->setComment('Category Price link');
            $installer->getConnection()->createTable($table);
        }
        //echo "<pre>"; print_r($context->getVersion()); die("abc");

        if (version_compare($context->getVersion(), '1.4.8', '<')) {
            $installer->getConnection()->addIndex(
                $installer->getTable('customerprice'),
                $setup->getIdxName(
                    $installer->getTable('customerprice'),
                    ['customer_id', 'product_id', 'qty'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['customer_id', 'product_id', 'qty'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }
        $installer->endSetup();
    }
}
