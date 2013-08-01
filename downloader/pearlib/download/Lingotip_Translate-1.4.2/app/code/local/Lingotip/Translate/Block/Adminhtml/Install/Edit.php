<?php

class Lingotip_Translate_Block_Adminhtml_Install_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml_install';
        
        $this->_updateButton('save', 'label', Mage::helper('translate')->__('Register'));
		$this->_removeButton('back', 'label', Mage::helper('translate')->__('Back'));
        $this->_updateButton('delete', 'label', Mage::helper('translate')->__('Delete Item'));
		$this->_removeButton('reset', 'label', Mage::helper('translate')->__('reset'));

		$gridLabel = Mage::helper('translate')->getGridLabel();
		$this->_addButton('back1', array(
            'label'     => Mage::helper('translate')->__($gridLabel),
             'onclick'   => 'setLocation(\'' . $this->getUrl('translate/adminhtml_translate') . '\')',
			 'class'     => 'back',
        ), -1
		);
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('translate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'translate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'translate_content');
                }
            }
 
        ";
    }

    public function getHeaderText()
    {
            return Mage::helper('translate')->__('LingoTip Registration');
    }
}