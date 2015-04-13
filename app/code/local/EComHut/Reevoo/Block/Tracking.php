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
class EComHut_Reevoo_Block_Tracking extends Mage_Core_Block_Template {

    public function getStatus() {
        try {
            return Mage::getStoreConfig('reevoo_setup/reevoo_status/status', Mage::app()->getStore());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function getExperience() {
        try {
            $_reevoo = Mage::helper('reevoo/api');
            if ($_reevoo->getStatus() === false) {
                return false;
            }
            return $_reevoo->getReevooApi();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns executes purchase tracking on final event.
     * @return null.
     */

    public function _getTrackingPurchase() {
        try {
            $orderIds = $this->getOrderData();
            if (empty($orderIds) || !is_array($orderIds)) {
                return;
            }
            $collection = Mage::getResourceModel('sales/order_collection')
                    ->addFieldToFilter('entity_id', array('in' => $orderIds));

            $_reevoo = Mage::helper('reevoo/api');
            if ($_reevoo->getStatus() === false) {
                return false;
            }
            $reevooMark = $_reevoo->getReevooApi();
            $sku = array();
            $total = '';
            foreach ($collection as $order) {
                $total = $order->getBaseGrandTotal();
                foreach ($order->getAllVisibleItems() as $item) {
                    $sku[] = $item->getSku();
                }
            }
            $reevooMark->purchaseTrackingEvent(array("skus" => implode(',', $sku), "value" => $total));
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function _getPropensityTracking($sku = false, $trackingMessage = 'Product Page') {
        try {
            $_reevoo = Mage::helper('reevoo/api');
            if ($_reevoo->getStatus() === false) {
                return false;
            }
            $reevooMark = $_reevoo->getReevooApi();
            if ($sku !== false) {
                $reevooMark->propensityToBuyTrackingEvent(array("action" => $trackingMessage, "sku" => $sku));
            } else {
                $reevooMark->propensityToBuyTrackingEvent(array("action" => $trackingMessage));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
