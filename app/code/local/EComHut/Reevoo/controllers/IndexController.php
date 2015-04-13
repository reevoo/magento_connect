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
class EComHut_Reevoo_IndexController extends Mage_Adminhtml_Controller_Action {

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        error_reporting(0);
        parent::__construct($request, $response, $invokeArgs);
    }

    /*
     * @url reevoo/index/index
     * @return page basic view.
     */

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*
     * @url reevoo/index/products
     * @return Processes products cron manually.
     */

    public function productsAction() {
        $store_id = $this->getRequest()->getParams();
        $url = Mage::getModel('adminhtml/url')->getUrl('reevoo/index/processproducts') . "?1&store=" . $store_id['store'];
        echo '<h3>Processing products feed.</h3>';
        echo '<p>Please wait while process is finished. Process can take longer depending on the catalog size.</p>';
        echo "<script>"
        . "window.location = '$url';"
        . "</script>";
    }

    /*
     * @url reevoo/index/processproducts
     * @return page with processing status.
     */

    public function processproductsAction() {
        try {
            $filename = Mage::getModel('reevoo/data')->cronProcessProducts();
            if (file_exists($filename)) {

                $path_parts = pathinfo($filename);
                $offerName = $path_parts['basename'];

                $message = 'Manual Processing Successful. Please verify from: ' . $filename . '. Time: ' . time();
                Mage::getModel('reevoo/data')->saveLog('cron_log', 'manual_cronjob_finished', $message);

                echo "<h3>Feed Processed Successfully.</h3>";
                if (Mage::helper('reevoo/ftp')->setTransfer($filename)) {
                    echo '<p>Product feed file successfully uploaded to server.</p>';
                } else {
                    echo '<p>Product feed generated and saved in: <b>' . $filename . '</b></p>';
                }
                Mage::getModel('reevoo/data')->__dispatchAfterUpload(); // Will delete file after offering download
            } else {
                echo "<h3>Error in processing feed.</h3>";
                echo '<p>We are sorry, there are no products feed available or an configuration error occured.</p>';
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * @url reevoo/index/purchase
     * @return Processes purchase cron job..
     */

    public function purchasesAction() {
        $store_id = $this->getRequest()->getParams();
        $url = Mage::getModel('adminhtml/url')->getUrl('reevoo/index/processpurchases') . "?1&store=" . $store_id['store'];
        echo '<h3>Processing purchase feed.</h3>';
        echo '<p>Please wait while process is finished. Process can take longer depending on the number of purchases.</p>';
        echo "<script>"
        . "window.location = '$url';"
        . "</script>";
    }

    /*
     * @url reevoo/index/processpurchase
     * @return page with processing status.
     */

    public function processpurchasesAction() {
        try {
            $filename = Mage::getModel('reevoo/data')->cronPurchaseOrders();

            if (file_exists($filename)) {
                $path_parts = pathinfo($filename);
                $offerName = $path_parts['basename'];
                $message = 'Manual Processing Successful. Please verify from: ' . $filename . '. Time: ' . time();
                Mage::getModel('reevoo/data')->saveLog('cron_log', 'manual_cronjob_finished', $message);

                echo "<h3>Feed Processed Successfully.</h3>";
                if (Mage::helper('reevoo/ftp')->setTransfer($filename)) {
                    echo '<p>Purchase feed successfully uploaded to server.</p>';
                } else {
                    echo '<p>Purchase feed generated and saved in: <b>' . $filename . '</b></p>';
                }
                Mage::getModel('reevoo/data')->__dispatchAfterUpload(); // Will delete file after offering download
            } else {
                echo 'We are sorry, there are no products feed available or an configuration error occured.';
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

}
