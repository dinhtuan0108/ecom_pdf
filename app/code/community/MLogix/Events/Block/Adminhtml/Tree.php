<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php
class MLogix_Events_Block_Adminhtml_Tree extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{  	
		$this->_controller = 'adminhtml_events';
		$this->_blockGroup = 'events';
		$this->_headerText = Mage::helper('events')->__('Categories');
		
		parent::__construct();
	}
  
  
  
	protected function _prepareLayout()
	{
	    $addUrl = $this->getUrl("*/*/new", array(
	        '_current'=>true,
	        'id'=>null,
	        '_query' => false
	    ));
	    	
	    $this->setChild('add_sub_button',
	        $this->getLayout()->createBlock('adminhtml/widget_button')
	            ->setData(array(
	                'label'     => Mage::helper('events')->__('Add Item'),
	                'onclick'   => "addNew('".$addUrl."', false)",
	                'class'     => 'add'
	            ))
	    );
	
	    $this->setChild('add_root_button',
	        $this->getLayout()->createBlock('adminhtml/widget_button')
	            ->setData(array(
	                'label'     => Mage::helper('events')->__('Add Root Category'),
	                'onclick'   => "addNew('".$addUrl."', true)",
	                'class'     => 'add',
	                'id'        => 'add_root_category_button'
	            ))
	    );
	
	    return parent::_prepareLayout();
	}

	
	public function getResetUrl()
	{
		return $this->getUrl("*/*/reset");
	}
	
	public function getEditUrl()
	{
		return $this->getUrl("*/*/edit");
	}
	
	public function getMoveUrl()
	{
		return $this->getUrl("*/*/move"); // todo
	}
	
	public function getStoreId()
	{
		return 0; // not important
	}
	
	public function getLoadTreeUrl($expanded=null)
	{
		return $this->getUrl("*/*/categoriesJson");		
	}
	
	public function getCategoryId()
	{
		return 1;
	}
	
	public function getRootName()
	{
		//$x = Mage::getModel('events/events')->load(1);
		//$x = $x->toArray();

		return 'Events Categories';
	}
	
	public function getIsWasExpanded()
	{
		return true;
	}
	
	public function getSwitchTreeUrl()
	{
		return $this->getUrl("*/*/categoriesJson"); // todo
	}
	
	
	
	public function getTreeJson($node = 0)
	{
		$cats = Mage::getModel('events/events')->getCategories($node);
		
		
		
		$json = Zend_Json::encode($cats);
		//echo $json;
		//die();

        return $json;	
	}
    
    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }    	
      
}