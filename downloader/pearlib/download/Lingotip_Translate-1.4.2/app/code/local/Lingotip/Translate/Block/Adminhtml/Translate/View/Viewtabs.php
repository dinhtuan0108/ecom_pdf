<?php
class Lingotip_Translate_Block_Adminhtml_Translate_View_Viewtabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
 
	   $this->setTemplate('widget/tabshoriz.phtml');
   }

     protected function _prepareLayout()
    {
		 $this->setTemplate('lingotip/view.phtml');
    }
 
 
}