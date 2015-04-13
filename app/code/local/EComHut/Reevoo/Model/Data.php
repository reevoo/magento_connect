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
class EComHut_Reevoo_Model_Data extends Mage_Core_Model_Abstract {
    /*
     * Initializing constructor
     * @return Null
     */

    protected function _construct() {
        $this->_init('reevoo/data');
    }

    /*
     * Saves log into database.
     * @param string $type Type of the event log.
     * @param string $event Secret name of the event.
     * @param string $event_message Message to be saved in database.
     * @return Null
     */

    public function saveLog($type, $event, $event_message) {
        try {
            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');
            $table = "ecomhut_logs";
            $tableName = Mage::getSingleton('core/resource')->getTableName($table);
            $sql = "INSERT INTO `$tableName` (`type`, `event`, `event_message`) VALUES ('{$type}', '{$event}', '{$event_message}')";
            $writeConnection->query($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Main linked processor for processing products feed in cron.
     * @return Filename of the generated CSV.
     */

    public function cronProcessProducts() {
        try {
            $this->saveLog('cron_log', 'product_cronjob_start', 'Starting Cron for Products feed');
            $_helper = Mage::helper('reevoo');
            $data = $_helper->getProducts();
            $filename = $_helper->saveProductsCsv($data);
            $message = 'Processing done. Please verify from: ' . $filename . '. Time: ' . time();
            $this->saveLog('cron_log', 'product_cronjob_finished', $message);
            return $filename;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Main linked processor for purchase feed in cron.
     * @return filename of processed csv file.
     */

    public function cronPurchaseOrders() {
        try {
            $this->saveLog('cron_log', 'purchasefeed_cronjob_start', 'Starting Cron for Purchaseorders feed');
            $_helper = Mage::helper('reevoo');
            $data = $_helper->getPurchaseOrders();
            $filename = $_helper->savePurchaseOrdersCsv($data);
            $message = 'Processing done. Please verify from: ' . $filename . '. Time: ' . time();
            $this->saveLog('cron_log', 'purchasefeed_cronjob_finished', $message);
            return $filename; //generateCsv($fileName, $assocDataArray);
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /*
     * Returns last product feed cron time (used in delta cases)
     * @return timestap of last cron execution
     */

    public function getLastProductFeedDate() {
        try {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $table = "ecomhut_logs";
            $tableName = Mage::getSingleton('core/resource')->getTableName($table);
            $sql = "SELECT timestamp(`event_time`) as `event_time` FROM `$tableName` WHERE `event` = 'product_cronjob_finished' ORDER BY `event_time` DESC LIMIT 1";
            return $readConnection->fetchOne($sql);
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /*
     * Returns last purchase feed cron time (used in delta cases)
     * @return timestap of last cron execution
     */

    public function getLastPurchaseFeedDate() {
        try {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $table = "ecomhut_logs";
            $tableName = Mage::getSingleton('core/resource')->getTableName($table);
            $sql = "SELECT timestamp(`event_time`) as `event_time` FROM `$tableName` WHERE `event` = 'purchasefeed_cronjob_finished' ORDER BY `event_time` DESC LIMIT 1";
            return $readConnection->fetchOne($sql);
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /*
     *  Operates dispatch operations. Will delete the csv file after uploading.
     *  @return null; 
     */

    public function __dispatchAfterUpload() {
        $_helper = Mage::getHelper('reevoo/ftp');
        if ($_helper->ftp_status) {
            if (unlink($_helper->filename)) {
                $this->saveLog('cron_log', '__dispatchAfterUpload', "Unlink file successful: " . $_helper->filename);
            } else {
                $this->saveLog('cron_log', '__dispatchAfterUpload', "Unlink file permission denied: " . $_helper->filename);
            }
        }
    }

}
