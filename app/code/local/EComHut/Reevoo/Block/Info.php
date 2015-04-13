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
class EComHut_Reevoo_Block_Info extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = $this->_getHeaderHtml($element);
        $html.= $this->_getFieldHtml($element);
        $html .= $this->_getFooterHtml($element);
        return $html;
    }

    protected function _getFieldHtml($fieldset) {
        $content = 'This module is developed by <a href="http://innovadeltech.com" target="_blank">Innovadel Technologies Limited</a>.';
        return $content;
    }

}
