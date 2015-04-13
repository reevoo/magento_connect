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
require_once(Mage::getBaseDir('lib') . "/Reevoo/reevoo_mark_cache.php");

class ReevooMarkHttpClient {

    function ReevooMarkHttpClient($base_url, $cache_path) {
        $this->base_url = $base_url;
        $this->cache = new ReevooMarkCache($cache_path);
    }

    function getData($url_path) {
        $doc = $this->cache->newDocumentFromCache($url_path);
        if ($doc->hasExpired()) {
            $remote_doc = new ReevooMarkDocument($this->loadFromRemote($url_path), time());
            if (!$doc->isCachableResponse() || $remote_doc->isCachableResponse()) {
                $this->cache->saveToCache($remote_doc->data, $url_path);
                $doc = $remote_doc;
            }
        }
        return $doc;
    }

    protected function loadFromRemote($url_path) {
        $remote_url = $this->base_url . $url_path;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($ch, CURLOPT_USERAGENT, "ReevooMark PHP Widget/8");
        curl_setopt($ch, CURLOPT_REFERER, "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        if ($result = curl_exec($ch))
            return str_replace("\r", "", $result);
        else
            return false;
    }

}
