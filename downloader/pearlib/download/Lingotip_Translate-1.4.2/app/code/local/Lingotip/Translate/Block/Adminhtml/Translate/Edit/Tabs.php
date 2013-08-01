<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('translate_tabs');
	  $this->_mode = 'edit';
	  $this->setDestElementId('add_form');
	  $css = $this->getGlobalIcon();
      $this->setDestElementId('edit_form');
      $css = Mage::helper('translate')->getLingoTipLeftIcon();
      $this->setTitle($css);
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('translate')->__('Edit Translation'),
          'title'     => Mage::helper('translate')->__('Edit Translation'),
          'content'   => $this->getLayout()->createBlock('translate/adminhtml_translate_edit_tab_form')->toHtml(),
      ));
      return parent::_beforeToHtml();
  }
    /*public function getGlobalIcon()
    {
        return '<img src="'.Mage::getDesign()->getSkinUrl('images/lingotip/lingotip.jpg').'" alt="'.$this->__('LingoTip').'" title="'.$this->__('LingoTip').'" class=""/>';
    }*/

}