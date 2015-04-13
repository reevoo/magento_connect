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
class EComHut_Reevoo_Block_Customerexperience extends Mage_Core_Block_Template {
    /*
     * Checks Reevoo Status
     * @return boolean
     */

    public function getStatus() {
        try {
            return Mage::getStoreConfig('reevoo_setup/reevoo_status/status', Mage::app()->getStore());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Retrieves reevoo API object
     * @return object of Reevoo api.
     */

    public function getExperience() {
        try {
            $_reevoo = Mage::helper('reevoo/api');
            return $_reevoo->getReevooApi();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
