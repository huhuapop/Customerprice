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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table1 = $installer->getConnection()->newTable(
            $installer->getTable('customerprice')
        )->addColumn(
            'customerprice_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Customer Price Id'
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
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '10',
            ['unsigned' => true, 'nullable' => true],
            'Product ID'
        )->addColumn(
            'product_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Product Name'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Price'
        )->addColumn(
            'log_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Log Price'
        )->addColumn(
            'new_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'New Price'
        )->addColumn(
            'qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '10',
            ['unsigned' => true, 'nullable' => true],
            'Quantity'
        );
        $installer->getConnection()->createTable($table1);

        $table2 = $installer->getConnection()->newTable(
            $installer->getTable('customerprice_discount')
        )->addColumn(
            'discount_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Discount Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '10',
            ['unsigned' => true, 'nullable' => true],
            'Customer ID'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'value'
        );
        $installer->getConnection()->createTable($table2);

        $connection = $installer->getConnection();

        $installer->endSetup();
    }
}
