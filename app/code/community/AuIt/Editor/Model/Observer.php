<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2008 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Model_Observer
{
    public function blocksAfter(Varien_Event_Observer $observer)
    {
    	if ( Mage::getDesign()->getArea() == 'adminhtml' )
    		return;
	    
   		$layout = $observer->getEvent()->getLayout();
    	$HeadBlock = $layout->getBlock('head');
		if ( $HeadBlock && $HeadBlock instanceof  Mage_Page_Block_Html_Head)
		{
			$HeadBlock->addItem('js_css','auit/editor/css/editor.css');
		}
		Mage::helper('auit_editor/blocks')->buildBlocks($layout);
		Mage::helper('auit_editor/blocks')->buildMenus($layout);
    	if (Mage::helper('auit_editor')->getIsInInlineMode() )
    	{
    		$blocks = $layout->getAllBlocks();
    		foreach ($blocks as $block )
    		{
    			if ( $block instanceof Mage_Core_Block_Abstract )
    			{
					$block->setData('cache_lifetime',null);
    			}
    		} 
    		$action = $observer->getEvent()->getAction();
    		/*
			*/
			Mage::helper('auit_editor/content')->addPageData($layout,$action);
    	}
		$Block = $layout->getBlock('before_body_end');
		if ( $Block && $Block instanceof  Mage_Core_Block_Text_List)
		{
			$Block->insert($layout->createBlock('auit_editor/page'));
		}
/*    	
    	$Block = $layout->getBlock('catalog.topnav');
		if (  0 && $Block )
		{
			//Varien_Data_Tree_Node_Collection
			//Varien_Data_Tree_Node
			$categories = $Block->getStoreCategories();
			$data = array();
			$data['treeid']=uniqid();
			$data['is_active']=true;
			$data['children']=array();
			$data['name']='AAAA';
			$data['request_path']='xxxxxxxxxx';
			
			
			//Mage_Catalog_Model_Category
			
			
			//ChildrenNodes( flat?
			$parent     = Mage::app()->getStore()->getRootCategoryId();
		//	$categories->appendChild($data=array(), $parentNode, $prevNode=null);
			//$categories->addItem(Varien_Object $item)
			
			$node = new Varien_Data_Tree_Node($data, 'treeid', new Varien_Data_Tree);
			$data['name']='A1';
			$node2 = new Varien_Data_Tree_Node($data, 'treeid', new Varien_Data_Tree);
			$node->addChild($node2);
			$categories->add($node);
			
			$nodes = $categories->getNodes();
			array_shift($nodes);//,$node);
            foreach ($nodes as $k => $_node)
            {
            	
            	
            }
            foreach ($categories as $_category)
            {
		//	echo "<br/>".	$Block->drawItem($_category);
			
            	//echo $this->drawItem($_category) 
            }
		}
*/    	
    }
    
	public function loadProductOptions(Varien_Event_Observer $observer)
    {
    	
    	$object = $observer->getEvent()->getCollection();
		if ( $object instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection )
		{
    		//$object->addAttributeToSelect(array('html_allowed_on_front', 'wysiwyg_enabled'));
		}
    }
    public function modelProductLoad(Varien_Event_Observer $observer)
    {
    	$object = $observer->getEvent()->getProduct();
		if ( $object instanceof Mage_Catalog_Model_Product )
		{
			Mage::helper('auit_editor')->checkProductAttribute($object);
		}
    }
	public function modelCategoryLoad(Varien_Event_Observer $observer)
    {
    	$object = $observer->getEvent()->getCategory();
		if ( $object instanceof Mage_Catalog_Model_Category )
		{
			Mage::helper('auit_editor')->checkCategoryAttribute($object);
		}
    }
	public function modelCmsBlockLoad(Varien_Event_Observer $observer)
    {
    	$object = $observer->getEvent()->getObject();
		if ( $object instanceof Mage_Cms_Model_Block &&
			Mage::getDesign()->getArea() != 'adminhtml' &&			
			Mage::helper('auit_editor')->getIsInInlineMode() )
		{
			Mage::helper('auit_editor/content')->addCMSBlockContent($object);
		}
    }
    

    
	public function modelLoad(Varien_Event_Observer $observer)
    {
    	if ( Mage::getDesign()->getArea() == 'adminhtml' )
    		return;

			
    	$object = $observer->getEvent()->getObject();
    	if ( $object instanceof Mage_Cms_Model_Block )
		{
			if ( Mage::helper('auit_editor')->getIsInInlineMode() )
			{
				Mage::helper('auit_editor/content')->addCMSBlockContent($object);
			}
		}
		/**
    	$object = $observer->getEvent()->getObject();
		if ( $object instanceof Mage_Cms_Model_Block &&
			Mage::getDesign()->getArea() != 'adminhtml' &&			Mage::helper('auit_editor')->getIsInInlineMode() )
		{
			Mage::helper('auit_editor/content')->addCMSBlockContent($object);
		}
		else if ( $object instanceof Mage_Catalog_Model_Product )
			Mage::helper('auit_editor')->checkProductAttribute($observer->getEvent()->getObject());
		else if ( $object instanceof Mage_Catalog_Model_Category )
			Mage::helper('auit_editor')->checkCategoryAttribute($observer->getEvent()->getObject());
			*/
    }
    public function assignHandlers($observer)
    {
        $Helper = $observer->getEvent()->getHelper();
        $MyHelper = Mage::helper('auit_editor');
        $Helper->addHandler('productAttribute', $MyHelper)
            ->addHandler('categoryAttribute', $MyHelper);
        return $this;
    }
    public function modelProductSaveBefore(Varien_Event_Observer $observer)
    {
    	$product = $observer->getEvent()->getProduct();
		if ( $product instanceof Mage_Catalog_Model_Product 
			&& $product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
			&& Mage::getVersion() >= 1.4 )
			{
				if ( $product->getCanSaveCustomOptions() && !$product->getTypeHasOptions())
				{
					$links = $product->getTypeInstance(true)->getLinks($product);
					if ( count($links) > 0 )
					{
	        			$product->setTypeHasOptions(true);
	        			$product->setLinksExist(true);
			            $product->setHasOptions(true);
					}        			
				}
			}
    }
}
