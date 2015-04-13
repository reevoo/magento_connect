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
class EComHut_Reevoo_Model_Observer {
    /*
     * Receives Order completion as observer and enables layout to save data, and set some values
     * @param object $observer Object of Varien_Event_Observer
     * @return NULL.
     */

    public function setOrderTracking($observer) {
        $this->_setOrderTracking($observer);
    }

    /*
     * Receives Order completion as observer and enables layout to save data, and set some values
     * @param object $observer Object of Varien_Event_Observer
     * @return NULL.
     */

    private function _setOrderTracking(Varien_Event_Observer $observer) {
		Mage::getModel('reevoo/data')->saveLog('purchase_tracking', 'orde_tracking_event', 'Success');
		$orderIds = $observer->getEvent()->getOrderIds();
		if (empty($orderIds) || !is_array($orderIds)) {
			echo 'orders are not available in varient events.';
			return;
		}
		$block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('reevoo_header_elements');
		if ($block) {
			$block->setOrderData($orderIds);
		}else{
			echo 'block not found...!';
		}
    }

}
