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
class Mage_Payone_Model_Elv extends Mage_Payone_Model_Abstract
{  
	/**
	* unique internal payment method identifier
	*
	* @var string [a-z0-9_]
	**/
	protected $_code			= 'payone_elv';   
	protected $_formBlockType	= 'payone/form';
	protected $_infoBlockType	= 'payone/info';
	protected $_paymentMethod	= 'elv';
    /**
     *  Owerride constructor to choose form block.
     */
	public function __construct()
	{
		parent::_construct();
        if ($this->canAuthorize()) {
            $this->_formBlockType = 'payone/formElv';
            $this->_infoBlockType = 'payone/infoElv';
        }
	}
    
    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}
		$info = $this->getInfoInstance();

		$info->setCcOwner($data->getOwner())
			->setCcLast4(substr($data->getAccountNumber(), -4))
            ->setCcNumberEnc($info->encrypt($data->getAccountNumber()))
			->setPoNumber($data->getBankCode());
		return $this;
    }
	/**
	 * prepare params array to send it to gateway page via POST
	 * @param Mage_Sales_Model_Order $order
     * @return array
     */

    protected function _getAuthorizeParams(Mage_Sales_Model_Order $order)
	{
		$params = parent::_getAuthorizeParams($order);
        if (!$this->canAuthorize()) {            
            return $params;
        }
        $params['autosubmit'] = 'yes';
		$info = $this->getInfoInstance();
		$params['bankaccountholder'] = $info->getCcOwner();
        $params['bankcountry'] = $params['country'];
		$params['bankaccount'] = $info->decrypt($info->getCcNumberEnc());
		$params['bankcode'] = $info->getPoNumber();		
		return $params;
	}
    /**
     * Build message to save in order history
     * @param array $response
     * @return string
     */
    protected function _getStatusHistoryMessage($response)
    {
        $message = $this->_getHelper()->__('Payment was processed with PAYONE API. Payment reference: %s', $response['txid']);
        return $message;
    }
 
    /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function validate()
    {
        parent::validate();
        if ($this->getConfigData('validate_bankcode') && $this->canAuthorize()) {
            $params = $this->getBankaccountcheckParams();
            
        	if ($this->_calculateValidateParamsCheckSum($params)==$this->_getValidatedForCurrentSession()) {
            	// Skip extra API request if address is already validated for certain $params
            	return;
            }
            
            $response = $this->processApiCall($params);
            if ($response['status'] == 'INVALID' || $response['status'] == 'ERROR') {
                Mage::throwException($response['customermessage']);
            } elseif ($response['status'] == 'BLOCKED') {
                Mage::throwException($this->_getHelper()->__('Account is blocked.'));
            }
            else {
            	// Cache API VALID response for certain $params
            	$this->_setValidatedForCurrentSession($this->_calculateValidateParamsCheckSum($params));
            }
        }
    }
    /**
     * Prepare params for bankaccountcheck API call
     * @return array
     */
    protected function getBankaccountcheckParams()
    {
        $info = $this->getInfoInstance();

        if ($info instanceof Mage_Sales_Model_Order_Payment) {
            $billingCountry = $info->getOrder()->getBillingAddress()->getCountryId();
        } else {
            $billingCountry = $info->getQuote()->getBillingAddress()->getCountryId();
        }

        $errorMsg = false;

 		$locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
		if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales))
			$locale = $locale[0];
		else
			$locale = $this->getDefaultLocale();

        $params = parent::_initRequestParams();

        $params['request']     = 'bankaccountcheck';
        $params['bankaccount'] = $info->decrypt($info->getCcNumberEnc());
        $params['bankcountry'] = $billingCountry;
        $params['language']    = $locale;
        $params['checktype']    = $this->getConfigData('bank_account_validation_type');
        
        if ($params['bankcountry'] == 'DE' || $params['bankcountry'] == 'AT') {
            $params['bankcode'] = $info->getPoNumber();
        }
        return $params;
    }

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
       return $this->getConfigData('payment_action') == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
    }
    
    protected function _getValidatedForCurrentSession() {
    	return $this->_getSession()->getValidatedForCurrentSession();
    }
    
	protected function _setValidatedForCurrentSession($checksum) {
		if (!$checksum) {
			return false;
		}
    	$this->_getSession()->setValidatedForCurrentSession($checksum);
    }
    
	protected function _calculateValidateParamsCheckSum($params) {
		if (!is_array($params)) {
			return false;
		}
		return md5(serialize($params));
    }

}