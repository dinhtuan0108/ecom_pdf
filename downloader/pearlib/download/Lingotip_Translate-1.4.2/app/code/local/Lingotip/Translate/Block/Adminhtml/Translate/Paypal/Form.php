<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Paypal_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
       $form = new Varien_Data_Form(array(
                                      'id' => 'paypal_form',
                                     // 'action' => $this->getUrl('*/*/savePaypal', array('id' => $this->getRequest()->getParam('id'))), 
									 'action' => Lingotip_Translate_Helper_Data::paypalUrl,
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
	  
	  $paramId = $this->getRequest()->getParam('id');
		  
	  $form_key = $this->getRequest()->getParam('form_key'); 
	  if(!isset($form_key) && $form_key == ""){$form_key = Mage::getSingleton('core/session')->getFormKey(); }
	  $returnUrl = $this->getUrl('*/*/savePaypal/form_key/'.$form_key.'/requestId/'.$paramId);
	  $cancelUrl = $this->getUrl('*/*/index');
	  
	  $directPaypal = $this->getRequest()->getParam('directPaypal');
	 
	  if(isset($directPaypal) && $directPaypal != "" && $directPaypal == "yes")
	  {
	  	Mage::helper('translate')->submit_paypal_post($paramId,$returnUrl,$cancelUrl,$paramId);
	  }
	  
	  $fields = Mage::helper('translate')->paypalHiddenFields($paramId,$returnUrl,$cancelUrl);
	  foreach($fields as $field => $value){
		$form->addField($field, 'hidden', array(
			  'name'      => $field,
			  'value'	  => $value,
		  ));
	  }
 
	  
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}