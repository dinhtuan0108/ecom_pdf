<?php
/**
 * Magento
 *
 * @category   Mage
 * @package    Mage_Payone
 * @copyright  Copyright (c) 2009 Matthias Walter
 * @copyright  Copyright (c) 2010 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Payone_Helper_Addresscheck
 *
 * @category   Mage
 * @package    Mage_Payone
 * @author     Matthias Walter <matthias.walter@noovias.com>
 * @author     Phoenix Medien GmbH & Co. KG <info@phoenix-medien.de>
 */
class Mage_Payone_Helper_Addresscheck extends Mage_Core_Helper_Data
{
	/**
	 *
	 * @param Mage_Customer_Model_Address_Abstract $address
	 * @return boolean|array
	 */
	public function doAddresscheck(Mage_Customer_Model_Address_Abstract $address)
	{
		$check = $this->getModelAddresscheck();
		if($check->isActive()) {
			if($check->haveToCheckAddressType($address->getAddressType())) {
				return $check->validateAddress($address);
			}
		}
		return true;
	}

    /**
     * Convert Mage Address to Address Fields for Request
     * @param $billingaddress
     * @return array $params
     */
    public function convertAddressToRequest(Mage_Customer_Model_Address_Abstract $address)
    {
        $params = array();
        $params['firstname']		= $address->getFirstname();
        $params['lastname']			= $address->getLastname();
        $params['street']			= $this->normalizeStreet($address->getStreet());
        $params['zip']				= $address->getPostcode();
        $params['city']				= $address->getCity();
        $params['telephonenumber']	= $address->getTelephone();
        $params['country']			= $address->getCountryId();
        if($address->getCountryId() == "US" || $address->getCountryId() == "CA"){
       		$params['state']		= $address->getRegionCode();
        }
        return $params;
    }

	/**
	 *
	 * @param array $response
	 * @return array
	 */
	public function _validateResponse(array $response, Mage_Customer_Model_Address_Abstract $origAddress)
	{
        if(empty($response)) {
            return true;
        }
		// Error During Request
        if ($response['status'] == Mage_Payone_Model_Addresscheck::ADDRESS_CHECK_STATUS_ERROR) {
			$message = $response['errorcode'].'<br>'.$response['errormessage'].'<br>'.$response['customermessage'].'<br>';
			$this->addToSessionPayoneMessage($message, 'error');
	        return true;
        }
		elseif ($response['status'] == Mage_Payone_Model_Addresscheck::ADDRESS_CHECK_STATUS_INVALID) {
			// Address is invalid
	        $result[] = $this->__('Address invalid - Please correct your Address');
	        $result[] = $response['customermessage'];
			return $result;
        }
		elseif($response['status'] == Mage_Payone_Model_Addresscheck::ADDRESS_CHECK_STATUS_VALID) {
			// address is valid but could be corrected
			if($response['secstatus'] == Mage_Payone_Model_Addresscheck::ADDRESS_CHECK_SECSTATUS_CORRECTED) {
				$mergedAddress = $this->_validateResponseAddress($origAddress, $response);
			}
	        return true;
		}
	}

    /**
     * Compare $billingaddress significant fields of source address
     * and address in response after address checking. If address was corrected,
     * save new address into session.
     * @param $sourceAddress
     * @param $response
     * @return array
     */
    public function _validateResponseAddress(Mage_Customer_Model_Address_Abstract $sourceAddress, $response)
    {
        $helper = Mage::helper('payone');
        $report = '';
        if ($this->normalizeStreet($sourceAddress->getStreet()) != $response['street']) {
            $report .= $helper->__('Street updated: %s', $sourceAddress->getStreet(-1) . ' => ' . $response['street']);
            $sourceAddress->setStreet(array($response['street']));
        }
        if ($sourceAddress->getPostcode() != $response['zip']) {
            $report .= $helper->__('ZIP updated: %s', $sourceAddress->getPostcode() . ' => ' . $response['zip']);
            $sourceAddress->setPostcode( $response['zip'] );
        }
        if ($sourceAddress->getCity() != $response['city']) {
            $report .= $helper->__('City updated: %s', $sourceAddress->getCity() . ' => ' . $response['city']);
            $sourceAddress->setCity( $response['city'] );
        }
        if (strlen($report) > 0) {
            $sessionReport = $helper->__('The address has been corrected.') . '<br>' .
            				 $helper->__('Country: %s', $sourceAddress->getCountryId()) . ',<br>' .
                             $helper->__('City: %s', $sourceAddress->getCity()) . ',<br>' .
                             $helper->__('ZIP: %s', $sourceAddress->getPostcode()) . ',<br>' .
                             $helper->__('Street: %s', $sourceAddress->getStreet(-1)). ',<br>';
			$this->addToSessionPayoneMessage($sessionReport, $sourceAddress->getAddressType());
        }
        return $sourceAddress;
    }

	public function addToSessionPayoneMessage($message, $type)
	{
		$session = Mage::getSingleton('core/session');
		$sessionMessage = $session->getPayoneAddressCheckMessage();
		if($sessionMessage == null) {
			$sessionMessageArray = array();
		}
		else {
			$sessionMessageArray = unserialize($sessionMessage);
		}

		$sessionMessageArray[$type] = $message;

		$sessionMessage = serialize($sessionMessageArray);

		$session->setPayoneAddressCheckMessage($sessionMessage);
	}

	/**
	 *
	 * @return Mage_Payone_Model_Addresscheck
	 */
	public function getModelAddresscheck() {
		return Mage::getSingleton('payone/addresscheck');
	}

    public function normalizeStreet($street)
    {
        if (!is_array($street)) {
            return $street;
        }
        return implode(' ', $street);
    }

}