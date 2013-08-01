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
abstract class Mage_Payone_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    **/
    protected $_code = 'payone_abstract';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = true;
    protected $_paymentMethod           = 'abstract';
    protected $_defaultLocale           = 'en';
    protected $_supportedLocales        = array('en', 'de', 'fr', 'nl', 'it', 'es', 'pt');

    protected $_integratorId            = '2018000';
    protected $_protected_keys          = array('aid','portalid','mid','mode','request','clearingtype','reference','customerid','param',
                                                'narrative_text','successurl','errorurl','backurl','storecarddata',
                                                'encoding','display_name','display_address','autosubmit','targetwindow',
                                                'amount','currency','due_time','invoiceid','invoiceappendix','invoice_deliverymode','eci',
                                                'id','pr','no','de','ti','va',
                                                'productid','accessname','accesscode',
                                                'access_expiretime','access_canceltime','access_starttime','access_period','access_aboperiod',
                                                'access_price','access_aboprice','access_vat',
                                                'settleperiod','settletime','vaccountname','vreference');

    protected $_order;

    protected $_requestParameters = null;

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
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

    /**
     * Disable redirection for API mode if there was not redirection response.
     * @return <type>
     */
    public function getOrderPlaceRedirectUrl()
    {
        if ($this->canAuthorize() && !$this->_getSession()->getPayoneRedirectUrl()) {
            return '';
        } else {
            return Mage::getUrl('payone/processing/redirect');
        }
    }

    /**
     * Authorize is alloved for API mode
     * @return boolen
     */
    public function canAuthorize()
    {
       return $this->getConfigData('payment_action') == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        if ($this->canCapture()) {
            // check request. maybe capturing (PMI). do nothing.
            if (Mage::app()->getRequest()->getParam('txaction')) {
                $payment->setStatus(self::STATUS_APPROVED)
                    ->setLastTransId($this->getTransactionId());
                return $this;
            }
            // make API call
            $params = $this->_getCaptureParams($payment, $amount);
            $response = $this->processApiCall($params);
            if (isset($response['status']) && $response['status'] == 'APPROVED') {
                $payment->getOrder()->addStatusToHistory($payment->getOrder()->getStatus(), $this->_getHelper()->__('Payone transaction has been captured.'));
                $this->increasePaymentSequenceNumber($payment);
            } else {
                // throw error if API call was not successfull
                $msg = $this->_getHelper()->__('Payone capturing was not successful.');
                if (isset($response['errorcode'])) {
                    $msg .= ' ('.$response['errorcode'].')';
                }
                Mage::throwException($msg);
            }
        } else {
                // capturing not supported for payment type. just save transaction id.
            $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());
        }
        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setLastTransId($this->getTransactionId());
        return $this;
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }

    public function getUrl()
    {
        $payoneRedirectUrl = $this->_getSession()->getPayoneRedirectUrl();
        if ($this->canAuthorize() && $payoneRedirectUrl) {
            return $payoneRedirectUrl;
        }
        return (string)Mage::getConfig()->getNode('global/payment/payone/urls/frontend')->asArray();
    }
    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */

    public function getFormFields()
    {
        $params = $this->_initRequestParams();
        //removing non-public params
        unset($params['mid']);
        unset($params['key']);

        $params = array_merge($params,$this->_getAuthorizeParams($this->getOrder()));
        // add security hash to params
        $params['hash'] = $this->generateHash($params);
        return $params;
    }

    /**
     * Returns common auth requests params
     * @param Mage_Sales_Model_Order $order
     * @return array
     */

    protected function _getAuthorizeParams(Mage_Sales_Model_Order $order)
    {
        if ($order != $this->getOrder()){
            $this->_order = $order;
        }

        $amount     = number_format($this->getOrder()->getGrandTotal()*100,0,'.','');
        $billing    = $this->getOrder()->getBillingAddress();
        $shipping   = $this->getOrder()->getShippingAddress();

        $locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
        if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales)) {
            $locale = $locale[0];
        } else {
            $locale = $this->getDefaultLocale();        }

        //Common params
        $params = $this->_getCommonParams();

        //Auth request specific params
        $params =   array_merge($params, array(
                        'request'       =>  ($this->getConfigData('request_type') == self::ACTION_AUTHORIZE) ? 'preauthorization' : 'authorization',
                        'clearingtype'  =>  $this->_paymentMethod,
                        'amount'        =>  $amount,
                        'reference'     =>  $this->getOrder()->getRealOrderId(),
                        'narrative_text'    => $this->getOrder()->getRealOrderId(),
                        'invoiceappendix'   => $this->getOrder()->getRealOrderId(),
                        'customerid'    =>  $billing->getCustomerId(),
                        'firstname'     =>  $billing->getFirstname(),
                        'lastname'      =>  $billing->getLastname(),
                        'company'       =>  $billing->getCompany(),
                        'street'        =>  Mage::helper('payone/addresscheck')->normalizeStreet($billing->getStreet()),
                        'zip'           =>  $billing->getPostcode(),
                        'city'          =>  $billing->getCity(),
                        'country'       =>  $billing->getCountry(),
                        'email'         =>  $this->getOrder()->getCustomerEmail(),
                        'telephonenumber'   =>  $billing->getTelephone(),
                        'language'      =>  $locale,
                        'param'         =>  $this->getOrder()->getRealOrderId(),
                        'display_name'  =>  ($this->getConfigData('display_name') ? 'yes' : 'no'),
                        'display_address'   =>  ($this->getConfigData('display_address') ? 'yes' : 'no'),
                        'successurl'    =>  Mage::getUrl('payone/processing/success', array('_nosid' => true)),
                        'errorurl'      =>  Mage::getUrl('payone/processing/error', array('_nosid' => true)),
                        'backurl'       =>  Mage::getUrl('payone/processing/cancel', array('_nosid' => true)),
                    )
        );
        if (is_object($shipping)) {
            $params = array_merge($params, array(
                            'shipping_firstname'    =>      $shipping->getFirstname(),
                            'shipping_lastname'     =>      $shipping->getLastname(),
                            'shipping_company'      =>      $shipping->getCompany(),
                            'shipping_street'       =>      Mage::helper('payone/addresscheck')->normalizeStreet($shipping->getStreet()),
                            'shipping_zip'          =>      $shipping->getPostcode(),
                            'shipping_city'         =>      $shipping->getCity(),
                            'shipping_country'      =>      $shipping->getCountry(),
                            )
                      );
        }
        //adding items data to request parameters
        $params = array_merge(
            $this->_getRequestItemsParams($this->getOrder(),$amount),$params);

        return $params;
    }

    /**
     * prepares capture request parameters
     *
     * @return array
     */
    protected function _getCaptureParams(Varien_Object $payment, $amount)
    {
        $amount = number_format($amount*100,0,'.','');

        //Common params
        $params = $this->_initRequestParams();
        $params = array_merge($this->_getCommonParams(),$params);

        $params['request'] = 'capture';
        $params['txid'] = $payment->getLastTransId();
        $params['amount'] = $amount;

        // set increment id since it doesn't exist yet
        $invoice = Mage::registry('current_invoice');
        if ($this->getConfigData('submit_documentid')) {
            $this->setNewIncrementId($invoice, 'invoice');
            if ($invoice->getIncrementId()) {
                $params['invoiceid'] = $invoice->getIncrementId();
            }
        }

        //adding items data to request parameters
        $params = array_merge(
            $this->_getRequestItemsParams($invoice,$amount),$params);

        return $params;
    }

    /**
     * prepares refund request parameters
     *
     * @return array
     */
    protected function _getRefundParams(Varien_Object $payment, $amount)
    {
        $amount = number_format($amount*100,0,'.','');
        //Common params
        $params = $this->_initRequestParams();
        $params = array_merge($this->_getCommonParams(),$params);

        $params['request'] = 'debit';
        $params['txid'] = $payment->getLastTransId();
        // Use nex value of Sequence Number
        $params['sequencenumber'] = $payment->getSequenceNumber() + 1;
        $params['amount'] = $amount*(-1);

        // set increment id since it doesn't exist yet
        $creditmemo = $payment->getCreditmemo();
        if ($this->getConfigData('submit_documentid')) {
            $this->setNewIncrementId($creditmemo, 'creditmemo');
            if ($creditmemo->getIncrementId()) {
                $params['invoiceid'] = $creditmemo->getIncrementId();
            }
        }

        //adding items data to request parameters
        $params = array_merge(
            $this->_getRequestItemsParams($creditmemo,$amount),$params);

        return $params;
    }

    /**
     * Utility method, builds and returns configurable params for request.
     * @return array $params
     */
    protected function _initRequestParams()
    {
        if (is_null($this->_requestParameters)){
        $this->_requestParameters   =   array(
                        'mid'           =>  $this->getConfigData('merchant_id'),
                        'portalid'      =>  $this->getConfigData('portal_id'),
                        'aid'           =>  $this->getConfigData('account_id'),
                        'key'           =>  md5($this->getConfigData('security_key')),
                        'mode'          =>  $this->getConfigData('transaction_mode'),
                        'encoding'      =>  'UTF-8',
                        'integratorid'  =>  $this->_integratorId,
                        'integratorver' =>  Mage::getVersion(),
                        'integratorextver' => Mage::getConfig()->getNode('modules/Mage_Payone/version')
                    );
        }
        return $this->_requestParameters;
    }

    /**
     * Returns common request params for payment instance
     * @param Varien_Object $payment
     * @return array
     */

    protected function _getCommonParams(){
        $params = array(
            'currency' => $this->getOrder()->getOrderCurrencyCode()
        );
        return $params;
    }

    /**
     * Returns order/invoice/creditmemo items data
     * if form of an array for API request
     * @param Varien_Object $salesObject
     * @return array();
     */

    protected function _getRequestItemsParams(Varien_Object $salesObject, $amount){
        //items' parameters dummy
        $params = array(
                            'id'        =>  array(),
                            'de'        =>  array(),
                            'pr'        =>  array(),
                            'no'        =>  array(),
                            'va'        =>  array()
        );
        //init product counter
        $i = 1;
        // submit products to the payment provider?
        if ($this->getConfigData('submit_products') == 1) {
            //grabbing salesObject specific items
            if ($salesObject instanceof Mage_Sales_Model_Order){
                $items = $salesObject->getAllVisibleItems();
            }elseif ($salesObject instanceof Mage_Sales_Model_Order_Invoice ||
                     $salesObject instanceof Mage_Sales_Model_Order_Creditmemo){
                $items = $salesObject->getAllItems();
            }else{
                return $params;
            }
            $total = 0;
            // add items
            foreach ($items as $item) {
                $params['id'][$i] = $item->getSku();
                $params['de'][$i] = $item->getName();
                $params['pr'][$i] = number_format((Mage::helper('checkout')->getPriceInclTax($item))*100,0,'.','');
                if ($salesObject instanceof Mage_Sales_Model_Order){
                    $params['no'][$i] = round($item->getQtyToInvoice());
                } else {
                    $params['no'][$i] = round($item->getQty());
                }
                if (!($salesObject instanceof Mage_Sales_Model_Order)) {
                    // Invoice and CreditMemo item does't have tax information. Need to load order item;
                    $orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
                    $params['va'][$i] = round($orderItem->getTaxPercent()*100, 0);
                } else {
                    $params['va'][$i] = round($item->getTaxPercent()*100, 0);
                }
                $total += $params['pr'][$i] * $params['no'][$i];
                $i++;
            }

            // add discount
            if ($this->getDiscountAmount() > 0) {
                $params['id'][$i] = '999';
                $params['de'][$i] = Mage::helper('payone')->__('Discount code') . ': ' . $this->getOrder()->getCouponCode();
                $params['pr'][$i] = number_format($salesObject->getDiscountAmount()*100*(-1),0,'.','');
                $params['no'][$i] = 1;
                //              $params['va'][$i] = $item->getTaxClassId();
                $total += $params['pr'][$i];
                $i++;
            }

            // add shipping fee
            $shippingAmount = number_format(($salesObject->getShippingAmount()+$salesObject->getShippingTaxAmount())*100,0,'.','');
            $params['id'][$i] = $this->getOrder()->getShippingMethod();
            $params['de'][$i] = $this->getOrder()->getShippingDescription();
            $params['pr'][$i] = $shippingAmount;
            $params['no'][$i] = 1;
            $params['va'][$i] = round($this->getShippingTaxRate($this->getOrder()->getBillingAddress())*100, 0);
            $total += $params['pr'][$i];

            // fix rounding error
            if ($total != $amount) {
                $priceDiff = $total - $amount;
                // make adjust in shipping fee
                $params['pr'][$i] = $params['pr'][$i] - $priceDiff;
            }
        }
        // add the whole basket as one article
        else {
            $params['id'][$i] = '000';
            $params['de'][$i] = Mage::helper('payone')->__('Shopping basket');
            $params['pr'][$i] = $amount;
            $params['no'][$i] = 1;
        }
        return $params;
    }

    protected function generateHash($data)
    {
        $hashstr = '';
        $keys = $this->_protected_keys;
        sort($keys);
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                if (is_array($data[$key])) {
                    ksort($data[$key]);
                    foreach ($data[$key] as $id => $val) {
                        $hashstr .= $val;
                    }
                }
                else {
                    $hashstr .= $data[$key];
                }
            }
        }
        $hashstr .= $this->getConfigData('security_key');
        $hash=md5($hashstr);
        return $hash;
    }

    protected function getApiUrl()
    {
        return (string)Mage::getConfig()->getNode('global/payment/payone/urls/api')->asArray();
    }

    protected function processApiCall($params, $requestTimeout = 30)
    {
        try {
            $client = new Varien_Http_Client();
            $client->setUri($this->getApiUrl())
                ->setConfig(array('timeout'=>$requestTimeout,))
                ->setParameterPost($params)
                ->setMethod(Zend_Http_Client::POST);

            $response = $client->request();
            $responseBody = $response->getBody();

            if (empty($responseBody)) {
                Mage::throwException($this->_getHelper()->__('Payone API failure. The request has not been processed.'));
            }

            // create array out of response
            $params = array();
            $lines = explode("\n", trim($responseBody));
            foreach($lines as $line) {
                $tokens = explode('=', $line, 2);
                if (is_array($tokens)) {
                    $params[$tokens[0]] = $tokens[1];
                }
            }

        } catch (Exception $e) {
            if (!isset($responseBody)) {
                $responseBody = '';
            }
            Mage::log('Exception during Payone API call. Request: ' . print_r($params, 1) . ' Response body: ' . $responseBody);
            Mage::throwException($this->_getHelper()->__('Payone API connection error. The request has not been processed:' . $e->getMessage()));
        }

        return $params;
    }

    protected function getShippingTaxRate($billingaddress)
    {
        $store = Mage::getSingleton('checkout/session')->getQuote()->getStore();
        $custTaxClassId = Mage::getSingleton('checkout/session')->getQuote()->getCustomerTaxClassId();
        $taxCalculationModel = Mage::getSingleton('tax/calculation');
        $request = $taxCalculationModel->getRateRequest($billingaddress, $billingaddress, $custTaxClassId, $store);
        $shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
        if ($shippingTaxClass) {
            return $taxCalculationModel->getRate($request->setProductClassId($shippingTaxClass));
        }

        return 0;
    }
    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */

    //public function authorize(Varien_Object $payment, $amount)
    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();
        $params = $this->_initRequestParams();
        $params = array_merge($this->_getAuthorizeParams($payment->getOrder()), $params);
        // make API call
        $response = $this->processApiCall($params);
        $this->_getSession()->unsPayoneRedirectUrl();
        if ($response['status'] ==  'APPROVED') {
            $response['amount'] = round($payment->getOrder()->getGrandTotal(), 2);
            $response['currency'] = $params['currency'];
            $response['method_code'] = $this->_code;
            $this->_getSession()->setAuthorizeResponse($response);
            $payment->setLastTransId($response['txid']);
            $this->_processAuthorizeResponse($payment, $response, $stateObject);
        } elseif ($response['status'] ==  'REDIRECT') {
            $this->_getSession()->setPayoneRedirectUrl($response['redirecturl']);
            $payment->setLastTransId($response['txid']);
            // Set order state
            $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
            $stateObject->setStatus(Mage::helper('payone')->getPendingPaymentStatus());
            $stateObject->setIsNotified(true);
            $payment->getOrder()->setCustomerNote(Mage::helper('payone')->__('Customer was redirected to Payone.'));
        } else {
            $errorMessage = 'Error during payone authorize API request. Errorcode: ' .
            $response['errorcode'] . ' Message: ' . $response['errormessage'];
            Mage::log($response);
            Mage::log($errorMessage);
            if (Mage::app()->getStore()->isAdmin()) {
                Mage::throwException($response['errormessage']);
            }
            Mage::throwException($response['customermessage']);
        }
        return $this;
    }

    /**
     * Build message to save in order history
     * @param array $response
     * @return string
     */
    protected function _getStatusHistoryMessage($response)
    {
        return Mage::helper('payone')->__('Processed with PAYONE API');
    }

    /**
     * Before refund
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Payone_Model_Abstract
     */

    public function processBeforeRefund($invoice, $payment)
    {
        $payment->setInvoice($invoice);
        return parent::processBeforeRefund($invoice, $payment);
    }

    /**
     * Refund a capture transaction
     *
     * @param Varien_Object $payment
     * @param float $amount
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($payment->getRefundTransactionId() && $amount>0) {
            // make API call
            $params = $this->_getRefundParams($payment, $amount);
            $response = $this->processApiCall($params);
            if (isset($response['status']) && $response['status'] == 'APPROVED') {
                $payment->setStatus('SUCCESS');
                $this->increasePaymentSequenceNumber($payment);
                return $this;
            }else{
                $errorMessage = Mage::helper('payone')->__('Unable to refund payment');
                if (isset($response['errormessage'])){
                    $errorMessage .= ': '.$response['errormessage'];
                }
                Mage::throwException(Mage::helper('payone')->__($errorMessage));
            }
        } else {
            Mage::throwException(Mage::helper('paypal')->__('Impossible to issue a refund transaction, because capture transaction does not exist.'));
        }
    }

    /**
     * Increases payment sequence_number
     * @param Varien_Object $payment
     * @return int
     */
    public function increasePaymentSequenceNumber(Varien_Object $payment, $setValue = null)
    {
        //updating payment sequence_number
        if (!is_null($setValue) && $setValue >= $payment->getSequenceNumber()) {
            // we should not allow decrease value
            $sequenceNumber = $setValue;
        } else {
            $sequenceNumber = $payment->getSequenceNumber();
            $sequenceNumber = is_null($sequnceNumber) ? 1 : ++$sequenceNumber;
        }
        $payment->setSequenceNumber($sequenceNumber);
        return $sequenceNumber;
    }

    protected function _processAuthorizeResponse($payment, $response, $stateObject)
    {
        $order = $payment->getOrder();
        if ($this->getConfigData('request_type', $order->getStoreId()) == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE) {
            $message = Mage::helper('payone')->__('The payment has been appointed by PAYONE.');
        } else {
            // only create invoice if payment has been authorization
            if ($order->canInvoice()) {
                $invoice = $order->prepareInvoice() // instantiate new invoice from available items
                                  ->register() // update totals (except total_paid)
                                  ->pay(); // update *_paid totals
                $invoice->setTransactionId($response['txid']);
                $order->addRelatedObject($invoice);
            }
            $message = Mage::helper('payone')->__('The payment has been appointed by PAYONE. Invoice has been created.');
        }
        $stateObject->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
        $stateObject->setStatus($this->getConfigData('order_status', $order->getStoreId()));
        $stateObject->setIsNotified(true);
        $order->setCustomerNote($message);
    }

    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processAppointedStatusRequest($order, $data)
    {
        $newPaymentStatus = $this->getConfigData('order_status', $order->getStoreId());

        // skip if payment is not pending
        if ($order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            return;
        }

        if ($this->getConfigData('request_type', $order->getStoreId()) == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE) {
            $order->setState($newPaymentStatus, $newPaymentStatus, Mage::helper('payone')->__('The payment has been appointed by PAYONE.'), true);
        } else {
            // only create invoice if payment has been authorization
            if ($order->canInvoice()) {
                $invoice = $order->prepareInvoice();
                $invoice->register()->capture();
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();
            }
            $order->setState($newPaymentStatus, $newPaymentStatus, Mage::helper('payone')->__('The amount has been authorized and captured by PAYONE.'), true);
        }
        // set transaction ID for the order email
        $order->getPayment()->setLastTransId($data['txid']);
        // send new order email to customer.
        $order->sendNewOrderEmail();
        $order->setEmailSent(true);
    }

    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processCaptureStatusRequest($order, $data)
    {
        if(!empty($data['id'])) {
            // process only new capture from PMI
            // If capture performed in PMI, there is no 'id' array of items
            return;
        }
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->register()->capture();
            $invoice->setTransactionId($data['txid']);
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        }
        $order->addStatusToHistory($order->getStatus(), Mage::helper('payone')->__('The order amount has been captured in the PMI.'));
    }

    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processPaidStatusRequest($order, $data)
    {
        $order->addStatusToHistory(
           $order->getStatus(),
           Mage::helper('payone')->__('The PAYONE transaction has been marked as paid.')
        );
    }

    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processUnderpaidStatusRequest($order, $data)
    {
        $message   = Mage::helper('payone')->__('The PAYONE transaction is UNDERPAID.') . '<br/>';
        $message  .= Mage::helper('payone')->__('Balance: %s', $data['balance']) . '<br/>';
        $message  .= Mage::helper('payone')->__('Receivable: %s', $data['receivable']) . '<br/>';

        $order->setState(
           Mage_Sales_Model_Order::STATE_HOLDED,
           Mage_Sales_Model_Order::STATE_HOLDED,
           $message
        );
    }

    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processCancelationStatusRequest($order, $data)
    {
        // set order "ON HOLD" if it is not finished yet
        if ($order->canCancel()) {
            $order->addStatusToHistory(
              Mage_Sales_Model_Order::STATE_HOLDED,
              Mage::helper('payone')->__('The PAYONE transaction has been canceled.')
            );
        }
    }

    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processDebitStatusRequest($order, $data)
    {
        // There is no abstract implementation. Should be implemented in certine payment method
        return;
    }

    /**
     * get new increment ID
     */
    public function setNewIncrementId(Varien_Object $object, $entityType)
    {
        if ($object->getIncrementId()) {
            return $this;
        }

        $objectResource = $object->getResource();
        $entityType = Mage::getModel('eav/entity_type')->loadByCode($entityType);
        $incrementId = $entityType->fetchNewIncrementId($object->getStoreId());

        if (false!==$incrementId) {
            $object->setIncrementId($incrementId);
        }

        return $this;
    }
}