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
class EComHut_Reevoo_Helper_Api extends Mage_Core_Helper_Abstract {
    /*
     * Initializes class ReevooMark and returns the class as object.
     * @param string $trkrefs (optional) tracking reference.
     * @param string $cache_path (optional) path for cache.
     * @return Class object for ReevooMark;
     */

    private $_resource;

    public function getReevooApi($trkrefs = '', $cache_path = '') {
        try {
            if ($this->getStatus() === false) {
                return false;
            }
            /**
              if (isset($this->_resource) && is_a($this->_resource, 'ReevooMark')) {
              return $this->_resource;
              }
              /* */
            if (strlen($trkrefs) < 1) {
                $trkrefs = $this->getValue('tracking_reference'); //Mage::getStoreConfig('reevoo_setup/reevoo_status/tracking_reference');
            }
            if (strlen($cache_path) < 1) {
                $cache_path = Mage::getBaseDir('cache');
            }
            require_once(Mage::getBaseDir('lib') . DS . "Reevoo" . DS . "reevoo_mark.php");
            return $this->_resource = new ReevooMark($trkrefs, $cache_path);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    const CONFIG_PATH = 'reevoo_setup/reevoo_status/';

    /*
     * Returns full status of API (after verifying extension status
     * @return true or false based on admin setting.
     */

    function getStatus() {
        try {
            $reevoo_status = $this->getValue('status');
            if ($reevoo_status) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns configuration value.
     * @param string $req value name to acquire the configuration value.
     * @param string $custom parent reference if its not related to API.
     * @return value of inquired configuration.
     */

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
     * Returns configuration value from multiselect field.
     * @param string $req value name to acquire the configuration value.
     * @param string $custom parent reference if its not related to API.
     * @param string $questioned i.e. which one to check.
     * @return value of inquired configuration.
     */

    function getMultiSelectValue($req, $custom = false, $questioned = false) {
        try {
            if ($custom === false) {
                $custom = self::CONFIG_PATH;
            }
            $value = Mage::getStoreConfig($custom . $req, Mage::app()->getStore());
            if (strlen($value) > 0) {
                $value = explode(",", $value);
            }
            if (is_array($value) && count($value) > 0) {
                foreach ($value as $output) {
                    if ($output == $questioned) {
                        return true;
                    }
                }
            }
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
