<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Block_Adminhtml_Install extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_install';
    $this->_blockGroup = 'translate';
    $this->_headerText = Mage::helper('translate')->__('LingoTip');
    $this->_addButtonLabel = Mage::helper('translate')->__('Add Item');
    parent::__construct();
  }
}