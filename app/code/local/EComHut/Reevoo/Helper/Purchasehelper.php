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
class EComHut_Reevoo_Helper_Purchasehelper extends Mage_Core_Helper_Abstract {
    /*
     * Fetch Purchase Collection.
     * @return Collection of Orders etc..
     */

    public function getPurchaseCollection() {
        try {
            if (!Mage::getStoreConfig('reevoo_setup/reevoo_status/status')) {
                return false;
            }

            $order_items = Mage::getResourceModel('sales/order_item_collection')->addAttributeToSelect('*')->distinct(TRUE);
            if (Mage::getStoreConfig('reevoo_setup/reevoo_ftp/purchasefeed_delta')) {
                $to = date('Y-m-d H:i:s');
                $lastTime = strtotime(Mage::getModel('reevoo/data')->getLastPurchaseFeedDate());
                $from = date('Y-m-d H:i:s', $lastTime);
                $order_items->addAttributeToFilter('updated_at', array('from' => $from, 'to' => $to));
            }
            $order_items->load();

            return $order_items;
        } catch (Exceptions $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns finalized and prepared purchase collection for CSV purposes.
     * @param boolean $debug (optional) if true, will return all of the collection data for debugging purposes..
     * @return Finalized and prepared collection of orders.
     */

    public function getPurchases($debug = false) { // Main function for purchases.
        try {
            $order_items = $this->getPurchaseCollection();
            $orders = array();
            $check = array();
            foreach ($order_items as $data) {
                $tmp = $data->getData();
                $order = Mage::getModel('sales/order')->load($tmp['order_id']);
                $shipments = $order->getShipmentsCollection()->getData();
                $basic = $order->getData();
                if (isset($check[$basic['entity_id']]) && $check[$basic['entity_id']]) {
                    continue;
                }
                if (is_array($shipments) && count($shipments) > 0) {
                    $x = array('basicDetails' => $order->getData(), 'productsOrder' => array(), 'shippingAddress' => $order->getShippingAddress()->getData(), 'billingAddress' => $order->getBillingAddress()->getData(), 'shipmentCollection' => $shipments);
                    $y = $x;
                    foreach ($order->getAllItems() as $odr) {
                        $y['productsOrder'][] = $odr->getData();
                    }
                    $orders[] = $y;
                }
                $check[$basic['entity_id']] = true;
            }
            if ($debug) {
                return $orders;
                //return $this->sortPurchases($orders, $debug);
            }
            return $this->_fixIndexes($this->sortPurchases($orders));
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Helper to fix the collection received from getPurchases()
     * @param array $order collection which requires fixes.
     * @return semi improved version of collection.
     */

    public function sortPurchases($order, $debug = false) {
        try {
            $output = array();
            if (is_array($order) && count($order) > 0) {
                foreach ($order as $key => $value) {
                    $output[$key] = $this->_sortPurchasesLevel2($value, $debug);
                }
            }
            return $output;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Helper to help the helper @sortPurchases() function
     * @param array $order individual order from sortPurchases collection
     * @return semi improved version of individual order.
     */

    private function _sortPurchasesLevel2($order, $debug = false) {
        $data['EMAIL'] = $this->_getBaseAttribute($order, 'customer_email');
        $data['FIRST_NAME'] = $this->_getShippingAttribute($order, 'firstname');
        $data['SKU'] = '';
        $multiply['SKU'] = $this->_getProductAttribute($order, 'sku');
        $data['MASTER_PRODUCT_SKU'] = '';
        $multiply['MASTER_PRODUCT_SKU'] = $this->_getProductsParentSku($multiply['SKU']);
        $data['PURCHASE_DATE'] = $this->_getBaseAttribute($order, 'created_at');
        $data['DELIVERY_DATE'] = $this->_getShipmentAttributes($order, 'created_at');
        if (is_array($data['DELIVERY_DATE']) && count($data['DELIVERY_DATE']) > 1) {
            $data['DELIVERY_DATE'] = $data['DELIVERY_DATE'][0];
        }
        $data['SHIPPING_STATUS'] = ucwords($this->_getBaseAttribute($order, 'status'));
        $data['PRICE'] = '';
        $multiply['PRICE'] = $this->_getProductAttribute($order, 'price_incl_tax');
        $data['ORDER_REF'] = $this->_getBaseAttribute($order, 'increment_id');
        $data['CUSTOMER_REF'] = $this->_getBaseAttribute($order, 'customer_id');
        $data['CURRENCY'] = $this->_getBaseAttribute($order, 'base_currency_code');
        $data['SHIPPING_POSTCODE1'] = $this->_getShippingAttribute($order, 'postcode');

        $extraAddresses = $this->_getCustomerAddressById($this->_getShipmentAttributes($order, 'shipping_address_id'));

        $data['SHIPPING_POSTCODE2'] = isset($extraAddresses[1]['postcode']) ? $extraAddresses[1]['postcode'] : ''; //$this->_getShippingAttribute($order, 'postcode');
        $data['SHIPPING_POSTCODE3'] = isset($extraAddresses[2]['postcode']) ? $extraAddresses[2]['postcode'] : ''; //$this->_getShippingAttribute($order, 'postcode');
        $data['SHIPPING_COUNTRY1'] = $this->_getShippingAttribute($order, 'country_id');
        $data['SHIPPING_COUNTRY2'] = isset($extraAddresses[1]['country_id']) ? $extraAddresses[1]['country_id'] : ''; //$this->_getShippingAttribute($order, 'country_id');
        $data['SHIPPING_COUNTRY3'] = isset($extraAddresses[2]['country_id']) ? $extraAddresses[2]['country_id'] : ''; //$this->_getShippingAttribute($order, 'country_id');
        $data['BILLING_POSTCODE'] = $this->_getBillingAttribute($order, 'postcode');
        $data['BILLING_COUNTRY'] = $this->_getBillingAttribute($order, 'country_id');
        $data['PICK_UP_STORE'] = ''; // We don't know any such attribute
        $multiply['TYPE'] = $this->_getProductAttribute($order, 'product_type');
        return $this->_multiplydetails($data, $order, $multiply, $debug);
    }

    /*
     * Helper for fetching address by shipping
     * @param array $data array of addresseses to be fetched for.
     * @return shipping and country array.
     */

    private function _getCustomerAddressById($id) {
        $arr = array();
        if (is_array($id)) {
            foreach ($id as $v) {
                $tmp = Mage::getModel('sales/order_address')->load($v)->getData();
                $arr[] = array('postcode' => $tmp['postcode'], 'country_id' => $tmp['country_id']);
            }
            return $arr;
        }
        $tmp = Mage::getModel('sales/order_address')->load($id)->getData();
        return array('postcode' => $tmp['postcode'], 'country_id' => $tmp['country_id']);
    }

    /*
     * Helper for fixing indexes
     * @param array $data array semi finalized data. Merges the indexes
     * @return finalized order collection.
     */

    private function _fixIndexes($data) {
        try {
            $output = array();
            $i = 1;
            foreach ($data as $value) {

                foreach ($value as $v) {
                    $output[0] = array_keys($v);
                    $output[$i] = $v;
                    $i++;
                }
            }
            return $output;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Helper fixer for merging uneeded data for smoother actions.
     * @param array $order individual order
     * @param array $sec Original collection of individual order details.
     * @param array $multiply The array which needs to be added/ fixed.
     * @return array of order detail based on each product stuff.
     */

    private function _multiplydetails($order, $sec = false, $multiply, $debug = false) {
        try {
            $d = array();
            $d['products'] = array_keys($multiply['SKU']);

            $productMultiply = $this->_sortKeysOrder($d['products'], $multiply);

            $output = array();

            $i = 0;
            //$output[$i] = $order;

            $configurables = array();

            if (count($productMultiply) > 0) {
                foreach ($productMultiply as $val) {
                    if ($val['TYPE'] == 'configurable') {
                        $configurables[$order['ORDER_REF']][$val['SKU']] = $val['PRICE'];
                    } else {
                        $output[$i] = $order;
                        $output[$i]['SKU'] = $val['SKU'];
                        $output[$i]['MASTER_PRODUCT_SKU'] = $val['MASTER_PRODUCT_SKU'];
                        $output[$i]['PRICE'] = $val['PRICE'];
                        unset($output[$i]['TYPE']);
                        $i++;
                    }
                }
            }
            if ($debug) {
                $final = $this->_mergeMasterProductsPrices($output, $configurables);
                return array('final' => $final, 'output' => $output);
            }
            return $this->_mergeMasterProductsPrices($output, $configurables);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Helper fixer for merging MasterProducts, their prices and so on.
     * @param array $order individual order
     * @param array $conf configuration array like array('order_ref'=>array('sku'=>12)) wher 12 is price and its only applicable for configurable products.
     * @return array of order detail based on each product stuff.
     */

    private function _mergeMasterProductsPrices($data, $conf) {
        try {
            $output = array();
            foreach ($data as $k => $v) {
                $output[$k] = $v;
                if ($v['MASTER_PRODUCT_SKU'] !== $v['SKU']) {
                    $output[$k]['PRICE'] = (int) @$conf[$v['ORDER_REF']][$v['MASTER_PRODUCT_SKU']] + (int) @$v['PRICE'];
                }
                if ((int) @$output[$k]['PRICE'] < 1) {
                    $output[$k]['PRICE'] = (int) $conf[$v['ORDER_REF']][$v['SKU']];
                }
            }
            return $output;
        } catch (Exception $ex) {
            Mage::logException($ex);
        }
    }

    /*
     * Helper to sort the key order of array. Relates to other functionalities.
     * @param array $data of semi managed from _multiplydetails
     * @param array $multiply data array of extra indexes of each product.
     * @return Improved version of sorted array.
     */

    private function _sortKeysOrder($data, $multiply) {
        try {
            $output = $data;

            foreach ($data as $key => $val) {
                unset($output[$key]);
                $output[$val]['SKU'] = $multiply['SKU'][$val];
                $output[$val]['MASTER_PRODUCT_SKU'] = $multiply['MASTER_PRODUCT_SKU'][$val];
                $output[$val]['PRICE'] = @$multiply['PRICE'][$val];
                $output[$val]['TYPE'] = $multiply['TYPE'][$val];
            }
            return $output;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Finds base attribute easily.
     * @param array $order individual order array.
     * @param string $attr name of key to be searched for.
     * @return Order basic attribute.
     */

    private function _getBaseAttribute($order, $attr) {
        try {
            if (is_array($order) && isset($order['basicDetails'][$attr])) {
                return $order['basicDetails'][$attr];
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Finds Product attribute easily.
     * @param array $order individual order array.
     * @param string $attr name of key to be searched for.
     * @return Order product attribute(s).
     */

    private function _getProductAttribute($order, $attr) {
        try {
            $data = array();
            if (is_array($order) && count($order['productsOrder']) > 0) {
                foreach ($order['productsOrder'] as $value) {
                    if (isset($value[$attr])) {
                        $data[$value['product_id']] = $value[$attr];
                    }
                }
                return $data;
            }
            return '';
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Finds shipping attribute easily.
     * @param array $order individual order array.
     * @param string $attr name of key to be searched for.
     * @return Order shipping attribute.
     */

    private function _getShippingAttribute($order, $attr) {
        try {
            if (is_array($order) && isset($order['shippingAddress'][$attr])) {
                return $order['shippingAddress'][$attr];
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Finds billing attribute easily.
     * @param array $order individual order array.
     * @param string $attr name of key to be searched for.
     * @return Order billing attribute.
     */

    private function _getBillingAttribute($order, $attr) {
        try {
            if (is_array($order) && isset($order['billingAddress'][$attr])) {
                return $order['billingAddress'][$attr];
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Finds Shipment attribute easily.
     * @param array $order individual order array.
     * @param string $attr name of key to be searched for.
     * @return Order shipment attribute(s).
     */

    private function _getShipmentAttributes($order, $attr) {
        try {
            $data = array();
            $i = 0;
            if (is_array($order) && count($order['shipmentCollection']) > 0) {
                foreach ($order['shipmentCollection'] as $value) {
                    if (isset($value[$attr])) {
                        $data[$i] = $value[$attr];
                        $i++;
                    }
                }
                if ($i == 1) {
                    return $data[0];
                }
                return $data;
            }
            return '';
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Fetches all parent products attribute.
     * @param array $list ids of the products whom parent is required.
     * @return Parent Product in full or false based on data.
     */

    private function _getProductsParentSku($list) {
        try {
            $data = array();
            if (is_array($list) && count($list) > 0) {
                foreach ($list as $key => $value) {
                    $data[$key] = $this->_getParentProductSku($key, $value);
                }
            }
            return $data;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Fetches all parent products attribute.
     * @param string $list id of the product whom parent is required.
     * @return Parent Product in full or false based on data.
     */

    private function _getParentProductSku($itemId, $itemSku) {
        try {
            $_helper = Mage::helper('reevoo/producthelper');
            $parent = $_helper->checkIfParentExists($itemId);
            if (!$parent) {
                return $itemSku;
            }
            return $parent['sku'];
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
