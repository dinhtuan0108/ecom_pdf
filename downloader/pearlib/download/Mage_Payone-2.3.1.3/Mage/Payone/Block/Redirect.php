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
class Mage_Payone_Block_Redirect extends Mage_Core_Block_Template
{
    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return order instance with loaded onformation by increment id
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        } else if ($this->getCheckout()->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        } else {
            return null;
        }
        return $order;
    }

    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        return $this->_getOrder()->getPayment()->getMethodInstance();
    }

    /**
     * Get form data
     *
     * @return array
     */
    public function getFormData()
    {
        if($this->getMethod()->canAuthorize() && ($this->getMethod()->getCode() == 'payone_sb' ||
           $this->getMethod()->getCode() == 'payone_wlt' || $this->getMethod()->getCode() == 'payone_cc')
          ) {
            return array();
        }
        return $this->_getOrder()->getPayment()->getMethodInstance()->getFormFields();
    }

    /**
     * Get request method. TRUE if POST
     *
     * @return boolean
     */
    public function sendPost()
    {
        if($this->getMethod()->canAuthorize() && ($this->getMethod()->getCode() == 'payone_cc')) {
            return false;
        }
        return true;
    }

    /**
     * Getting gateway url
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->_getOrder()->getPayment()->getMethodInstance()->getUrl();
    }
}