<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php
class MLogix_Events_Block_Events extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getEvents($parent=0)     
    { 
        $model = $this->getCurrentEvents();
        if(!$model) return array();
        
        if($parent)
        	return $model->getEvents($parent);
        else
        	return $model->getEvents($model->getId());
    }
    
    public function getImageUrl($itemId)
    {
    	$model = Mage::getModel('events/events')->load($itemId);
    	return $model->getImageUrl();
    }
    
    public function getViewUrl($itemId)
    {
    	return $this->getUrl("*/*/view").'id/'.$itemId;
    }
    
    public function getCurrentEvents()
    {
    	if(!Mage::registry('current_events'))    	
    		Mage::register('current_events', Mage::getModel('events/events'));
    	
    	return Mage::registry('current_events');
    }
    
    public function getEventsTitle()
    {
    	$cg = $this->getCurrentEvents();
    	if($cg && $cg->getTitle())
    		return $cg->getTitle();    	
    	else
    		return "Events";
    }
    
    public function getBreadcrumbs()
    {    	
    	return $this->getCurrentEvents()->getBreadcrumbPath();    	
    }
}