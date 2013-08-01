<?php
class Lingotip_Translate_Block_Adminhtml_Translate_View extends Mage_Adminhtml_Block_Widget_Form_Container
{ 
    public function __construct()
    {
        parent::__construct();
		
        $this->_objectId = 'id';
		 $this->_mode = 'view';
		 
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml_translate';
        $this->_mode = 'view';
      
	    $this->_removeButton('save', 'label', Mage::helper('translate')->__('Delete Item'));
	  
		 
        $this->_removeButton('delete', 'label', Mage::helper('translate')->__('Delete Item'));
		$this->_removeButton('reset', 'label', Mage::helper('translate')->__('reset Item'));
	    $this->_removeButton('back','class','' , 'label', Mage::helper('translate')->__('Back to List'));
 
        $this->_formScripts[] = "
		
		viewForm = new varienForm('view_form', '');
		
            function toggleEditor() {
                if (tinyMCE.getInstanceById('translate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'translate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'translate_content');
                }
            }
 
        ";
    }
   public function getHeaderWidth()
    {
        return '';
    }

    public function getHeaderCssClass()
    {
        return '';
    }
	
	public function getHeaderHtml()
    {
        return '';
    }
	
    public function getHeaderText()
    {
	   return '';	
       return Mage::helper('translate')->__('LingoTip');
    }
}