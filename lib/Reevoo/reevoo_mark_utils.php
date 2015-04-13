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
 
class ReevooMarkUtils {

    function ReevooMarkUtils($trkrefs) {
        $trkref_array = explode(',', $trkrefs);
        $this->trkref = self::presenceKey($trkref_array, 0);
    }

    static function presence($var, $default = null) {
        return empty($var) ? $default : $var;
    }

    static function presenceKey($arr, $key, $default = null) {
        return (is_array($arr) && array_key_exists($key, $arr)) ? $arr[$key] : $default;
    }

    function getVariant($options = array()) {
        $variant = self::presenceKey($options, 'variantName', '');
        if ($variant != '') {
            $variant = ' ' . $variant . '_variant';
        }
        return $variant;
    }

    function getTrkref($options = array()) {
        return self::presenceKey($options, 'trkref', $this->trkref);
    }

    function getPaginationParams($options = array()) {
        $page = self::presenceKey($_GET, 'reevoo_page', 1);
        $paginated = self::presenceKey($options, 'paginated');
        if ($paginated) {
            $numberOfReviews = self::presenceKey($options, 'numberOfReviews', "default");
            $pagination_params = "&per_page={$numberOfReviews}&page={$page}";
        } elseif (self::presenceKey($options, 'numberOfReviews')) {
            $pagination_params = "&reviews={$options['numberOfReviews']}";
        } else {
            $pagination_params = '';
        }
        return $pagination_params;
    }

    function getLocaleParam($options = array()) {
        if (self::presenceKey($options, 'locale')) {
            return "&locale={$options['locale']}";
        } else {
            return "";
        }
    }

    function getSortByParam($options = array()) {
        if (self::presenceKey($_GET, 'reevoo_sort_by')) {
            return "&sort_by={$_GET['reevoo_sort_by']}";
        } else {
            return "";
        }
    }

    function getFilterParam($options = array()) {
        if (self::presenceKey($_GET, 'reevoo_filter')) {
            return "&filter={$_GET['reevoo_filter']}";
        } else {
            return "";
        }
    }

    function getClientUrlParam($options = array()) {
        if (self::presenceKey($options, 'paginated')) {
            $current_url = urlencode($this->getCurrentURL());
            return "&client_url={$current_url}";
        } else {
            return "";
        }
    }

    function reviewCount($data) {
        return $data->header("X-Reevoo-ReviewCount");
    }

    function offerCount($data) {
        return $data->header("X-Reevoo-OfferCount");
    }

    function conversationCount($data) {
        return $data->header("X-Reevoo-ConversationCount");
    }

    function bestPrice($data) {
        return $data->header("X-Reevoo-BestPrice");
    }

    function getCurrentURL() {
        return Mage::helper('core/url')->getCurrentUrl();
    }

}
