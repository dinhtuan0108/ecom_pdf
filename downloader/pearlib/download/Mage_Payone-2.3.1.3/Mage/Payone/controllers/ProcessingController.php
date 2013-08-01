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
class Mage_Payone_ProcessingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
	protected $_order;

    /**
     * Payment instance
     */
	protected $_paymentInst;

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
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
	 * When a customer chooses Payone on Checkout/Payment page he will
	 * be redirected to payment page.
	 */
	public function redirectAction()
	{
        try {
    		$session = $this->_getCheckout();

    		$order = Mage::getModel('sales/order');
    		$order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            if ($order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    $this->_getPendingPaymentStatus(),
                    Mage::helper('payone')->__('Customer was redirected to Payone.')
                )->save();
            }

            if ($session->getQuoteId() && $session->getLastSuccessQuoteId()) {
                $session->setPayoneQuoteId($session->getQuoteId());
                $session->setPayoneLastSuccessQuoteId($session->getLastSuccessQuoteId());
                $session->setPayoneLastRealOrderId($session->getLastRealOrderId());
                $session->getQuote()->setIsActive(false)->save();
                $session->clear();
            }

            $this->loadLayout();
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckout()->addError($e->getMessage());
        } catch(Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('checkout/cart');
	}

	/**
	 * Payone should have called statusAction already. Check if that's
	 * the case and redirect customer to success page.
	 */
	public function successAction()
	{
		try {
        	// load quote and order
        	$this->_loadCheckoutObjects();

			// if order is canceled
			if ($this->_order->getStatus() == Mage_Sales_Model_Order::STATE_CANCELED) {
				$this->cancelAction();
				return;
			}

			/**
			 * Get sure order status has changed since redirect. The payment WLT
			 * is a exception since the payment status (callback) might not
			 * performed yet for technical reasons.
			 */
			if (($this->_order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) &&
				 $this->_order->getPayment()->getMethodInstance()->getPaymentMethodType() != 'wlt' &&
                 $this->_order->getPayment()->getMethodInstance()->getPaymentMethodType() != 'vor' &&
                 $this->_order->getPayment()->getMethodInstance()->getPaymentMethodType() != 'sb' ) {
				Mage::throwException(Mage::helper('payone')->__('Sorry, your payment has not been confirmed by the payment provider.'));
            }

			// payment is okay. show success page.
            $this->_getCheckout()->setLastSuccessQuoteId($this->_order->getQuoteId());
			$this->_redirect('checkout/onepage/success');
			return;
        } catch(Mage_Core_Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('payone')->__($e->getMessage()));
		} catch(Exception $e) {
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
	}

    /**
     * An error occured during the payment process.
     * Cancel order and redirect user to the shopping cart.
     */
    public function errorAction()
    {
    	try {
       		// load quote and order
        	$this->_loadCheckoutObjects();

       		// cancel order
        	if ($this->_order->canCancel()) {
	            $this->_order->cancel();
    	        $this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, Mage::helper('payone')->__('An error occured during the payment process.'));
        	    $this->_order->save();
        	}

       		// add error message
	        $this->_getCheckout()->addError(Mage::helper('payone')->__('An error occured during the payment process.'));

	    } catch(Mage_Core_Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('payone')->__($e->getMessage()));
        } catch(Exception $e) {
            Mage::logException($e);
        }

        // redirect customer to cart
       $this->_redirect('checkout/cart');
    }

    /**
     * Payment has been canceled at PAYONE.
     * Cancel order and redirect user to the shopping cart.
     */
    public function cancelAction()
    {
    	try {
       		// load quote and order
        	$this->_loadCheckoutObjects();

       		// cancel order
        	if ($this->_order->canCancel()) {
	            $this->_order->cancel();
    	        $this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, Mage::helper('payone')->__('The Payone transaction has been canceled.'));
        	    $this->_order->save();
        	}

        	// set quote to active
            if ($quoteId = $this->_getCheckout()->getQuoteId()) {
                $quote = Mage::getModel('sales/quote')->load($quoteId);
                if ($quote->getId()) {
                    $quote->setIsActive(true)->save();
                }
            }

       		// add error message
	        $this->_getCheckout()->addError(Mage::helper('payone')->__('The order has been canceled.'));

    	} catch(Mage_Core_Exception $e) {
            $this->_getCheckout()->addError(Mage::helper('payone')->__($e->getMessage()));
        } catch(Exception $e) {
            Mage::logException($e);
        }

        // redirect customer to cart
        $this->_redirect('checkout/cart');
    }

	/**
	 * Process transaction status messages
	 */
	public function statusAction()
	{
		try {
			/** verify call */
			$data = $this->_getTransactionRequest();
            //Mage::log('Data: ' . print_r($data, 1));
			// process action
            $sequnceNumber = empty($data['sequencenumber']) ? 0 : $data['sequencenumber'];
            $this->_paymentInst->increasePaymentSequenceNumber($this->_order->getPayment(), $sequnceNumber);
			switch ($data['txaction']) {
				case 'appointed':
                    $this->_paymentInst->processAppointedStatusRequest($this->_order, $data);
					break;
				case 'capture':
					// this event can either be intialisized by the
					// Magento backend functionality or by the PAYONE
					// merchant interface. In the first case simply do
					// nothing, otherwise process the capturing.
                    $this->_paymentInst->processCaptureStatusRequest($this->_order, $data);
					break;
				case 'paid':
                    $this->_paymentInst->processPaidStatusRequest($this->_order, $data);
					break;
				case 'underpaid':
					// set order "ON HOLD"
                    $this->_paymentInst->processUnderpaidStatusRequest($this->_order, $data);
					break;
				case 'cancelation':
                    $this->_paymentInst->processCancelationStatusRequest($this->_order, $data);
					break;
				case 'debit':
                    $this->_paymentInst->processDebitStatusRequest($this->_order, $data);
					break;
				case 'refund':
				case 'reminder':
				case 'transfer':
				    break;
				default:
//					Mage::throwException($this->__('Unhandled Payone action "%s".', $data['txaction']));
					// answer with "TSOK" if we don't support the action.
					die('TSOK');
			}

			// set transaction ID
			$this->_order->getPayment()->setLastTransId($data['txid']);

			$this->_order->save();

			// send confirmation for status change
			die('TSOK');
		}
		catch (Exception $e) {
			$msg = $e->getMessage();

			if (!empty($data['txid']))
				$msg .= ' ('.$data['txid'].')';
            Mage::log(Mage::helper('payone')->__('PAYONE transaction status update failed: %s', $msg));

			// return "TSOK" if reference is empty and callback can't be performed
			if ($msg == 'Submitted data is empty.') {
				die('TSOK');
			}
		}

		$this->norouteAction();
		return;
	}

    /**
     * Load quote and order objects from session
     */
    protected function _loadCheckoutObjects()
    {
        	// load quote
        $this->_getCheckout()->setQuoteId($this->_getCheckout()->getPayoneQuoteId(true));

        	// load order
    	$this->_order = Mage::getModel('sales/order');
		$this->_order->loadByIncrementId($this->_getCheckout()->getPayoneLastRealOrderId(true));
	    if (!$this->_order->getId()) {
	    	Mage::throwException('Order ID not found.');
	    }
    }

	/**
	 * Checking POST variables and sets order ($order) and payment instance ($paymentInst).
	 *
	 * @return	array	Checked POST variables.
	 */
	protected function _getTransactionRequest()
	{
        //Mage::log('POST: ' . print_r($_POST, 1));
		if (!$this->getRequest()->isPost())
			Mage::throwException('Wrong request type.');

		// validate request ip coming from PAYONE subnet
		$helper = Mage::helper('core/http');
		if (method_exists($helper, 'getRemoteAddr')) {
			$remoteAddr = $helper->getRemoteAddr();
		} else {
			$request = $this->getRequest()->getServer();
			$remoteAddr = $request['REMOTE_ADDR'];
		}
		if (substr($remoteAddr,0,11) != '217.70.200.' && substr($remoteAddr,0,11) != '213.178.72.') {
			Mage::throwException('IP can\'t be validated as PAYONE-IP.');
		}

		$data = $this->getRequest()->getPost();

		if (empty($data) || empty($data['reference'])) {
			Mage::throwException('Submitted data is empty.');
		}

		// load order
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($data['reference']);
		if (!$order->getId())
			Mage::throwException('Order ID not found.');
		$this->_order = $order;

		// get payment instance
		$paymentInst = $order->getPayment()->getMethodInstance();
		if ($data['key'] != md5($paymentInst->getConfigData('security_key', $order->getStoreId()))) {
			Mage::throwException('Security key does not match.');
		}
		$this->_paymentInst = $paymentInst;

		return $data;
	}
    /**
     * Prepare params for CC API call
     */
    public function ccinitAction()
    {
        if (!$this->_getCheckout()->getQuote()->getIsActive()) {
            return;
        }
        $model = Mage::getModel('payone/cc');
        $params = $model->getCcInitFields();
        $this->getResponse()->setBody(Zend_Json::encode($params));
    }
    
    public function saveSessInfoAction()
    {
        $payment = $this->getRequest()->getPost();
        if (!empty($payment) && isset($payment['payment']) && !empty($payment['payment'])) {
            $payment = $payment['payment'];
            if ($payment['method']=='payone_cc' && !empty($payment['cc_owner']) && !empty($payment['cc_type']) 
                && !empty($payment['cc_number']) && !empty($payment['cc_exp_month']) && !empty($payment['cc_exp_year']) 
                && !empty($payment['cc_additional_data'])) 
            {
                Mage::getSingleton('core/session')->setPayoneCcInfo($payment);
            }
        }
            
        return;
    }

    protected function _getPendingPaymentStatus()
    {
        return Mage::helper('payone')->getPendingPaymentStatus();
    }

    public function ccformAction()
    {
       $this->loadLayout();
       $this->renderLayout();
    }
    
}