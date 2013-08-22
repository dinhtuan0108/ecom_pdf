<?php

class MLogix_Events_Block_Adminhtml_Events_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('events_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('events')->__('Item Information'));
      $this->setTemplate('widget/tabshoriz.phtml');
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('events')->__('Item Information'),
          'title'     => Mage::helper('events')->__('Item Information'),          
          'content'   => $this->getLayout()->createBlock('events/adminhtml_events_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}