<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php
class MLogix_Wedding_Block_Wedding extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getWedding($parent=0)     
    { 
        $model = $this->getCurrentWedding();
        if(!$model) return array();
        
        if($parent)
        	return $model->getWedding($parent);
        else
        	return $model->getWedding($model->getId());
    }
    
    public function getImageUrl($itemId)
    {
    	$model = Mage::getModel('wedding/wedding')->load($itemId);
    	return $model->getImageUrl();
    }
    
    public function getViewUrl($itemId)
    {
    	return $this->getUrl("*/*/view").'id/'.$itemId;
    }
    
    public function getCurrentWedding()
    {
    	if(!Mage::registry('current_wedding'))    	
    		Mage::register('current_wedding', Mage::getModel('wedding/wedding'));
    	
    	return Mage::registry('current_wedding');
    }
    
    public function getWeddingTitle()
    {
    	$cg = $this->getCurrentWedding();
    	if($cg && $cg->getTitle())
    		return $cg->getTitle();    	
    	else
    		return "Wedding";
    }
    
    public function getBreadcrumbs()
    {    	
    	return $this->getCurrentWedding()->getBreadcrumbPath();    	
    }
}