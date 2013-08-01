<?php
class Lingotip_Translate_Block_Adminhtml_Translate_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('translate_tabs');
      $this->setDestElementId('view_form');
	  $css = $this->getGlobalIcon();
      $this->setTitle($css);
	  //$this->setTemplate('widget/tabshoriz.phtml');
   }
 
 protected function _beforeToHtml()
  {	 
  	//$this->setTemplate('lingotip/view.phtml');
      $this->addTab('form_section', array(
          'label'     => Mage::helper('translate')->__('View Translation Details'),
          'title'     => Mage::helper('translate')->__('View Translation Details'),
          'content'   => $this->getLayout()->createBlock('translate/adminhtml_translate_view_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  } 
  
   public function getGlobalIcon()
    {
        return '<img src="'.Mage::getDesign()->getSkinUrl('images/lingotip/lingotip.png').'" alt="'.$this->__('LingoTip').'" title="'.$this->__('LingoTip').'" class=""/>';
    }
	
}