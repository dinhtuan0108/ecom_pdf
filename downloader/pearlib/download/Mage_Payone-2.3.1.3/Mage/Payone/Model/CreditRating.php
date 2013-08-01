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

class Mage_Payone_Model_CreditRating extends Mage_Payone_Model_Abstract
{
    const CREDIT_RATING_INQUIRY_TYPE_IH = 'IH';
    const CREDIT_RATING_INQUIRY_TYPE_IA = 'IA';
    const CREDIT_RATING_INQUIRY_TYPE_IB = 'IB';

    const CREDIT_RATING_SCORE_GREEN = 'G';
    const CREDIT_RATING_SCORE_YELLOW = 'Y';
    const CREDIT_RATING_SCORE_RED = 'R';

    const ADDRESS_CHECK_TYPE_NO = 'NO';
    const ADDRESS_CHECK_TYPE_BA = 'BA';
    const ADDRESS_CHECK_TYPE_PE = 'PE';

    const STORE_ONLY_DATE = 1;
    const STORE_DATE_SCORE = 2;

    protected $_localCache = array();

    function  __construct() {
        $this->_localCache = $this->_getSession()->getCreditRatingCache();
    }

    /**
     * Check if it, neccessary to perfotm request for this methode and quote.
     * @param  $methodCode
     * @param  $quote
     * @return boolean
     */
    public function isActiveForMethod($methodCode, $quote)
    {
        $addressCheck = Mage::getSingleton('payone/addresscheck');
        $addressCheckCache = $addressCheck->_getCheckout()->getPayouneAddresscheckCache();
        if ($addressCheck->isActive() && is_null($addressCheckCache)) {
            return false;
        }

        $mappedScore = $this->_getPersonStatusMappedScore($quote);
        if (!is_null($mappedScore)) {
            return true;
        }
        return $this->getConfigData('creditratingactive') &&
                $this->getConfigData('creditratingmintotal') < $quote->getGrandTotal() &&
                $quote->getBillingAddress()->getCountryId() == 'DE';
    }
    /**
     * Check if if module enabled
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getConfigData('creditratingactive');
    }
    /**
     * Main method. Returns rating score for given method and quote.
     *
     * @param $methodCode
     * @param $quote
     * @return score
     */
    public function getCreditRatingScore($methodCode, $quote)
    {
        $session = Mage::getSingleton('core/session');
		$session->setPayoneCreditRatingTimestamp(sprintf('%s', Mage::helper('core')->formatDate(null, 'medium', true)));
		$session->setPayoneCreditRatingQuoteId($quote->getId());

        if ($mappedScore = $this->_getPersonStatusMappedScore($quote) ) {
            $score = array();
            $score['score']  =  $mappedScore;
            $score['source'] = 'Mapped value of person status';
            return $score;
        }
        try {
            $confirm = $this->_confirmAddress($quote);
            if ($confirm['status'] != 'VALID') {
                return self::CREDIT_RATING_SCORE_RED;
            }
            $request = $this->_buildCreditRatingRequest($confirm['address'], $quote);
            $response = $this->_performRequest($request);
        } catch (Exception $e) {
            $score['score'] = isset ($response['score']) ? $response['score'] : self::CREDIT_RATING_SCORE_YELLOW;
            $score['source'] = 'Default value due exception: ' . $e->getMessage();
            return $score;
        }
        $score['score'] = isset ($response['score']) ? $response['score'] : self::CREDIT_RATING_SCORE_YELLOW;
        $score['source'] = $response['source'];
        return $score;
    }

    /**
     * Check address, and correct it if necessary
     * @param <type> $quote
     * @return <type>
     */
    protected function _confirmAddress($quote)
    {
        $result['status'] = 'INVALID';
        $billingaddress = $quote->getBillingAddress();

        $sourceAddress = $this->_getAddressData($billingaddress);
        if ($this->getConfigData('addresschecktype') == self::ADDRESS_CHECK_TYPE_NO) {
            $result['status'] = 'VALID';
            $result['address'] = $sourceAddress;
            return $result;
        }
        $params = $this->_initRequestParams();
        $params['request'] = 'addresscheck';
        $params['addresschecktype'] = $this->getConfigData('addresschecktype');
        $request = array_merge($params, $sourceAddress);
        $response = $this->_performRequest($request);
        if (!isset($response['status']) || $response['status'] != 'VALID') {
            return $result;
        }
        $merged = $this->_mergeAddressData($sourceAddress, $response);
        $result['status'] = 'VALID';
        $result['address'] = $merged;
        return $result;
    }

    /**
     * Map address $billingaddress from quote to array for peyone request.
     * @param $billingaddress
     * @return array $params
     */
    protected function _getAddressData($billingaddress)
    {
        $params = array();
        $params['firstname'] = $billingaddress->getFirstname();
        $params['lastname'] =  $billingaddress->getLastname();
        $params['street'] =    Mage::helper('payone/addresscheck')->normalizeStreet($billingaddress->getStreet());
        $params['zip'] = $billingaddress->getPostcode();
        $params['city'] = $billingaddress->getCity();
        $params['telephonenumber'] = $billingaddress->getTelephone();
        $params['country'] = $billingaddress->getCountryId();
        return $params;
    }

    /**
     * Compare $billingaddress significant fields of source address (from quote)
     * and address in response after address checking. If address was corrected,
     * save new address into session.
     * @param $sourceAddress
     * @param $response
     * @return <type>
     */
    protected function _mergeAddressData($sourceAddress, $response)
    {
        $helper = Mage::helper('payone');
        $report = '';
        if ($sourceAddress['street'] != $response['street']) {
            $report .= $helper->__('Street updated: %s', $sourceAddress['street'] . ' => ' . $response['street']);
            $sourceAddress['street'] = $response['street'];
        }
        if ($sourceAddress['zip'] != $response['zip']) {
            $report .= $helper->__('ZIP updated: %s', $sourceAddress['zip'] . ' => ' . $response['zip']);
            $sourceAddress['zip'] = $response['zip'];
        }
        if ($sourceAddress['city'] != $response['city']) {
            $report .= $helper->__('City updated: %s', $sourceAddress['city'] . ' => ' . $response['city']);
            $sourceAddress['city'] = $response['city'];
        }
        if (strlen($report) > 0) {
            $sessionReport = $helper->__('The address has been corrected.') . ' ' .
            				 $helper->__('Country: %s', $sourceAddress['country']) . ', ' .
                             $helper->__('City: %s', $sourceAddress['city']) . ', ' .
                             $helper->__('ZIP: %s', $sourceAddress['zip']) . ', ' .
                             $helper->__('Street: %s', $sourceAddress['street']);
           $session = Mage::getSingleton('core/session');
           $session->setPayoneCreditRatingMessage($sessionReport);
        }
        return $sourceAddress;
    }

    /**
     * Builds params for Credit Rating request
     * @param $confirmadAddress
     * @param $quote
     * @return $params
     */
    protected function _buildCreditRatingRequest($confirmadAddress, $quote)
    {
        $params = $this->_initRequestParams();
        $params['request'] = 'consumerscore';
        $params['addresschecktype'] = 'NO';
        $params['consumerscoretype'] = $this->getConfigData('creditratinginquirytype');
        if (strlen($birthday = $quote->getCustomerDob()) > 0) {
            $params['birthday'] = substr($birthday, 0, 4) .
                                  substr($birthday, 5, 2) .
                                  substr($birthday, 8, 2) ;
        }
        // Call test address
        //$params = array_merge($params, $this->getTestAddress(1));
        // Get real address
        $params = array_merge($params, $confirmadAddress);
        return $params;
    }

    /**
     * Implements local cache
     * @param <type> $request
     * @return <type>
     */
    protected function _performRequest($request)
    {
        Mage::helper('payone')->debug('Request: ' . var_export($request, true), Mage_Payone_Helper_Data::DEBUG_CONFIG_CREDIT_RATING, Mage_Payone_Helper_Data::DEBUG_LEVEL_FULL);
        $response = array('source' => '');
        $session = Mage::getSingleton('customer/session');
        $key = $this->_getCacheKey($request);
        // Check in local cache
        if (isset($this->_localCache[$key])) {
            $response = $this->_localCache[$key];
            Mage::helper('payone')->debug('Response (from cache): ' . var_export($response, true), Mage_Payone_Helper_Data::DEBUG_CONFIG_CREDIT_RATING, Mage_Payone_Helper_Data::DEBUG_LEVEL_FULL);
            $response['source'] = 'SESSION CACHE';
            return $response;
        }
        // Check in DB for registered users
        // Only for check score request
        if (Mage::getSingleton('customer/session')->isLoggedIn() && $request['request'] == 'consumerscore') {
            $response = $this->_getStoredResponseData($session->getCustomerId(), $request);
            // Response empty if data is expared
            if (isset($response['status']) && $response['status'] == 'VALID') {
                $this->_localCache[$key] = $response;
                $this->_getSession()->setCreditRatingCache($this->_localCache);
                Mage::helper('payone')->debug('Response (from DB): ' . var_export($response, true), Mage_Payone_Helper_Data::DEBUG_CONFIG_CREDIT_RATING, Mage_Payone_Helper_Data::DEBUG_LEVEL_FULL);
                $response['source'] = 'DB';
                return $response;
            }
            // No stored data, continue
        }
        try {
            $response = $this->processApiCall($request, 10);
            $this->_localCache[$key] = $response;
            $this->_getSession()->setCreditRatingCache($this->_localCache);
            Mage::helper('payone')->debug('Response (from server): ' . var_export($response, true), Mage_Payone_Helper_Data::DEBUG_CONFIG_CREDIT_RATING, Mage_Payone_Helper_Data::DEBUG_LEVEL_FULL);
            $response['source'] = 'ONLINE REQUEST';
            if ($session->isLoggedIn() &&
                $request['request'] == 'consumerscore') {
                $this->_updateUserData($response);
            }
        } catch (Exception $e) {
            $response['source'] = 'Exception during request:' . $e->getMessage();
            Mage::log('Exception during request:' . $e->getMessage());
            // Ignore any Exception. Request should be hidden
        }

        return $response;
    }

	/**
	 * Creates key for local cache by significant fields of request.
	 * @param  $request
	 * @return string $key
	 */
    protected function _getCacheKey($request)
    {
        $key = $request['request'] .
               $request['firstname'] .
               $request['lastname'] .
               $request['street'] .
               $request['zip'] .
               $request['city'] .
               $request['country'];
         $key = str_replace(' ', '', $key);
         return $key;
    }

    /**
     * Returns rating score for customer from DB
     * @param <type> $customerId
     * @param <type> $request
     * @return <type>
     */
    protected function _getStoredResponseData($customerId, $request)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $response = array();

        $billingAddress = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();

        $customerAddress = $customer->getAddressById($billingAddress->getCustomerAddressId());
        if (!$this->_compareAddreeses($billingAddress, $customerAddress)) {
            return array();
        }

        if (is_null($customerAddress->getCustomerAddressPayoneCreditRatingDate()) || $this->getConfigData('storedata') == self::STORE_ONLY_DATE) {
            return array();
        }

        $requestTime = strtotime($customerAddress->getCustomerAddressPayoneCreditRatingDate());
        $now = strtotime(now());
        $interval = $now - $requestTime;
        $score = $customerAddress->getCustomerAddressPayoneCreditRatingScore();
        $lifeTime = $this->getConfigData('storelifetime');
        if ($interval < $lifeTime * 24 * 3600 && ($score == self::CREDIT_RATING_SCORE_GREEN ||
            $score == self::CREDIT_RATING_SCORE_YELLOW || $score == self::CREDIT_RATING_SCORE_RED)) {
            $response['status'] = 'VALID';
            $response['score'] = $score;
        }
        return $response;
    }

    /**
     * Save rating score and debug data for registered user.
     * @param  $response
     * @return
     */
    protected function _updateUserData($response)
    {
        if (!isset($response['status']) || $response['status'] != 'VALID' ||
            empty($response['score'])) {
            return;
        }

		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId())
			return;

        $billingAddress = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress();

        if($billingAddress->getSaveInAddressBook()) {
            $session = Mage::getSingleton('core/session');
            $session->setCreditRatingResponse($response);
            return;
        }

        $address = $customer->getAddressById($billingAddress->getCustomerAddressId());
        if (!$address->getId())
			return;

        if (!$this->_compareAddreeses($billingAddress, $address))
            return;

        $address->setData('customer_address_payone_credit_rating_date', now());
        if ($this->getConfigData('storedata') == self::STORE_DATE_SCORE) {
            $address->setData('customer_address_payone_credit_rating_score', $response['score']);
        } else {
            $address->setData('customer_address_payone_credit_rating_score', '');
        }
        if ($this->getConfigData('storedebugresponse')){
            $debugInfo = empty($response['secscore']) ? '' : $response['secscore'];
            $address->setData('customer_address_payone_credit_rating_secscore', $debugInfo);
        } else {
            $address->setData('customer_address_payone_credit_rating_secscore', '');
        }

        $address->save();

        return $this;
    }

    /**
     * Retrieve information from configuration for payment method
     * @param <type> $field
     * @param <type> $methodCode
     * @return <type>
     */
    protected function _getMethodConfigData($field, $methodCode)
    {
        $path = 'payonecreditrating/' . $methodCode . '/'.$field;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
    }

    /**
     * Retrieve information from configuration
     * @param <type> $field
     * @return <type>
     */
    public function getConfigData($field, $storeId = null)
    {
        $path = 'payonecreditrating/general/' . $field;
        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * Returns allowed score rating for given method
     * @param  $methodCode
     * @return $score
     */
    public function getAllowedScoreForMethod($methodCode)
    {
        $allowedRed = explode(',', $this->getConfigData('allow_red_for_payment_methods'));
        foreach ($allowedRed as $allowedMethod ){
            if ($allowedMethod == $methodCode) {
                return self::CREDIT_RATING_SCORE_RED;
            }
        }
        $allowYellow = explode(',', $this->getConfigData('allow_yellow_for_payment_methods'));
        foreach ($allowYellow as $allowedMethod ){
            if ($allowedMethod == $methodCode) {
                return self::CREDIT_RATING_SCORE_YELLOW;
            }
        }
        return self::CREDIT_RATING_SCORE_GREEN;
    }

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function _compareAddreeses ($addreeses1, $addreeses2)
    {
        return Mage::helper('payone')->compareAddreeses($addreeses1, $addreeses2);
    }

	/**
	 * Get singleton of session model
	 *
	 * @return Mage_Core_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('core/session');
	}

    protected function _getPersonStatusMappedScore($quote)
    {
        $score = null;
        $addressCheck = Mage::getSingleton('payone/addresscheck');
        if (! $addressCheck->isActive()) {
            $score;
        }

        $billingaddress = $quote->getBillingAddress();
        $sourceAddress = Mage::helper('payone/addresscheck')->convertAddressToRequest($billingaddress);
        $params = $addressCheck->_initRequestParams();
        $request = array_merge($params, $sourceAddress);
        $cacheKey = $addressCheck->_getCacheKey($request);
        $addressCheckCache = $addressCheck->_getCheckout()->getPayouneAddresscheckCache();
        if (empty($addressCheckCache[$cacheKey]['personstatus'])) {
            return null;
        }
        $mapping = Mage::getSingleton('payone/config')->getPersonStatusMapping();
        if (empty($mapping[$addressCheckCache[$cacheKey]['personstatus']])) {
            return null;
        } else {
            return $mapping[$addressCheckCache[$cacheKey]['personstatus']];
        }
    }
}
?>