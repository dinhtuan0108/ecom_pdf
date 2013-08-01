<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/translate?id=15 
    	 *  or
    	 * http://site.com/translate/id/15 	
    	 */
    	/* 
		$translate_id = $this->getRequest()->getParam('id');

  		if($translate_id != null && $translate_id != '')	{
			$translate = Mage::getModel('translate/translate')->load($translate_id)->getData();
		} else {
			$translate = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($translate == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$translateTable = $resource->getTableName('translate');
			
			$select = $read->select()
			   ->from($translateTable,array('translate_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$translate = $read->fetchRow($select);
		}
		Mage::register('translate', $translate);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}