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
 * @copyright  Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Payone_Model_Source_AllowScore
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_GREEN, 'label' => Mage::helper('payone')->__('Green')),
            array('value' => Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_YELLOW, 'label' => Mage::helper('payone')->__('Yellow')),
            array('value' => Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_RED, 'label' => Mage::helper('payone')->__('Red')),
        );
    }
}
?>