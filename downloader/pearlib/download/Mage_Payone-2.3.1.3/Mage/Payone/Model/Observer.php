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

class Mage_Payone_Model_Observer
{
	/**
	* Saves corrected address after Peyone Credit Rating  request to status to order
	* @param   Varien_Event_Observer $observer
	* @return  Mage_Payone_Model_SaveOrderObserver
	*/
	public function savePayoneStatus($observer)
	{
		$event = $observer->getEvent();
		$session = Mage::getSingleton('core/session');
        $creditRating = Mage::getSingleton('payone/creditRating');
        $addressCheck = Mage::getSingleton('payone/addresscheck');
        $helper = Mage::helper('payone');

		if ($creditRating->isActive() && $session->getPayoneCreditRatingQuoteId() == $event->getQuote()->getId() &&
			$session->getPayoneCreditRatingMessage() != '') {

            $order = $event->getOrder();
			$status = $helper->__('PayoneCreditRating (') . $session->getPayoneCreditRatingTimestamp() . ") \n";
            $status .= $session->getPayoneCreditRatingMessage();
			$order->addStatusToHistory($order->getStatus(), nl2br($status));

            // clear all session values
            $session->unsetData('payone_credit_rating_message');
			$session->unsetData('payone_credit_rating_quote_id');
            $session->unsetData('payone_credit_rating_timestamp');
		}
		if ($addressCheck->isActive() && $session->getPayoneAddressCheckMessage() != '') {
            $order = $event->getOrder();
            $sessionMessageArray = unserialize($session->getPayoneAddressCheckMessage());

			$status = $helper->__('PayoneAddresscheck').':<br>';

			foreach ($sessionMessageArray as $key => $message) {
				$status .= '<b>'.$key.'</b>:<br>'.$message;
			}

			$order->addStatusToHistory($order->getStatus(), nl2br($status));

            // clear all session values
            $session->unsetData('payone_address_check_message');
		}

		return $this;
	}

    public function savePayoneCreditRatingScore($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $session = Mage::getSingleton('core/session');
        $response = $session->getCreditRatingResponse();
        $session->unsetData('credit_rating_response');
        $creditRating = Mage::getSingleton('payone/creditRating');
		if ($creditRating->isActive() && !empty($response)) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$customer->getId())
                return;

            $billingAddress = $order->getBillingAddress();
            $addresses = $customer->getAddresses();

            foreach ($addresses as $addr) {
                if(Mage::helper('payone')->compareAddreeses($billingAddress, $addr)) {
                    $address = $addr;
                }
            }

            if (empty($address))
                return;

            $address->setData('customer_address_payone_credit_rating_date', now());

            if ($creditRating->getConfigData('storedata') == Mage_Payone_Model_CreditRating::STORE_DATE_SCORE) {
                $address->setData('customer_address_payone_credit_rating_score', $response['score']);
            } else {
                $address->setData('customer_address_payone_credit_rating_score', '');
            }

            if ($creditRating->getConfigData('storedebugresponse')){
                $debugInfo = empty($response['secscore']) ? '' : $response['secscore'];
                $address->setData('customer_address_payone_credit_rating_secscore', $debugInfo);
            } else {
                $address->setData('customer_address_payone_credit_rating_secscore', '');
            }

            $address->save();
        }
    }

    public function registerInvoice($observer)
    {
        if (is_null(Mage::registry('current_invoice'))) {
            $invoice = $observer->getEvent()->getInvoice();
            Mage::register('current_invoice', $invoice);
        }
    }
    
    public function emptyCcSession($observer)
    {
        Mage::getSingleton('core/session')->unsetData('payone_cc_info');
        return;
    }
}
?>