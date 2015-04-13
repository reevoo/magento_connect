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
class EComHut_Reevoo_Helper_Producthelper extends Mage_Core_Helper_Abstract {
    /*
     * Returns CSV as string.
     * @param array $assocDataArray associative array of expected CSV data.
     * @param array $createHeader to select indexes as header of csv. Default off.
     * @return CSV as a string.
     */

    function stringCsv($assocDataArray, $createHeader = false) {
        try {
            $data = '';
            if (isset($assocDataArray['0'])) {
                $fp = fopen("php://output", 'w');
                if ($createHeader) {
                    fputcsv($fp, array_keys($assocDataArray['0']));
                }
                foreach ($assocDataArray AS $values) {
                    fputcsv($fp, $values);
                }
                $data += stream_get_contents($fp);
                fclose($fp);
                return $data;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Creates CSV at specified path.
     * @param array $fileName Where the file is expected to be. Full path can also be used.
     * @param array $assocDataArray To create CSV based on.
     * @return null.
     */

    function generateCsv($fileName, $assocDataArray, $headers = true) {
        try {
            if (is_array($assocDataArray) && count($assocDataArray) > 0):
                $fp = fopen($fileName, 'w');
                if ($headers) {
                    $headers = array_keys($assocDataArray[0]);  // custom header
                    unset($assocDataArray[0]);
                    fputcsv($fp, $headers); // custom header
                }
                foreach ($assocDataArray as $fields) {

                    fputcsv($fp, $fields);
                }
                fclose($fp);
            endif;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Removes un-needed attributes per defined in extraIndexes function().
     * @param array $from associative array of product collection.
     * @return only matching attributes from the defined attributes list in extraIndexes() function.
     */

    function removeAttributes($from) {
        try {
            $attr = $this->extraIndexes();
            $data = array();

            if (count($from) > 0) {
                foreach ($from as $key => $value) {
                    if (is_array($value)) {
                        $data[$key] = $this->confirmIndexes($value, $attr);
                    }
                }
            }
            return $this->sortByAttributes($data, $attr);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Sort product collection attributes
     * @param array $from which requires sorting.
     * @param array $attr attributes list. Usually a reference to extraIndexes function is sufficient.
     * @return Sorted array of attributes.
     */

    function sortByAttributes($from, $attr) {
        try {
            $data = array();
            $i = 0;
            foreach ($attr as $possible) {

                $x = 0;
                foreach ($from as $key => $value) {
                    if (is_array($value)) {
                        $data[$key][$possible] = $from[$key][$possible];
                    }
                    $x++;
                }
                $i++;
            }
            return $data;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Fixes any un expected index. Fixes sorting issues too.
     * @param array $orgdata data which is going to be fixed
     * @return Fixed data.
     */

    function fixindexes($orgdata, $remove = false) {
        try {
            $data = array();
            if (count($orgdata) > 0) {
                foreach ($orgdata as $key => $value) {
                    if (!is_array($value)) {
                        if (!$remove) {
                            $data[$key] = $value;
                        }
                    } else {
                        $data[$key] = $this->confirmIndexes($value, $orgdata[0]);
                    }
                }
            }
            return $data;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Checks each indexe in 2 different arrays and fixes ind from the available.
     * @param array $ind Parameter index which is going to be fixed
     * @param array $available Default or available attributes list
     * @return Leaves only confirmed indexes in $ind based on $available.
     */

    function confirmIndexes($ind, $available) {
        try {
            $available = array_change_key_case($available, CASE_UPPER);
            $ind = array_change_key_case($ind, CASE_UPPER);
            foreach ($available as $k => $v) {
                if (!isset($ind[$k])) {
                    $ind[$k] = '';
                }
            }
            foreach ($ind as $key => $value) { // This loop is to remove any non-existing indexes (from the list of available array) from the data array.
                if (!isset($available[$key])) {
                    unset($ind[$key]);
                }
            }

            return $ind;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Finds parent of products from id and returns model of product if it does.
     * @param string $id Parameter id of product which is expected to have parent.
     * @return Parent product object else false.
     */

    function checkIfParentExists($id) {
        try {
            $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($id);
            if (!$parentIds)
                $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
            if (isset($parentIds[0])) {
                return Mage::getModel('catalog/product')->load($parentIds[0]);
            }
            return false;   // Sorry no parent exists.
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns extra indexes.
     * @return Returns extra indexes.
     */

    function extraIndexes() {
        $data = array();
        $data['SKU'] = 'SKU';
        $data['MASTER_PRODUCT_ID'] = 'MASTER_PRODUCT_ID';
        $data['NAME'] = 'NAME';
        $data['IMAGE_URL'] = 'IMAGE_URL';
        $data['PRODUCT_CATEGORY'] = 'PRODUCT_CATEGORY';
        $data['DESCRIPTION'] = 'DESCRIPTION';
        $data['MPN'] = 'MPN';
        $data['EAN'] = 'EAN';
        $data['MODEL'] = 'MODEL';
        $data['MANUFACTURER'] = 'MANUFACTURER';
        return $data;
    }

    /*
     * Returns uppercased indexes recursive.
     * $param array $arr array which needs to be uppercased recursively.
     * @return Returns extra indexes.
     */

    public function arrayKeyChangeCase($arr) {
        try {
            $final = array();
            if (is_array($arr) && count($arr) > 0) {
                foreach ($arr as $key => $val) {
                    if (is_array($val)) {
                        $final[strtoupper($key)] = $this->arrayKeyChangeCase($val);
                    } else {
                        $final[strtoupper($key)] = $val;
                    }
                }
            }
            return $final;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
