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
class Mage_Payone_Block_FormSb extends Mage_Payment_Block_Form_Cc
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('payone/form_sb.phtml');
	}
    /**
     * Returns options of Transfer Types
     * @return array
     */
    public function getTransferTypeOptions()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $country = $quote->getBillingAddress()->getCountry();
        $options = array();
        switch ($country) {
            case 'DE':
                $options['PNT'] = 'Sofortüberweisung Deutschland';
                $options['GPY'] = 'Giropay Deutschland';
                break;
            case 'AT':
                $options['PNT'] = 'Sofortüberweisung Österreich';
                $options['EPS'] = 'EPS Österreich';
                break;
            case 'CH':
                $options['PFF'] = 'PostFinance E-Finance Schweiz';
                $options['PFC'] = 'PostFinance Card Schweiz';
                break;
        }

        $method = $this->getMethod();
        $alloved = explode(',', $method->getConfigData('allowed_transfer_type'));
        if (count($alloved) == 0) {
            return $options;
        }
        $allovedOptions = array();
        foreach ($options as $option => $title) {
            if (in_array($option . '-' . $country, $alloved)) {
                $allovedOptions[$option] = $title;
            }
        }
        return $allovedOptions;
    }
    /**
     * Returns order country
     * @return string
     */
    public function getCountry()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        return $quote->getBillingAddress()->getCountry();
    }

}