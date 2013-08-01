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
class Mage_Payone_Block_FormElv extends Mage_Payment_Block_Form_Cc
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('payone/form_elv.phtml');
	}
    public function getBillingName()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $addr = $quote->getBillingAddress();
        return $addr->getFirstname() . ' ' . $addr->getLastname();
    }
}