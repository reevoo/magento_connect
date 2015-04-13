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
require_once(Mage::getBaseDir('lib') . "/Reevoo/reevoo_mark_utils.php");
require_once(Mage::getBaseDir('lib') . "/Reevoo/reevoo_mark_http_client.php");

class ReevooMark {

    function ReevooMark($trkrefs, $cache_path, $base_url = 'http://mark.reevoo.com') {
        $this->trkrefs = $trkrefs;
        $this->base_url = $base_url;
        $this->http_client = new ReevooMarkHttpClient($base_url, $cache_path);
        $this->utils = new ReevooMarkUtils($trkrefs);
    }

    function cssAssets() {
        echo '<link rel="stylesheet" href="//mark.reevoo.com/stylesheets/reevoomark/embedded_reviews.css" type="text/css" />';
    }

    function productBadge($options = array()) {
        if (!$options['sku']) {
            Mage::log('Compulsory parameter sku has not been provided to ReevooMark#productBadge', null, 'reevoo_php_api.log');
            return;
        }
        $variant = $this->utils->getVariant($options);
        $trkref = $this->utils->getTrkref($options);
        echo "<a class=\"reevoomark{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/{$options['sku']}\"></a>";
    }

    function conversationsBadge($options = array()) {
        if (!$options['sku']) {
            Mage::log('Need to provide a SKU for ReevooMark#conversationBadge', null, 'reevoo_php_api.log');
            return;
        }
        $variant = $this->utils->getVariant($options);
        $trkref = $this->utils->getTrkref($options);
        echo "<a class=\"reevoomark reevoo-conversations{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/{$options['sku']}\"></a>";
    }

    function productSeriesBadge($options = array()) {
        if (!$options['sku']) {
            Mage::log('Need to provide a SKU for ReevooMark#productSeriesBadge', null, 'reevoo_php_api.log');
            return;
        }
        $variant = $this->utils->getVariant($options);
        $trkref = $this->utils->getTrkref($options);
        echo "<a class=\"reevoomark{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/series:{$options['sku']}\"></a>";
    }

    function conversationSeriesBadge($options = array()) {
        if (!$options['sku']) {
            Mage::log('Need to provide a SKU for ReevooMark#conversationSeriesBadge', null, 'reevoo_php_api.log');
            return;
        }
        $variant = $this->utils->getVariant($options);
        $trkref = $this->utils->getTrkref($options);
        echo "<a class=\"reevoomark reevoo-conversations{$variant}\" href=\"{$this->base_url}/partner/{$trkref}/series:{$options['sku']}\"></a>";
    }

    function overallServiceRatingBadge($options = array()) {
        $variant = $this->utils->getVariant($options);
        $trkref = $this->utils->getTrkref($options);
        echo "<a class=\"reevoo_reputation{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
    }

    function customerServiceRatingBadge($options = array()) {
        $variant = $this->utils->getVariant($options);
        $trkref = $this->utils->getTrkref($options);
        echo "<a class=\"reevoo_reputation customer_service{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
    }

    function deliveryRatingBadge($options = array()) {
        $variant = $this->utils->getVariant($options);
        $trkref = $this->utils->getTrkref($options);
        echo "<a class=\"reevoo_reputation delivery{$variant}\" href=\"{$this->base_url}/retailer/{$trkref}\"></a>";
    }

    function productReviews($options = array()) {
        if (!$options['sku']) {
            Mage::log('Need to provide a SKU for ReevooMark#productReviews', null, 'reevoo_php_api.log');
            return;
        }
        $trkref = $this->utils->getTrkref($options);
        $pagination_params = $this->utils->getPaginationParams($options);
        $locale_param = $this->utils->getLocaleParam($options);
        $sort_by_param = $this->utils->getSortByParam($options);
        $filter_param = $this->utils->getFilterParam($options);
        $client_url_param = $this->utils->getClientUrlParam($options);
        $notEmpty = $this->get_embedded_data("/reevoomark/embeddable_reviews?trkref={$trkref}&sku={$options['sku']}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}{$client_url_param}", "X-Reevoo-ReviewCount", $options);
        return $notEmpty;
    }

    function customerExperienceReviews($options = array()) {
        $trkref = $this->utils->getTrkref($options);
        $pagination_params = $this->utils->getPaginationParams($options);
        $locale_param = $this->utils->getLocaleParam($options);
        $sort_by_param = $this->utils->getSortByParam($options);
        $filter_param = $this->utils->getFilterParam($options);
        $client_url_param = $this->utils->getClientUrlParam($options);
        $notEmpty = $this->get_embedded_data("/reevoomark/embeddable_customer_experience_reviews?trkref={$trkref}{$pagination_params}{$locale_param}{$sort_by_param}{$filter_param}{$client_url_param}", "X-Reevoo-ReviewCount", $options);
        return $notEmpty;
    }

    function conversations($options = array()) {
        if (!$options['sku']) {
            Mage::log('Need to provide a SKU for ReevooMark#productReviews', null, 'reevoo_php_api.log');
            return;
        }
        $trkref = $this->utils->getTrkref($options);
        $locale_param = $this->utils->getLocaleParam($options);
        $notEmpty = $this->get_embedded_data("/reevoomark/embeddable_conversations?trkref={$trkref}&sku={$options['sku']}{$locale_param}", "X-Reevoo-ConversationCount", $options);
        return $notEmpty;
    }

    function purchaseTrackingEvent($options = array()) {
        $trkref = $this->utils->getTrkref($options);
        if (!$options['skus']) {
            Mage::log('Need to provide the list of skus purchased for ReevooMark#purchaseTrackingEvent', null, 'reevoo_php_api.log');
            return;
        }
        $skus = $options['skus'];
        $value = $options['value'];
        $this->echo_tracking_script($trkref, "retailer.track_purchase(\"{$skus}\".split(/[ ,]+/), \"{$value}\");");
    }

    function propensityToBuyTrackingEvent($options = array()) {
        $trkref = $this->utils->getTrkref($options);
        $action = isset($options['action']) ? $options['action'] : false;
        $sku = isset($options['sku']) ? $options['sku'] : false;
        if (!$sku) {
            $sku = "Global CTA";
        }
        $this->echo_tracking_script($trkref, "retailer.Tracking.ga_track_event(\"Propensity to buy\", \"{$action}\", \"{$sku}\");retailer.track_exit();");
    }

    function javascriptAssets() {
        echo <<<EOT
<script id="reevoomark-loader" type="text/javascript" charset="utf-8">
(function () {
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = '//cdn.mark.reevoo.com/assets/reevoo_mark.js';
    var s = document.getElementById('reevoomark-loader');
    s.parentNode.insertBefore(script, s);
 })();
 if (typeof afterReevooMarkLoaded === 'undefined') {
    var afterReevooMarkLoaded = [];
 }
 afterReevooMarkLoaded.push(
   function () {
     ReevooApi.each('{$this->trkrefs}'.split(/[ ,]+/), function (retailer) {
     retailer.init_badges();
     retailer.init_reevoo_reputation_badges();
   });
 }
);
</script>
EOT;
    }

    private function get_embedded_data($embedded_data_url, $count_header, $options) {
        $showEmptyMessage = array_key_exists('showEmptyMessage', $options) ? $options['showEmptyMessage'] : true;
        $data = $this->http_client->getData($embedded_data_url);
        $notEmpty = !!($data->header($count_header));
        if ($notEmpty || $showEmptyMessage) {
            echo $data->body();
        }
        return $notEmpty;
    }

    private function echo_tracking_script($trkref, $tracking_call) {
        echo <<<EOT
<script type="text/javascript" charset="utf-8">
if (typeof afterReevooMarkLoaded === 'undefined') {
  var afterReevooMarkLoaded = [];
}
afterReevooMarkLoaded.push(
  function(){
    ReevooApi.load('{$trkref}', function(retailer){
      {$tracking_call}
    });
  }
);
</script>
EOT;
    }

}
