<?php
class IG_CashOnDelivery_Model_Total extends Mage_Sales_Model_Quote_Address_Total_Shipping
{
	protected function getSession()
	{
		if (Mage::getDesign()->getArea() == 'adminhtml')
			return Mage::getSingleton('adminhtml/session_quote');
		
		return Mage::getSingleton('checkout/session');
	}
	
	protected function getQuote()
	{
		return $this->getSession()->getQuote();
	}
	
	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		if(version_compare(Mage::getVersion(), '1.4.0', '>='))
			return $this->collect14($address);
			
		return $this->collect13($address);
	}
	
	protected function collect13(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		
		$model = Mage::getModel('ig_cashondelivery/cashondelivery');
 		$amount = $address->getShippingAmount();

 		if (
  			(($amount != 0) || $address->getShippingDescription()) &&
 			($this->getQuote()->getPayment()->getMethod() == $model->getCode()) &&
 			($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
 		) {
			$fee		= $model->getExtraFee();
 			$fee_excl	= Mage::helper('tax')->getShippingPrice($fee, false);
 			$fee_incl	= Mage::helper('tax')->getShippingPrice($fee, true);
 			
 			if (Mage::helper('tax')->shippingPriceIncludesTax())
 			{
				$address->setShippingAmount($address->getShippingAmount()+$address->getShippingTaxAmount()+$fee_incl);
	 			$address->setBaseShippingAmount($address->getBaseShippingAmount()+$address->getBaseShippingTaxAmount()+$fee_incl);
 			}
 			else
 			{
 				$address->setShippingAmount($address->getShippingAmount()+$fee_excl);
 				$address->setBaseShippingAmount($address->getBaseShippingAmount()+$fee_excl);
 			}
 					
 			$address->setGrandTotal($address->getGrandTotal()+$fee_excl);
 			$address->setBaseGrandTotal($address->getBaseGrandTotal()+$fee_excl);
			
			$address->setShippingDescription($address->getShippingDescription().' + '.$model->getTitle());
		}
		
		return $this;
	}
	
	protected function collect14(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		
		$model = Mage::getModel('ig_cashondelivery/cashondelivery');
 		$amount = $address->getShippingAmount();

 		if (
  			(($amount != 0) || $address->getShippingDescription()) &&
 			($this->getQuote()->getPayment()->getMethod() == $model->getCode()) &&
 			($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
 		) {
			$fee		= $model->getExtraFee();
 			$fee_excl	= Mage::helper('tax')->getShippingPrice($fee, false);
 			
			$address->setShippingAmount($address->getShippingAmount()+$fee_excl);
			$address->setBaseShippingAmount($address->getBaseShippingAmount()+$fee_excl);
 						
			$address->setShippingDescription($address->getShippingDescription().' + '.$model->getTitle());
		}
		
		return $this;
	}
}
