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
class EComHut_Reevoo_Model_Adminhtml_System_Config_Backend_Product_Cron extends Mage_Core_Model_Config_Data {

    const CRON_STRING_PATH = 'crontab/jobs/reevoo_productfeed_cron/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/jobs/reevoo_productfeed_cron/run/model';

    /*
     * Process Cron Job time calculation
     * @return null.
     */

    protected function _afterSave() {
        $time = $this->getData('groups/reevoo_productfeed/fields/time/value');
        $frequncy = $this->getData('groups/reevoo_productfeed/frequency/value');

        $frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronDayOfWeek = date('N');

        $cronExprArray = array(
            intval($time[1]), # Minute
            intval($time[0]), # Hour
            ($frequncy == $frequencyMonthly) ? '1' : '*', # Day of the Month
            '*', # Month of the Year
            ($frequncy == $frequencyWeekly) ? '1' : '*', # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('core/config_data')
                    ->load(self::CRON_STRING_PATH, 'path')
                    ->setValue($cronExprString)
                    ->setPath(self::CRON_STRING_PATH)
                    ->save();

            Mage::getModel('core/config_data')
                    ->load(self::CRON_MODEL_PATH, 'path')
                    ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                    ->setPath(self::CRON_MODEL_PATH)
                    ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }

}
