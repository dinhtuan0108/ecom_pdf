<?php
/**
 * Magento
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
class Mage_Payone_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Validate address attribute values
     *
     * @return bool|array
     */
	public function validate() 
	{
		$errors = parent::validate();

		// If there are already errors there's no need to do Addresscheck
        if(is_array($errors)) {
            return $errors;
        }

		$response = $this->helper()->doAddresscheck($this);

		if($response === true) {
			return true;
		}

		$errors = $this->helper()->_validateResponse($response, $this);

		if (empty($errors)) {
            return true;
        }

        return $errors;
	}

	/**
	 *
	 * @return Mage_Payone_Helper_Addresscheck
	 */
	protected function helper() {
		return Mage::helper('payone/addresscheck');
	}

}
