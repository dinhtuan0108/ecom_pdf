<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php
class MLogix_Wedding_AlbumController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->_redirect('*/*/view');
    }
    
    public function viewAction()
    {
    	$id = $this->getRequest()->getParam('id');

    	if(!$id) $id = 0;
    	
    	$model = Mage::getModel('wedding/wedding')->load($id);
    	
    	Mage::register('current_wedding', $model);    	  	
    	
		$this->loadLayout();     
		$this->renderLayout();    	
    }            

}