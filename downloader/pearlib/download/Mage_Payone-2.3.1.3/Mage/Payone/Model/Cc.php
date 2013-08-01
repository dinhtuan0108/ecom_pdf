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
class Mage_Payone_Model_Cc extends Mage_Payone_Model_Abstract
{
	/**
	* unique internal payment method identifier
	*
	* @var string [a-z0-9_]
	**/
    protected $_code            = 'payone_cc';
    protected $_formBlockType   = 'payone/form';
    protected $_infoBlockType   = 'payone/info';
    protected $_paymentMethod   = 'cc';
    protected $_canSaveCc = true;

    /**
     *  Owerride constructor to choose form block.
     */
	public function __construct()
	{
		parent::_construct();
        if ($this->canAuthorize()) {
            $this->_formBlockType = 'payone/formCc';
            $this->_infoBlockType = 'payone/infoCc';
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
        $info->setCcType($data->getCcType())
            ->setCcOwner($data->getCcOwner())
            ->setCcLast4(substr($data->getCcNumber(), -4))
            ->setCcNumber($data->getCcNumber())
            ->setCcExpMonth($data->getCcExpMonth())
            ->setCcExpYear($data->getCcExpYear())
            ->setPoNumber($data->getCcAdditionalData());;
        return $this;
    }

    /**
     * Prepare info instance for save
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        if ($this->_canSaveCc) {
            $info->setCcNumberEnc($info->encrypt($info->getCcNumber()));
        }
        $info->setCcNumber(null)
            ->setCcCid(null);
        return $this;
    }
    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    protected function _getAuthorizeParams(Mage_Sales_Model_Order $order)
    {
        $params = parent::_getAuthorizeParams($order);
        if (!$this->canAuthorize()) {
            return $params;
        }

        $params['autosubmit'] = 'yes';
            // additional CC fields (pan and cvc will be filled in the frontend)
        $info = $this->getInfoInstance();
        $params['cardholder'] = $info->getCcOwner();
        $params['cardpan'] = '';
        if ($info->getPoNumber()){
            $params['pseudocardpan'] = $info->getPoNumber();
        }
        $params['cardtype'] = substr($info->getCcType(), 0, 1);
        $params['cardexpiremonth'] = $info->getCcExpMonth();
        $params['cardexpireyear'] = $info->getCcExpYear();
        $params['cardcvc2'] = '';

        // prevent 3D Secure payment on admin orders
        if (Mage::app()->getStore()->isAdmin()) {
            $params['eci'] = '00';
        }

        return $params;
    }
    /**
     * Prepare params for AJAX API call
     * @return <type>
     */
    public function getCcInitFields()
    {
 		$locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
		if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales))
			$locale = $locale[0];
		else
			$locale = $this->getDefaultLocale();
		$params = 	array(
                        'request'       =>  'creditcardcheck',
                        'storecarddata' =>  'yes',
						'portalid'		=>	$this->getConfigData('portal_id'),
						'aid'			=>	$this->getConfigData('account_id'),
                        'mid'			=>	$this->getConfigData('merchant_id'),
						'mode'			=>	$this->getConfigData('transaction_mode'),
						'language'		=>	$locale,
						'encoding'		=>	'UTF-8',
					);
        $params['hash'] = $this->generateHash($params);
        return $params;
    }

    protected function _processAuthorizeResponse($payment, $response, $stateObject)
    {
        if ($this->getConfigData('request_type') == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE) {
            // only create invoice if payment has been authorization
            if ($payment->getOrder()->canInvoice()) {
                $invoice = $payment->getOrder()->prepareInvoice() // instantiate new invoice from available items
                                   ->register() // update totals (except total_paid)
                                   ->pay(); // update *_paid totals
                $invoice->setTransactionId($response['txid']);
                $this->getOrder()->addRelatedObject($invoice);
            }
            $payment->getOrder()->setCustomerNote(Mage::helper('payone')->__('The amount has been authorized and captured by PAYONE.'));
        } else {
            $payment->getOrder()->setCustomerNote(Mage::helper('payone')->__('The amount has been preauthorized by Payone.'));
        }
        $stateObject->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
        $stateObject->setStatus($this->getConfigData('order_status', $payment->getOrder()->getStoreId()));
        $stateObject->setIsNotified(true);
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

}