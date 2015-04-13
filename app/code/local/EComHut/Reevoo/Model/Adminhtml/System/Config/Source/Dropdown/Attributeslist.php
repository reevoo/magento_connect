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
class EComHut_Reevoo_Model_Adminhtml_System_Config_Source_Dropdown_Attributeslist extends Mage_Core_Model_Config_Data {
    /*
     * Product Attributes type media_image.
     * @return array of attributes list
     */

    public function toOptionArray() {
        return $this->__getAttributes();
    }

    /*
     * Product Attributes type media_image for SKU.
     * @return array of attributes list
     */

    private function __getAttributes() {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addFieldToFilter('frontend_input', array('eq' => 'media_image'))  // We filter to only select data type media_image
                ->getItems();

        $data = array();
        foreach ($attributes as $attribute) {
            if (strlen($attribute->getFrontendLabel()) > 0) {
                $data[] = array(
                    'value' => $attribute->getAttributecode(),
                    'label' => $attribute->getFrontendLabel()
                );
            }
        }
        return $data;
    }

}
