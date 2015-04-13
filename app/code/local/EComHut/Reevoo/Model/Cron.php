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
class EComHut_Reevoo_Model_Cron {
    /*
     * Initialization
     * @return NULL
     */

    protected function _construct() {
        $this->_init('reevoo/data');
    }

    /*
     * Processes Products Feed Collection.
     * @return CSV filename of the processed cron task.
     */

    function processProductsFeed() {
        try {
            $filename = Mage::getModel('reevoo/data')->cronProcessProducts();
            Mage::helper('reevoo/ftp')->setTransfer($filename);
            Mage::getModel('reevoo/data')->__dispatchAfterUpload(); // Will delete file after offering download
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /*
     * Processes Purchase Feed Collection.
     * @return CSV of the processed cron task.
     */

    function processPurchaserFeed() {
        try {
            $filename = Mage::getModel('reevoo/data')->cronPurchaseOrders();
            Mage::helper('reevoo/ftp')->setTransfer($filename);
            Mage::getModel('reevoo/data')->__dispatchAfterUpload(); // Will delete file after offering download
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
        return $this;
    }

}
