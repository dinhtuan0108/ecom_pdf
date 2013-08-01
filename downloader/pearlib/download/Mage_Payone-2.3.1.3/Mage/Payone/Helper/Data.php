<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Payone
 * @copyright  Copyright (c) 2008 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payone data helper
 */
class Mage_Payone_Helper_Data extends Mage_Payment_Helper_Data
{
    const DEBUG_LEVEL_DISABLED = 0;
    const DEBUG_LEVEL_LIGHT = 1;
    const DEBUG_LEVEL_FULL = 2;

    const DEBUG_CONFIG_ADDRESS_CHECK = 'address_check';
    const DEBUG_CONFIG_CREDIT_RATING = 'credit_rating';

    public function debug($message, $config, $level = self::DEBUG_LEVEL_LIGHT)
    {
        switch($config) {
            case self::DEBUG_CONFIG_CREDIT_RATING :
                $model = Mage::getModel('payone/CreditRating');
                break;
            case self::DEBUG_CONFIG_ADDRESS_CHECK :
                $model = Mage::getModel('payone/Addresscheck');;
                break;
        }
        if ($level <= $model->getConfigData('debug')) {
            Mage::log($message);
        }
    }

    public function compareAddreeses ($addreeses1, $addreeses2)
    {
        return $addreeses1->getFirstname() == $addreeses2->getFirstname() &&
               $addreeses1->getLastname() == $addreeses2->getLastname() &&
               $addreeses1->getCountryId() == $addreeses2->getCountryId() &&
               $addreeses1->getRegion() == $addreeses2->getRegion() &&
               $addreeses1->getPostcode() == $addreeses2->getPostcode() &&
               $addreeses1->getCity() == $addreeses2->getCity() &&
               $addreeses1->getTelephone() == $addreeses2->getTelephone() &&
               $addreeses1->getStreet(-1) == $addreeses2->getStreet(-1);
    }

    public function getPendingPaymentStatus()
    {
        if (version_compare(Mage::getVersion(), '1.4.0', '<')) {
            return Mage_Sales_Model_Order::STATE_HOLDED;
        }
        return Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
    }


}