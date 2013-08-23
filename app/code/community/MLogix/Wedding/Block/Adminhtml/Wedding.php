<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php
class MLogix_Wedding_Block_Adminhtml_Wedding extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_wedding';
    $this->_blockGroup = 'wedding';
    $this->_headerText = Mage::helper('wedding')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('wedding')->__('Add Item');
    parent::__construct();
  }
}