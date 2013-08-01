<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Block_Adminhtml_Translate extends Mage_Adminhtml_Block_Widget_Grid_Container
{ 
  public function __construct()
  {
    $this->_controller = 'adminhtml_translate';
    $this->_blockGroup = 'translate';
    $isForSelectTT = $this->getRequest()->getParam('textboxid');
	
	if(isset($isForSelectTT) && $isForSelectTT != "")
	{
		$this->_headerText = Mage::helper('translate')->__('Select a Translation');		
	}
	else
	{
		$this->_headerText = Mage::helper('translate')->__('Manage Translations');
		$this->_addButtonLabel = Mage::helper('translate')->__('Request a Translation');
	}
 
	 
    parent::__construct();
	
$installModel = Mage::getModel('translate/install');
$registerationData = $installModel->getRegisterationData($installModel); 
$installId = $registerationData->getId(); 

	
	if(isset($isForSelectTT) && $isForSelectTT != "" || !isset($installId) && $installId == "" )
	{
		$this->_removeButton('add');
	}

		if(isset($installId) && $installId != "" )
		{
		}
		else
		{
			$this->_addButton('registerc', array(
				'label'     => Mage::helper('translate')->__('Registration'),
				'onclick'   => "location.href='".$this->getUrl('*/adminhtml_install/index')."'",
				'class'     => '',
    		));
		}
		
		
	 
  }
   
   
	
    public function getHeaderCssClass()
    {
        return 'icon-head ' . parent::getHeaderCssClass();
    }
}