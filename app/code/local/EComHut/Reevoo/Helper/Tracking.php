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
class EComHut_Reevoo_Helper_Tracking extends Mage_Core_Helper_Abstract {
    /*
     * Returns configuration value.
     * @param string $req value name to acquire the configuration value.
     * @param string $custom parent reference if its not related to API.
     * @return value of inquired configuration.
     */

    const CONFIG_PATH = 'reevoo_setup/reevoo_tracking/';

    function getValue($req, $custom = false) {
        try {
            if ($custom === false) {
                $custom = self::CONFIG_PATH;
            }
            return Mage::getStoreConfig($custom . $req, Mage::app()->getStore());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns full status of Tracking (after verifying extension status
     * @return true or false based on admin setting.
     */

    function getStatus($st = 'tracking_status') {
        try {
            $reevoo_status = $this->getValue('status', 'reevoo_setup/reevoo_status/');
            $widget_status = $this->getValue($st);
            if ($reevoo_status && $widget_status) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
