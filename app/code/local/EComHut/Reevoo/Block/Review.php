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
class EComHut_Reevoo_Block_Review extends Mage_Review_Block_Helper {
    /*
     * Checks if extension is enabled than adds short summy phtml file.
     * @param array $args All possible arguments for Mage_Review_Block_Helper
     * @return value of inquired configuration.
     */

    public function __construct(array $args = array()) {
        try {
            if (Mage::getStoreConfig('reevoo_setup/reevoo_status/status', Mage::app()->getStore())) {
                $this->_availableTemplates = array(
                    'default' => 'reevoo/summary.phtml',
                    'short' => 'reevoo/summary_short.phtml'
                );
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        parent::__construct($args);
    }

}
