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
class EComHut_Reevoo_Helper_Data extends Mage_Core_Helper_Abstract {

    private $_productslist;
    private $_attributes;

    /*
     * Returns Products collections
     * @return $_productCollection
     */

    private function _getProducts() {
        try {
            if (!Mage::getStoreConfig('reevoo_setup/reevoo_status/status')) {
                return false;
            }
            if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) { // store level
                $store_id = Mage::getModel('core/store')->load($code)->getId();
            } elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) { // website level
                $website_id = Mage::getModel('core/website')->load($code)->getId();
                $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
            } else { // default level
                $store_id = Mage::app()->getRequest()->getParam('store', 1);
            }
            $_productCollection = Mage::getModel('catalog/product')->getCollection()->setStoreId($store_id)
                    ->addAttributeToSelect('*');
            if (Mage::getStoreConfig('reevoo_setup/reevoo_ftp/productfeed_delta')) {
                $to = date('Y-m-d H:i:s');
                $lastTime = strtotime(Mage::getModel('reevoo/data')->getLastProductFeedDate());
                $from = date('Y-m-d H:i:s', $lastTime);
                $_productCollection->addAttributeToFilter('updated_at', array('from' => $from, 'to' => $to));
            }
            $_productCollection->addFinalPrice()->getSelect();

            Mage::getModel('review/review')->appendSummary($_productCollection);
            if ($_productCollection->count()):
                return $this->_processProducts($_productCollection);
            endif;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Processes product collection for fixing the product relationship.
     * @param object $_productCollection object of product collection list.
     * @return Finalized and prepared collection of orders.
     */

    private function _processProducts($_productCollection) {
        $i = 1;
        try {
            if (count($_productCollection) > 0) {
                $ddata[0] = array_merge($this->getAttributes(), Mage::helper('reevoo/producthelper')->extraIndexes());
                ksort($ddata[0]);
                $ddata[0] = array_change_key_case($ddata[0], CASE_UPPER);
                foreach ($_productCollection as $product) {
                    $ddata[$i] = $this->_fixAttibuteComplication($product, $i);
                    $i++;
                }
                return $ddata;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Fixes attribute complications from individual products. Also sorts limited attributes for final data.
     * @param object $product (pass by reference) Individual Product object from product collection.
     * @return Corrected attributes associative array with products.
     */

    private function _fixAttibuteComplication(&$product) {
        try {
            $ddata = $product->getData();
            unset($ddata['stock_item']);
            unset($ddata['media_gallery']);
            unset($ddata['group_price']);
            unset($ddata['tier_price']);
            if (isset($ddata['rating_summary'])) {
                foreach ($product->getRatingSummary()->getData() as $k => $r) {
                    $ddata['rating_summary_' . $k] = $r;
                }
                unset($ddata['rating_summary']);
            }

            if (isset($ddata['stock_item'])) {
                foreach ($product->getStockItem()->getData() as $k => $st) {
                    $ddata[$k] = $st;
                }
            }
            if (strlen((string) Mage::getStoreConfig('reevoo_setup/reevoo_ftp/productfeed_defaultimage')) > 1) {
                if (isset($ddata[Mage::getStoreConfig('reevoo_setup/reevoo_ftp/productfeed_defaultimage')])) {
                    $ddata['IMAGE_URL'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $ddata[Mage::getStoreConfig('reevoo_setup/reevoo_ftp/productfeed_defaultimage')];
                }
            } else {
                if (isset($ddata['image'])) {
                    $ddata['IMAGE_URL'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
                }
            }
            $ddata['PRODUCT_CATEGORY'] = $product->getCategoryIds();
            if (count($ddata['PRODUCT_CATEGORY'])) {
                $ddata['PRODUCT_CATEGORY'] = $this->_getParentCategoires($ddata['PRODUCT_CATEGORY']);
            } else {
                $ddata['PRODUCT_CATEGORY'] = '';
            }
            $ddata = array_change_key_case($ddata, CASE_UPPER);
            return $ddata;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Processes and fixes categories by passing individual category to loop.
     * @param array $ids ID array of categories list.
     * @param string $glue glue for merging/ differenciate a full index of category.
     * @return Prepared categories list/names as string.
     */

    private function _getParentCategoires($ids, $glue = ' | ') {
        $finalName = '';
        try {
            if (is_array($ids)) {
                foreach ($ids as $cats) {
                    //$cat = $this->_getCatName($cats);
                    $paths = Mage::getModel('catalog/category')->load($cats)->getPath();
                    $catsAll = explode('/', $paths);
                    $finalName .= $this->_getCategoryNameProcessed($catsAll) . $glue;
                }
                return substr($finalName, 0, -3);
            } else {
                return (string) Mage::getModel('catalog/category')->load($ids)->getName();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns prepared category names including parent categories (i.e. pipe signed attributes.)
     * @param string $debug (optional) if true, will return all of the collection data for debugging purposes..
     * @return Finalized and prepared collection of orders.
     */

    private function _getCategoryNameProcessed($cats, $embedd = ' > ') {
        try {
            if (is_array($cats) && count($cats) > 0) {
                $rootid = Mage::app()->getStore()->getRootCategoryId();
                $data = '';
                foreach ($cats as $ids) {
                    if ($ids != $rootid) {
                        $name = Mage::getModel('catalog/category')->load($ids)->getName();
                        if (strlen(trim($name)) > 1) { // This is to avoid empty names added to category name
                            $data .= $name . $embedd;
                        }
                    }
                }
                return substr($data, 0, -3);
            } else {
                return (string) Mage::getModel('catalog/category')->load($cats)->getName();
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns finalized and prepared purchase collection of purchases.
     * @return Finalized and prepared collection of purchase feed.
     */

    public function getPurchases() {
        try {
            $_helper = Mage::helper('reevoo/purchasehelper');
            return $_helper->getPurchases();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Fetches all product attributes (including system/ product).
     * @return List of fetched attributes.
     */

    public function getAttributes() {

        if (isset($this->_attributes) && is_array($this->_attributes)) {
            return $this->_attributes;
        }

        try {
            $attributes = Mage::getModel('catalog/product')->getAttributes();
            $attributeArray = array();

            foreach ($attributes as $a) {
                foreach ($a->getEntityType()->getAttributeCodes() as $attributeName) {
                    $attributeArray[$attributeName] = $attributeName;
                }
                break;
            }
            ksort($attributeArray);
            return $this->_attributes = $attributeArray;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns finalized and prepared products collection.
     * @return Finalized and prepared associative array with collection of products.
     */

    public function getProducts() {
        try {
            if (!Mage::getStoreConfig('reevoo_setup/reevoo_status/status')) {
                return false;
            }
            if (isset($this->_productslist) && count($this->_productslist) > 0) {
                return $this->_productslist;
            }
            $products = $this->_getProducts();
            $data = $this->processParents($products);   // Going to add parents.

            $_productHelper = Mage::helper('reevoo/producthelper');
            $this->_productslist = $_productHelper->removeAttributes($_productHelper->fixindexes($data));
            return $this->_productslist;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     *  Adds parent products if there is any.
     *  @param array $data products array
     *  @returns array with added parent products into it.
     */

    public function processParents($data) {
        try {
            $finalData = array();
            if (count($data) > 0) {
                $_helper = Mage::helper('reevoo/producthelper'); //checkIfParentExists();
                foreach ($data as $key => $value) {
                    $parent = $_helper->checkIfParentExists(@$value['ENTITY_ID']);
                    if ($parent !== false) {
                        $finalData[$key] = $this->mergeParents($data[$key], $this->_fixAttibuteComplication($parent), $data[0]);
                        //$finalData[$key]['MASTER_PRODUCT'] = $this->_fixAttibuteComplication($parent);
                    } else {
                        $finalData[$key] = $value;
                        $finalData[$key]['MASTER_PRODUCT_ID'] = $value['SKU'];
                    }
                }
            }
            return $finalData;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Relates and merge parent products.
     * @param array $mainProduct main product which is going to be merged in parent.
     * @param array $parent parent product
     * @param array $attributes product attributes list (to fill empty attributes)
     * @return Finalized and prepared product collection.
     */

    public function mergeParents($mainProduct, $parent, $attributes) {
        try {
            $_helper = Mage::helper('reevoo/producthelper');
            $finalData = array();
            $mainProduct = $_helper->confirmIndexes($mainProduct, $attributes);
            $parent = $_helper->confirmIndexes($parent, $attributes);
            foreach ($mainProduct as $key => $value) {
                if (strlen($value) < 1) {
                    $mainProduct[$key] = $parent[$key];
                }
                $finalData = $mainProduct;
            }
            $finalData['MASTER_PRODUCT_ID'] = $parent['SKU'];
            return $finalData;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns full folder and file name of prepared csv file.
     * @param array $data associative product data array for csv preparation.
     * @param string $filename Filename we want to create with csv data.
     * @return system folder and file path of csv file.
     */

    public function saveProductsCsv($data, $filename = '') {
        try {
            if (strlen($filename) < 1) {
                $dt = date('Ymd_Gi');
                $filename = "productexportfeed_" . $dt . ".csv";
            }
            $folder = Mage::getBaseDir('var') . DS . 'Revoo_Feeds';
            try {
                if (!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                Mage::helper('reevoo/producthelper')->generateCsv($folder . DS . $filename, $data);
                return $folder . DS . $filename;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * returns purchase orders associative array.
     * @param boolean $debug (optional) if true, will return all of the collection data for debugging purposes..
     * @return Finalized and prepared associated array with collection of orders.
     */

    public function getPurchaseOrders($debug = false) {
        try {
            return Mage::helper('reevoo/purchasehelper')->getPurchases($debug);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns full folder and file name of prepared csv file.
     * @param array $data associative product data array for csv preparation.
     * @param string $filename Filename we want to create with csv data.
     * @return system folder and file path of csv file.
     */

    public function savePurchaseOrdersCsv($data, $filename = '') {
        if (strlen($filename) < 1) {
            $dt = date('Ymd_Gi');
            $filename = "purchaserexportfeed_" . $dt . ".csv";
        }
        $folder = Mage::getBaseDir('var') . DS . 'Revoo_Feeds';
        try {
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
            Mage::helper('reevoo/producthelper')->generateCsv($folder . DS . $filename, $data, false);
            return $folder . DS . $filename;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
