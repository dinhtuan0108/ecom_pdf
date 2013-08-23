<?php

class MLogix_Wedding_Block_Adminhtml_Wedding_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('wedding_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('wedding')->__('Item Information'));
      $this->setTemplate('widget/tabshoriz.phtml');
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('wedding')->__('Item Information'),
          'title'     => Mage::helper('wedding')->__('Item Information'),          
          'content'   => $this->getLayout()->createBlock('wedding/adminhtml_wedding_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}