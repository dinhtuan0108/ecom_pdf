<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Paypal_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
  
      parent::__construct();
      $this->setId('translate_tabs');
      $this->setDestElementId('paypal_form');
      $css = Mage::helper('translate')->getLingoTipLeftIcon();
      $this->setTitle($css);
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('translate')->__('Pay For Translation'),
          'title'     => Mage::helper('translate')->__('Pay For Translation'),
          'content'   => $this->getLayout()->createBlock('translate/adminhtml_translate_paypal_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}