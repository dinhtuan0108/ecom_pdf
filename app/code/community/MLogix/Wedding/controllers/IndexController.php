<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?>
<?php
class MLogix_Wedding_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
		$this->_redirect('*/list');
			
		$this->loadLayout();     
		$this->renderLayout();
    }
}