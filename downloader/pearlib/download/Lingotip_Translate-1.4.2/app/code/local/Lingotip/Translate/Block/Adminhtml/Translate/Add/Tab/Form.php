<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Add_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
	 
      $fieldset = $form->addFieldset('translate_form', array('legend'=>Mage::helper('translate')->__('Estimate Translation Fee')));
     
	  $itemId = (int) $this->getRequest()->getParam('id');
		$this->setTemplate('lingotip/addnew.phtml');
		
  	 $model = Mage::getModel('translate/translate');

	 if ( Mage::getSingleton('adminhtml/session')->getTranslateData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTranslateData());
          Mage::getSingleton('adminhtml/session')->setTranslateData(null);
      } elseif ( Mage::registry('translate_data') ) {
          $form->setValues(Mage::registry('translate_data')->getData());
      }
      return parent::_prepareForm();
  }
  

}