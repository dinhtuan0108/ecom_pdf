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
class Mage_Payone_Model_Vor extends Mage_Payone_Model_Abstract
{
	/**
	* unique internal payment method identifier
	*
	* @var string [a-z0-9_]
	**/
	protected $_code			= 'payone_vor';
	protected $_formBlockType	= 'payone/form';
	protected $_infoBlockType	= 'payone/info';
	protected $_paymentMethod	= 'vor';

    /**
     *  Owerride constructor to choose form block.
     */
	public function __construct()
	{
		parent::_construct();
        if ($this->canAuthorize()) {
            $this->_infoBlockType	= 'payone/infoVorApi';

        }
	}

	/**
	 * prepare Auth request params array
	 * @param Mage_Sales_Model_Order $order
     * @return array
     */

    protected function _getAuthorizeParams(Mage_Sales_Model_Order $order)
	{
		$params = parent::_getAuthorizeParams($order);
        if (!$this->canAuthorize()){
            $params['autosubmit'] = 'yes';
        }
		return $params;
	}

    /**
     * Build message to save in order history
     * @param array $response
     * @return string
     */
    protected function _getStatusHistoryMessage($response)
    {
        $message  = $this->_getHelper()->__('Payee: %s', $response['clearing_bankaccountholder']) . '<br/>';
        $message .= $this->_getHelper()->__('Account number: %s', $response['clearing_bankaccount']) . '<br/>';
        $message .= $this->_getHelper()->__('Bank sort code: %s', $response['clearing_bankcode']) . '<br/>';
        $message .= $this->_getHelper()->__('IBAN: %s', $response['clearing_bankiban']) . '<br/>';
        $message .= $this->_getHelper()->__('BIC: %s', $response['clearing_bankbic']) . '<br/>';
        $message .= $this->_getHelper()->__('Bank: %s', $response['clearing_bankname']) . '<br/>';
        $message .= $this->_getHelper()->__('Payment reference: %s', $response['txid']) . '<br/>';

        return $message;
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

    protected function _processAuthorizeResponse($payment, $response, $stateObject)
    {
        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus(Mage::helper('payone')->getPendingPaymentStatus());
        $stateObject->setIsNotified(false);
        $payment->getOrder()->setCustomerNote($this->_getStatusHistoryMessage($response));
    }


    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processAppointedStatusRequest($order, $data)
    {
        // set state STATE_PENDING_PAYMENT, waiting for payment (PAID status).
        $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage::helper('payone')->getPendingPaymentStatus(), Mage::helper('payone')->__('The payment has been appointed by PAYONE.'))->save();
        if ($this->getConfigData('payment_action') != Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE) {
            // Send order confirmation email for non API mode
            $order->sendNewOrderEmail();
            $order->setEmailSent(true);
        }
    }

    /**
     * Is called from controller to process status action
     * @param <type> $order
     * @param <type> $data
     */
    public function processPaidStatusRequest($order, $data)
    {
        $message   = Mage::helper('payone')->__('The PAYONE transaction has been marked as paid.') . '<br/>';
        $message  .= Mage::helper('payone')->__('Balance: %s', $data['balance']) . '<br/>';
        $message  .= Mage::helper('payone')->__('Receivable: %s', $data['receivable']) . '<br/>';
        if (($data['receivable'] - $data['balance']) < round($order->getGrandTotal(), 2)) {
            $message  .= Mage::helper('payone')->__('Payment is less than order total.');
            $order->addStatusToHistory($order->getStatus(), $message);
            return;
        }
        $message  .= Mage::helper('payone')->__('Order has been paid.');
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $this->getConfigData('order_status', $order->getStoreId()), $message, true);
        $order->save();

        if ($this->getConfigData('request_type', $order->getStoreId()) != Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE) {
            // only create invoice if payment has been authorization
            if ($order->canInvoice()) {
                $invoice = $order->prepareInvoice();
                $invoice->register()->capture();
                Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();
            }
        }
    }
}