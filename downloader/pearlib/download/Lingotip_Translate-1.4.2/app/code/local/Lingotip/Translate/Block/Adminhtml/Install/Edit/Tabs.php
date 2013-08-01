<?php

class Lingotip_Translate_Block_Adminhtml_Install_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct(); 
      $this->setId('install_tabs');
      $this->setDestElementId('edit_form');
      $css = Mage::helper('translate')->getLingoTipLeftIcon();
      $this->setTitle($css);
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('translate')->__('Register'),
          'title'     => Mage::helper('translate')->__('Register'),
          'content'   => $this->getLayout()->createBlock('translate/adminhtml_install_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}