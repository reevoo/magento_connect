<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade reevoo to newer
 * versions in the future. If you wish to customize the code for your
 * needs please refer to https://github.com/reevoo/ for more information.
 *
 * @category    Reeovoo Magento Connect
 * @package     Com_Reevoo
 * @copyright   Copyright (c) 20015 Reevoo Limited. (www.reevoo.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reevoo Package Module.
 *
 * @category   Reeovoo Magento Connect
 * @package    Com_Reevoo
 * @author     Haroon Chaudhry<haroon.chaudhry@innovadeltech.com>
 */
$installer = $this;
$installer->startSetup();

/* * Slightly buggy and unsupported for some reasons. **
  $table = $installer->getConnection()->newTable($installer->getTable('reevoo/logs'))
  ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
  'unsigned' => true,
  'nullable' => false,
  'primary' => true,
  'identity' => true,
  ), 'Primary ID of the table.')
  ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
  'nullable' => true,
  ), 'Type of the log')
  ->addColumn('event', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
  'nullable' => true,
  ), 'Event name i.e. start, stop etc')
  ->addColumn('event_message', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
  ), 'Event Details or Message to be saved with event')
  ->addColumn('event_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
  ), 'Timestamp')
  ->setComment('eComHut base logger');

  $installer->getConnection()->createTable($table);
  /**
 * Alternative syntax as direct SQL.
 */
/**/
$table = "ecomhut_logs";
$tableName = Mage::getSingleton('core/resource')->getTableName($table);
$installer->run("CREATE TABLE IF NOT EXISTS `$tableName` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(256) NOT NULL DEFAULT '',
  `event` varchar(256) NOT NULL,
  `event_message` tinytext NOT NULL,
  `event_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");
/**/
$installer->endSetup();
