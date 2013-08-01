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
class Mage_Payone_Model_Source_TransferType
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'PNT-DE', 'label' => Mage::helper('payone')->__('Sofortüberweisung Deutschland')),
            array('value' => 'PNT-AT', 'label' => Mage::helper('payone')->__('Sofortüberweisung Österreich')),
            array('value' => 'GPY-DE', 'label' => Mage::helper('payone')->__('Giropay Deutschland')),
            array('value' => 'EPS-AT', 'label' => Mage::helper('payone')->__('EPS Österreich')),
            array('value' => 'PFF-CH', 'label' => Mage::helper('payone')->__('PostFinance E-Finance Schweiz')),
            array('value' => 'PFC-CH', 'label' => Mage::helper('payone')->__('PostFinance Card Schweiz'))
		);
	}
}