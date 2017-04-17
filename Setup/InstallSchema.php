<?php

namespace Gabrielqs\TransactionalEmails\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use \Magento\Framework\DB\Ddl\Table;


class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'transactionalemails_log'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('transactionalemails_email')
        )->addColumn(
            'email_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Transactional Emails ID'
        )->addColumn(
            'from',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'From'
        )->addColumn(
            'to',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'To'
        )->addColumn(
            'bcc',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Bcc'
        )->addColumn(
            'subject',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Subject'
        )->addColumn(
            'body',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Body'
        )->addColumn(
            'request',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Request'
        )->addColumn(
            'response',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Response'
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true],
            'Order ID'
        )->addColumn(
            'status',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Status'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true],
            'Customer ID'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Email Creation Time'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('transactionalemails_email'),
                ['from'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['from'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('transactionalemails_email'),
                ['to'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['to'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('transactionalemails_email'),
                ['order_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['order_id'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('transactionalemails_email'),
                ['status'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['status'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('transactionalemails_email'),
                ['customer_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['customer_id'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('transactionalemails_email'),
                ['creation_time'],
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['creation_time'],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addForeignKey(
            $installer->getFkName('transactionalemails_email', 'order_id',
                $installer->getTable('sales_order'), 'entity_id'),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('transactionalemails_email', 'customer_id',
                $installer->getTable('customer_entity'), 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Transactional Emails - Email Table'
        );
        $installer->getConnection()->createTable($table);
    }
}