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
class EComHut_Reevoo_Helper_Ftp extends Mage_Core_Helper_Abstract {

    const CONFIG_PATH = 'reevoo_setup/reevoo_ftp/';

    public $ftp_status;
    public $filename;

    /*
     * Returns full status of FTP (after verifying extension status
     * @return true or false based on admin setting.
     */

    function getStatus() {
        try {
            $reevoo_status = $this->getValue('status', 'reevoo_setup/reevoo_status/');
            $ftp_status = $this->getValue('ftp_status');
            if ($reevoo_status && $ftp_status) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Returns configuration value.
     * @param string $req value name to acquire the configuration value.
     * @param string $custom parent reference if its not related to ftp.
     * @return value of inquired configuration.
     */

    function getValue($req, $custom = false) {
        try {
            if ($custom === false) {
                $custom = self::CONFIG_PATH;
            }
            return Mage::getStoreConfig($custom . $req, Mage::app()->getStore());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Inquire for database and decides which ftp method to choose for FTP.
     * @param string $filePath path to file which is expected to be uploaded in ftp connection.
     * @return Null/ status of transfer.
     */

    function setTransfer($filePath) {
        try {
            $this->ftp_status = false;
            if ($this->getStatus() === false) {
                return false;
            }
            if (!file_exists($filePath)) {
                return false;   // we escape and do not upload the file if there is no content present.
            }
            $this->filename = $filePath;
            if ($this->getValue('ftp_type') == 'sftp') {
                $status = $this->setSftpTransfer($filePath);
            } elseif ($this->getValue('ftp_type') == 'ftp') {
                $status = $this->setFtpTransfer($filePath);
            }
            if ($status) {
                $this->deleteFileAfterTransfer($this->filename); // Delete the filer in alternative way.
            }
            return $this->ftp_status = $status;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Executes FTP transfer.
     * @param string $filePath path to file which is expected to be uploaded to server.
     * @return Null/ Status of transfer.
     */

    function setFtpTransfer($filePath) {
        $myhost = $this->getValue('ftp_host');
        $port = !$this->getValue('ftp_port') ? 21 : $this->getValue('ftp_port');
        $myuser = $this->getValue('ftp_user');
        $mypass = $this->getValue('ftp_pass');
        $uploadPath = $this->getFtpPath();
        $path_parts = pathinfo($filePath);
        $uploadName = $uploadPath . $path_parts['basename'];

        try {
            $ch = curl_init();
            $fp = fopen($filePath, 'r');
            curl_setopt($ch, CURLOPT_URL, 'ftp://' . $myhost . ':' . $port . '/' . $uploadName);
            curl_setopt($ch, CURLOPT_USERPWD, "$myuser:$mypass");
            curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, true); // Create directory.
            curl_setopt($ch, CURLOPT_FTP_USE_EPSV, true);   // Passive
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));
            curl_exec($ch);
            $error_no = curl_errno($ch);
            curl_close($ch);
            if ($error_no !== 0) {
                Mage::log('File upload error no. ' . $error_no);
                return false;
            }
            return $this->ftp_status = true;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), Zend_Log::ERR);
        }
    }

    /*
     * Exectutes SFTP transfer.
     * @param string $filePath path for file which requires upload
     * @return Null/ Status of transfer.
     */

    function setSftpTransfer($filePath) {
        $myhost = $this->getValue('ftp_host');
        $port = !$this->getValue('ftp_port') ? 22 : $this->getValue('ftp_port');
        $myuser = $this->getValue('ftp_user');
        $mypass = $this->getValue('ftp_pass');
        $uploadPath = $this->getFtpPath();
        $error = false;
        try {
            set_include_path(get_include_path() . PATH_SEPARATOR . Mage::getBaseDir('lib') . DS . "phpseclib" . DS);
            require_once(Mage::getBaseDir('lib') . DS . "phpseclib" . DS . "Net" . DS . "SFTP.php");
            $sftp = new Net_SFTP($myhost, $port, 90);
            if (!$sftp->login($myuser, $mypass)) {
                Mage::log('SFTP Login Incorrect.');
                return false; // escape rest else.
            }
            $path_parts = pathinfo($filePath);
            if (!$sftp->chdir($uploadPath)) {
                $sftp->mkdir($uploadPath);
            }
            $uploadName = $uploadPath . $path_parts['basename'];
            $sftp->put($uploadName, $filePath, NET_SFTP_LOCAL_FILE);
            return $this->ftp_status = true;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /*
     * Processes FTP/SFTP Path as configured in database.
     * @return finalized ftp part with trailing slash in the end.
     */

    function getFtpPath() {
        $uploadPath = $this->getValue('ftp_path');
        if (substr("$uploadPath", -1) == "/") { // returns last string.
            return $uploadPath;
        }
        return $uploadPath . "/";
    }

    /*
     * Delete file from server once file is uploaded.
     * @return Null.
     */

    function deleteFileAfterTransfer($file) {
        $_helper = Mage::getModel('reevoo/data');
        if (unlink($file)) {
            $_helper->saveLog('cron_log', 'deleteFileAfterTransfer', "Unlink file successful: " . $file);
        } else {
            $_helper->saveLog('cron_log', 'deleteFileAfterTransfer', "Unlink file permission denied: " . $file);
        }
    }

}
