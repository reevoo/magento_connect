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
 
class EComHut_Reevoo_Model_Adminhtml_System_Config_Source_Dropdown_Badges extends Mage_Core_Model_Config_Data {
    /*
     * Dropdown Options for possible FTP options.
     * @return array of FTP options.
     */
    public function toOptionArray() {
        return array(
            array(
                'value' => 'product_badge',
                'label' => 'Product Badges',
            ),
            array(
                'value' => 'owner_badge',
                'label' => 'Ask an owner Badges',
            )
        );
    }

}
