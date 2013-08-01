<?php
class Mage_Payment_Block_Info_Nganluong extends Mage_Payment_Block_Info
{

    protected $_payableTo;
    protected $_mailingAddress;
    protected $_return_url;
    protected $_checkout;
    protected $_merchantID;
    protected $_secure_code;
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/nganluong.phtml');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getPayableTo()
    {
        if (is_null($this->_payableTo)) {
            $this->_convertAdditionalData();
        }
        return $this->_payableTo;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getMailingAddress()
    {
        if (is_null($this->_mailingAddress)) {
            $this->_convertAdditionalData();
        }
        return $this->_mailingAddress;
    }
    public function getCheckout()
    {
    	 if (is_null($this->_checkout)) {
            $this->_convertAdditionalData();
        }
        return $this->_checkout;
    }
    public function getMerchantSite()
    {
    	if (is_null($this->_merchantID)) {
            $this->_convertAdditionalData();
        }
        return $this->_merchantID;
    }
    public function getSecureCode()
    {
    	if (is_null($this->_secure_code)) {
            $this->_convertAdditionalData();
        }
        return $this->_secure_code;
    }
     public function getReturnUrl()
    {
        if (is_null($this->_return_url)) {
            $this->_convertAdditionalData();
        }
        return $this->_return_url;
    }
    /**
     * Enter description here...
     *
     * @return Mage_Payment_Block_Info_Checkmo
     */
    protected function _convertAdditionalData()
    {
        $details = @unserialize($this->getInfo()->getAdditionalData());
        //if (is_array($details)) {
            $this->_payableTo = "Nho Game";isset($details['payable_to']) ? (string) $details['payable_to'] : '';
            $this->_mailingAddress = "nhogame.com@gmail.com"; //isset($details['mailing_address']) ? (string) $details['mailing_address'] : '';
            $this->_return_url = "http://padmadefleur.vn/"; //isset($details['return_url']) ? (string) $details['return_url'] : '';
            $this->_checkout = isset($details['checkout']) ? (string) $details['checkout'] : '';
            $this->_merchantID = isset($details['merchantID']) ? (string) $details['merchantID'] : '';
            $this->_secure_code = isset($details['secure_code']) ? (string) $details['secure_code'] : '';
        /*} else {
            $this->_payableTo = 'Nho Game';
            $this->_mailingAddress = 'nhogame.com@gmail.com';
            $this->_return_url='http://padmadefleur.vn/';
            $this->_checkout='';
            $this->_merchantID='';
            $this->_secure_code='';
        }*/
        return $this;
    }
    
    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/nganluong.phtml');
        return $this->toHtml();
    }

}
