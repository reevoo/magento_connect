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
  
require_once(Mage::getBaseDir('lib') . "/Reevoo/reevoo_mark_document.php");
require_once(Mage::getBaseDir('lib') . "/Reevoo/reevoo_mark_utils.php");

class ReevooMarkCache {

    var $cache_path;

    function ReevooMarkCache($cache_path) {
        $this->cache_path = $cache_path;
    }

    function saveToCache($data, $url_path) {
        if (ReevooMarkUtils::presence($this->cache_path)) {
            if (!file_exists($this->cache_path))
                mkdir($this->cache_path);
            file_put_contents($this->cachePath($url_path), $data);
        }
    }

    function loadFromCache($url_path) {
        if (ReevooMarkUtils::presence($this->cache_path)) {
            if (file_exists($this->cachePath($url_path))) {
                return file_get_contents($this->cachePath($url_path));
            }
        }
    }

    function cacheMTime($url_path) {
        if (ReevooMarkUtils::presence($this->cache_path)) {
            if (file_exists($this->cachePath($url_path))) {
                return filemtime($this->cachePath($url_path));
            }
        }
    }

    function newDocumentFromCache($url_path) {
        return new ReevooMarkDocument($this->loadFromCache($url_path), $this->cacheMTime($url_path));
    }

    protected function cachePath($url_path) {
        $digest = md5($url_path);
        return "$this->cache_path/$digest.cache";
    }

}
