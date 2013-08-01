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
 * @copyright  Copyright (c) 2009 Matthias Walter
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Payone_Model_CreditRating
 *
 * @category   Mage
 * @package    Mage_Payone
 * @author     Matthias Walter <matthias.walter@noovias.com>
 */
class Mage_Payone_Model_Addresscheck extends Mage_Payone_Model_Abstract
{
    const ADDRESS_CHECK_TYPE_NO = 'NO';
    const ADDRESS_CHECK_TYPE_BA = 'BA';
    const ADDRESS_CHECK_TYPE_PE = 'PE';

    const ADDRESS_CHECK_STATUS_VALID	= 'VALID';
    const ADDRESS_CHECK_STATUS_INVALID	= 'INVALID';
    const ADDRESS_CHECK_STATUS_ERROR	= 'ERROR';

    const ADDRESS_CHECK_SECSTATUS_CORRECT		= '10';
    const ADDRESS_CHECK_SECSTATUS_CORRECTED		= '20';
    const ADDRESS_CHECK_SECSTATUS_UNCORRECTED	= '30';

    protected $_localCache			= array();
	protected $_supportedCountries	= array('DE','AT','CH','BE','CA','CZ','DK','FI','FR','HU','IT','LU','NL','NO','PL','PT','SK','ES','SE','US');

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $checkout = $this->_getCheckout();
        $this->_localCache = $checkout->getPayouneAddresscheckCache();
    }

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Check if if module enabled
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getConfigData('active');
    }

    /**
     * have to check this Address type
     *
     * @return boolean
     */
    public function haveToCheckAddressType($type)
    {
        return $this->getConfigData('check'.ucfirst($type).'Address');
    }

    /**
     * This method runs the Addresscheck
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return score
     */
    public function validateAddress(Mage_Customer_Model_Address_Abstract $address)
    {
		$result = array();

		// is the country of the address supported
		if(!in_array($address->getCountry(), $this->_supportedCountries)) {
			return true;
		}

		// Convert Customer Address to Params
		$sourceAddress = $this->helper()->convertAddressToRequest($address);

		// Check AddressCheck Type
        if ($this->getConfigData('addresschecktype') == self::ADDRESS_CHECK_TYPE_NO) {
			return true;
        }

		// Prepare Request Params
        $params = $this->_initRequestParams();
        $request = array_merge($params, $sourceAddress);

		// Do Request
        $response = $this->_performRequest($request);
		return $response;
	}

    /**
     * Utility method, builds and returns configurable params for request.
     * @return array $params
     */
    public function _initRequestParams()
    {
		$params	= parent::_initRequestParams();

        $params['request'] = 'addresscheck';
        $params['addresschecktype'] = $this->getConfigData('addresschecktype');
		$params['language'] = $this->getLocale();

        return $params;
    }

	/**
	 * Creates key for local cache by significant fields of request.
	 * @param  $request
	 * @return string $key
	 */
    public function _getCacheKey($request)
    {
        $key = $request['request'] .
               $request['firstname'] .
               $request['lastname'] .
               $request['street'] .
               $request['zip'] .
               $request['city'] .
               $request['country'];
               $key = str_replace('/s', '', $key);
        return $key;
    }

    /**
     * Implements local cache
     * @param <type> $request
     * @return <type>
     */
    protected function _performRequest($request)
    {
        Mage::helper('payone')->debug('Request: ' . var_export($request, true), Mage_Payone_Helper_Data::DEBUG_CONFIG_ADDRESS_CHECK, Mage_Payone_Helper_Data::DEBUG_LEVEL_FULL);
        $response = array();
        $key = $this->_getCacheKey($request);
        // Check in local cache
        if (isset($this->_localCache[$key])) {
            $response = $this->_localCache[$key];
            Mage::helper('payone')->debug('Response (from cache): ' . var_export($response, true), Mage_Payone_Helper_Data::DEBUG_CONFIG_ADDRESS_CHECK, Mage_Payone_Helper_Data::DEBUG_LEVEL_FULL);
            return $response;
        }
		// new Api Call
        try {
            $response = $this->processApiCall($request, 10);
            $this->_localCache[$key] = $response;
            $updatedKey = $this->getUpdatedKey($request, $response);
            if (!is_null($updatedKey) && $updatedKey != $key) {
                $this->_localCache[$updatedKey] = $response;
            }
            $checkout = $this->_getCheckout();
            $checkout->setPayouneAddresscheckCache($this->_localCache);
            Mage::helper('payone')->debug('Response (from server): ' . var_export($response, true), Mage_Payone_Helper_Data::DEBUG_CONFIG_ADDRESS_CHECK, Mage_Payone_Helper_Data::DEBUG_LEVEL_FULL);
        } catch (Exception $e) {
            Mage::log( 'Exception during request:' . $e->getMessage());
            // Ignore any Exception. Request should be hidden
        }

        return $response;
    }

    /**
     * Retrieve information from configuration
     * @param string $field
     * @return int
     */
    public function getConfigData($field, $storeId = null)
    {
        $path = 'payonecreditrating/addresscheck/' . $field;
        return Mage::getStoreConfig($path, $storeId);
    }

	public function getLocale() {
 		$locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
		if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales))
			$locale = $locale[0];
		else
			$locale = $this->getDefaultLocale();

		return $locale;
	}

	/**
	 *
	 * @return Mage_Payone_Helper_Addresscheck
	 */
	protected function helper() {
		return Mage::helper('payone/addresscheck');
	}

    public function getUpdatedKey($request, $response)
    {
        if (empty($response['status']) || $response['status'] != 'VALID' ) {
            return null;
        }
        $response['request'] = $request['request'];
        $response['firstname'] = $request['firstname'];
        $response['lastname'] = $request['lastname'];
        
        return $this->_getCacheKey($response);
    }

}