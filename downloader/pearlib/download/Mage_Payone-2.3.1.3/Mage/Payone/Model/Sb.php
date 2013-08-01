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
class Mage_Payone_Model_Sb extends Mage_Payone_Model_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    **/
    protected $_code            = 'payone_sb';
    protected $_formBlockType   = 'payone/form';
    protected $_infoBlockType   = 'payone/info';
    protected $_paymentMethod   = 'sb';

    /**
     *  Owerride constructor to choose form block.
     */
    public function __construct()
    {
        parent::_construct();
        if ($this->canAuthorize()) {
            $this->_formBlockType = 'payone/formSb';
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

        $info->setCcLast4(substr($data->getAccountNumber(), -4))
             ->setCcType($data->getOnlineBankTransferType())
             ->setCcOwner($data->getBankGroup())
             ->setCcNumberEnc($info->encrypt($data->getAccountNumber()))
             ->setPoNumber($data->getBankCode());
        return $this;
    }
    /**
     * prepare Auth request params array
     * @param Mage_Sales_Model_Order $order
     * @return array
     */

    protected function _getAuthorizeParams(Mage_Sales_Model_Order $order)
    {
        $params = parent::_getAuthorizeParams($order);
        if (!$this->canAuthorize()) {
            return $params;
        }
        $info = $this->getInfoInstance();
        $onlineBankTransferType = $info->getCcType();
        $bankGroup = $info->getCcOwner();
        $accountNumber = $info->decrypt($info->getCcNumberEnc());
        $bankCode = $info->getPoNumber();
        $params['onlinebanktransfertype'] = $onlineBankTransferType;
        if ($params['country'] == 'AT' || $params['country'] == 'DE') {
            $params['bankaccount'] = $accountNumber;
            $params['bankcode'] = $bankCode;
        }
        if ($params['country'] == 'AT' && $onlineBankTransferType == 'EPS') {
            $params['bankgrouptype'] = $bankGroup;
        }
        $params['bankcountry'] = $params['country'];
        return $params;
    }
    /**
     * Check if tehre is allowed transfer types for current country
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        if (!$this->canAuthorize()) {
            return parent::canUseForCountry($country);
        }

        if (!parent::canUseForCountry($country)) {
            return false;
        }

        $allowedTypes = explode(',', $this->getConfigData('allowed_transfer_type'));
        if (count($allowedTypes) == 0 || strlen($allowedTypes[0]) == 0) {
            // Return false if there is no selected types in config
            return false;
        }

        foreach ($allowedTypes as $type) {
            list($typeCode, $typeCountry) =  explode('-', $type);
            if ($typeCountry == $country) {
                return true;
            }
        }
        return false;
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
    }

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
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $this->getConfigData('order_status'), $message, TRUE);
        $order->save();
    }
}
