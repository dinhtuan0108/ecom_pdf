<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected function _getNodeJson($node, $level = 0)
    {
    	$item = parent::_getNodeJson($node, $level);
/*
    	$category = Mage::getModel('catalog/category');
    	$category->setStoreId($item['store']);
    	$category->load($node->getId());
    	$category->getUrlInstance()->setStore($item['store']);
    	$clickurl = $category->getUrl();
    	if ( $clickurl !=  $category->getCategoryIdUrl() )
    	{
    		$cmsurl = str_replace($category->getUrlInstance()->getDirectUrl(''),'',$clickurl);
    		$cmsurl = "{{store direct_url='$cmsurl'}}";
			$item['clickurl']= $clickurl;
			$item['cmsurl']= $clickurl.Mage::helper('auit_editor')->maskAttribute($cmsurl);
			
			$item['draghtml']= '<a href="'.$item['cmsurl'].'" title="'.$category->getMetaTitle().'">'.$category->getName().'</a>';
			//$item['qtip']= $category->getMetaTitle();
    	}
*/    	
		return $item;    	
    }
}
