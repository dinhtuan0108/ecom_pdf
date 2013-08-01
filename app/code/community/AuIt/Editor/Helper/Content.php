<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2008 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Helper_Content extends Mage_Core_Helper_Abstract
{
	protected $_pageData=array();
	
	public function getPageData()
	{
		return $this->_pageData;
	}
	public function addPageData(Mage_Core_Model_Layout $layout,Mage_Core_Controller_Varien_Action $action)
	{
		if ( $action instanceof Mage_Catalog_ProductController)
		{
			$product = Mage::registry('current_product');
			if ( !$product )
				$product = Mage::registry('product');
			if ( $product ) 
				$this->addProductData($product);
		}
		else if ( $action instanceof Mage_Catalog_CategoryController)
		{
			$category = Mage::registry('current_category');
			if ( $category ) 
				$this->addCategoryData($category);
		}
		else if ( 	$action instanceof Mage_Cms_IndexController ||
					$action instanceof Mage_Cms_PageController ||
					$action instanceof AuIt_Editor_Admin_CmsController)
		{
			$page = Mage::getSingleton('cms/page');
			if ( $page && $page->getId() )
				$this->addCMSPageData($page);
		}
		else 
		{
			if ( class_exists ( 'AW_Blog_PostController' ,false ) )
			{
				if ( $action instanceof AW_Blog_PostController )
				{
					$this->addAWBlogData($action);
				}
			}
		}
		
		/**
		foreach ( $layout->getAllBlocks() as $name => $block )
		{
			if ( $block instanceof Mage_Cms_Block_Block)
			{
				//$this->addCMSBlockData($Block);
			}
			else if ( $block instanceof Mage_Cms_Block_Page)
			{
				$this->addCMSPageData($block);
			}
			else if ( $block instanceof Mage_Catalog_Block_Category_View)
			{
				$this->addCategoryData($block);
			}
			else if ( $block instanceof Mage_Catalog_Block_Product_View)
			{
				$this->addProductData($block);
			}
		}
		*/
	}
	public function translateLocaltoHTML(&$rData,$data)
	{
		$rData['data']=array();
		$html = Mage::helper('auit_editor')->prefilter($data['local']);
   	    $html = Mage::helper('auit_editor')->translateDirective($html);
		$rData['data']=array('xhtml'=>$html);
	}
	public function translateBlocktoHTML(&$rData,$data)
	{
		$rData['data']=array();
		$html = Mage::helper('auit_editor')->prefilter($data);
   	    $html = Mage::helper('auit_editor')->translateDirective($html);
		$rData['data']=array('xhtml'=>$html);
	}
	
	public function addCMSPageData(Mage_Cms_Model_Page $page)
	{
		$data = $page->getData();
		$contentId= 'AUIT_'.uniqid();
		$content = Mage::helper('auit_editor')->prefilter($data['content']);
		$page->setContent('<div id="'.$contentId.'" class="auit-edit-block clearfix" >'.$content.'</div>');
		$data['content']='';
		if ( isset($data['store_id']) && is_array($data['store_id']) )
		{
			$data['store_id']=implode (',',$data['store_id']);
		}
		foreach ( $data as $n => $v )
		{
			if ( is_object($v)|| is_array($v) )
			{
				unset($data[$n]);
			}
			else if ( is_null ($v) )
			{
				$data[$n]='';
			} 
		}
		$this->_pageData['MAIN']=array(
			'factory'=>'CMSPage',
			'window_id'=>'CMSPage_'.$page->getId(),
			'page_id'=>$page->getId(),
			'blockIds'=>array($contentId=>array('name'=>'content')),
		    'storeId'=>Mage::app()->getStore()->getId(),
			'storeName'=>Mage::app()->getStore()->getName(),
			'desc'=>Mage::getSingleton('core/layout')->createBlock('auit_editor/forms_cms_page')->forms(),
			'data'=>array(
					'data'=>$data,
					'modifiedData'=>new stdClass(),
					'use_store'=>new stdClass()
					)
		
		);
	}
    public function addInlineBlock($marker,$directive)
    {
		$contentId= 'AUIT_'.uniqid();
    	$block = '<DIV id="'.$contentId.'" class="auit-edit-block auit-edit-block-inline"  >';
		$block .= '<DIV style="display:none" class="auit-edit-inlineblock-description">';
		
		$block .= '<DIV class="source">';
		$block .=  base64_encode($marker);//str_replace(array('{{','}}'),array('',''),$marker);
		$block .= '</DIV>';
		$block .= '<DIV class="desc">';
		$block .= base64_encode(Zend_Json::encode(Mage::getSingleton('core/layout')->createBlock('auit_editor/forms_cms_inlineblock')->forms()));
		$block .= '</DIV>';
		
		$block .= '</DIV>';
		$block .= $marker;
        $block .= '</DIV>';

        /*
        $data = array();
        $data['source']=$marker;
        
		if ( !isset($this->_pageData['SUB']) ) 
			$this->_pageData['SUB']=array();
		$this->_pageData['SUB'][]=array(
			'factory'=>'InlineBlock',
			'storeId'=>Mage::app()->getStore()->getId(),
			'storeName'=>Mage::app()->getStore()->getName(),
			'type'=>$directive,
			'page_id'=>1,
			'blockIds'=>array($contentId=>array('name'=>'content')),
			'desc'=>Mage::getSingleton('core/layout')->createBlock('auit_editor/forms_cms_inlineblock')->forms(),
			'data'=>array(
					'data'=>$data,
					'modifiedData'=>new stdClass(),
					'use_store'=>new stdClass()
					)
		
		);
        */
        return $block;
    }
	
	public function addCMSBlockContent(Mage_Cms_Model_Block $block,$breturnValue=false)
	{
		$content = Mage::helper('auit_editor')->prefilter($block->getContent());
		$data = $block->getData();
		$data['content']='';
		if ( isset($data['store_id']) && is_array($data['store_id']) )
		{
			$data['store_id']=implode (',',$data['store_id']);
		}
		foreach ( $data as $n => $v ){
			if ( is_object($v)|| is_array($v) )	{
				unset($data[$n]);
			}
			else if ( is_null ($v) ){
				$data[$n]='';
			} 
		}
		
		$contentId= 'AUIT_'.uniqid();
		$pData = array(
			'factory'=>'CMSBlock',
			'storeId'=>Mage::app()->getStore()->getId(),
			'storeName'=>Mage::app()->getStore()->getName(),
			'page_id'=>$block->getId(),
			'blockIds'=>array($contentId=>array('name'=>'content')),
			'desc'=>Mage::getSingleton('core/layout')->createBlock('auit_editor/forms_cms_block')->forms(),
			'data'=>array(
					'data'=>$data,
					'modifiedData'=>new stdClass(),
					'use_store'=>new stdClass()
					)
		);		
		if ( $breturnValue )
			return $pData;
			
		if ( !isset($this->_pageData['SUB']) ) 
			$this->_pageData['SUB']=array();
		$this->_pageData['SUB'][]=$pData;
		$result ='<div id="'.$contentId.'" class="auit-edit-block clearfix" >'.$content.'</div>';
		$block->setContent($result);
	}
	
	public function addCategoryData(Mage_Catalog_Model_Category $category)
	{
		$data = $category->getData();
		foreach ( $data as $n => $v )
			if ( is_object($v)|| is_array($v) )
				unset($data[$n]); 
		$desc = Mage::getSingleton('core/layout')->createBlock('auit_editor/forms_category_page')->setCategory($category)->forms();
		$contentIds= array();
		if ( 1 )
		foreach ($desc['htmlfields'] as $name => $v)
		{
			$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', $name);
			if ( $attribute )
			{
				$attribute->setData('is_html_allowed_on_front',1);
				$attribute->setData('is_wysiwyg_enabled',1);//1.4
				$contentId= 'AUIT_'.uniqid();
				 
				$content = Mage::helper('auit_editor')->prefilter($category->getData($name));
				$category->setData($name,'<div id="'.$contentId.'" class="auit-edit-block clearfix" >'.$content.'</div>');
			
				$data[$name]='';
				$dv = $category->getAttributeDefaultValue($name);
				$v['use_store']=!( is_null($dv)|| $dv===false);
				$contentIds[$contentId]=$v;
			}
		}
		
		$useStore=array();
		foreach ( $desc['forms'] as $form )
		{
			foreach ( $form['fields'] as $item )
			{
				$attcode = $item['name'];
				$dv = $category->getAttributeDefaultValue($attcode);
				$useStore[$attcode]=!( is_null($dv)|| $dv===false);
			}
		}
		$this->_pageData['MAIN']=array(
			'factory'=>'CategoryPage',
			'window_id'=>'CategoryPage_'.$category->getId(),
		    'storeId'=>Mage::app()->getStore()->getId(),
			'storeName'=>Mage::app()->getStore()->getName(),
			'page_id'=>$category->getId(),
			'blockIds'=>$contentIds,
			'desc'=>$desc,
			'data'=>array(
					'data'=>$data,
					'modifiedData'=>new stdClass(),
					'use_store'=>$useStore
					)
		);
	}
	
	public function addProductData(Mage_Catalog_Model_Product $product)
	{
		$data = $product->getData();
		foreach ( $data as $n => $v )
			if ( is_object($v)|| is_array($v) )
				unset($data[$n]); 
		$desc = Mage::getSingleton('core/layout')->createBlock('auit_editor/forms_product_page')->setProduct($product)->forms();
		$contentIds= array();

		$attributes = $product->getAttributes();
		if (1 )
		foreach ($desc['htmlfields'] as $name => $v)
		{
        	$attribute  = (isset($attributes[$name])) ? $attributes[$name] : null;
        	if ($attribute )
        	{
				$attribute->setData('is_html_allowed_on_front',1);
				$attribute->setData('is_wysiwyg_enabled',1);//1.4
        		$attribute->setIsHtmlAllowedOnFront(1);
				$contentId= 'AUIT_'.uniqid();
				$content = Mage::helper('auit_editor')->prefilter($product->getData($name));
				$product->setData($name,'<div id="'.$contentId.'" class="auit-edit-block clearfix" >'.$content.'</div>');
				$data[$name]='';
				$dv = $product->getAttributeDefaultValue($name);
				$v['use_store']=!( is_null($dv)|| $dv===false);
				$contentIds[$contentId]=$v;
        	}
		}
		$useStore=array();
		foreach ( $desc['forms'] as $form )
		{
			foreach ( $form['fields'] as $item )
			{
				$attcode = $item['name'];
				$dv = $product->getAttributeDefaultValue($attcode);
				$useStore[$attcode]=!( is_null($dv)|| $dv===false);
			}
		}
		$this->_pageData['MAIN']=array(
			'factory'=>'ProductPage',
			'window_id'=>'ProductPage_'.$product->getId(),
		    'storeId'=>Mage::app()->getStore()->getId(),
			'storeName'=>Mage::app()->getStore()->getName(),
			'page_id'=>$product->getId(),
			'blockIds'=>$contentIds,
			'desc'=>$desc,
			'data'=>array(
					'data'=>$data,
					'modifiedData'=>new stdClass(),
					'use_store'=>$useStore
					)
		);
	}
	public function addAWBlogData(Mage_Core_Controller_Varien_Action $action)
	{
		$identifier = $action->getRequest()->getParam('identifier', $action->getRequest()->getParam('id', false));
		if ( !$identifier )
			return false;
		$page = Mage::getSingleton('blog/post');
		if (!is_null($identifier) && $identifier!==$page->getId()) {
			$page->setStoreId(Mage::app()->getStore()->getId());
			if (!$page->load($identifier)) {
				return false;
			}
		}
		$data = $page->getData();
		$desc = Mage::getSingleton('core/layout')->createBlock('auit_editor/forms_blog_page')->setBlog($page)->forms();
		$contentIds= array();
		foreach ($desc['htmlfields'] as $name)
		{
			$contentId= 'AUIT_'.uniqid();
			$content = Mage::helper('auit_editor')->prefilter($data[$name]);
			$page->setData($name,'<div id="'.$contentId.'" class="auit-edit-block clearfix" >'.$content.'</div>');
			$data[$name]='';
			$contentIds[$contentId]=array('name' => $name);
		}
		if ( isset($data['store_id']) && is_array($data['store_id']) )
		{
			$data['store_id']=implode (',',$data['store_id']);
		}
		if ( isset($data['cat_id']) && is_array($data['cat_id']) )
		{
			$data['cat_id']=implode (',',$data['cat_id']);
		}
		foreach ( $data as $n => $v )
		{
			if ( is_object($v)|| is_array($v) )
			{
				unset($data[$n]);
			}
			else if ( is_null ($v) )
			{
				$data[$n]='';
			} 
		}
		$this->_pageData['MAIN']=array(
			'factory'=>'Blog','p'=>1,
			'window_id'=>'Blog_'.$page->getId(),
			'page_id'=>$page->getId(),
			'blockIds'=>$contentIds,
		    'storeId'=>Mage::app()->getStore()->getId(),
			'storeName'=>Mage::app()->getStore()->getName(),
			'desc'=>$desc,
			'data'=>array(
					'data'=>$data,
					'modifiedData'=>new stdClass(),
					'use_store'=>new stdClass()
					)
		
		);
	}	
}