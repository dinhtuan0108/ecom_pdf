<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Postedit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    { 
        parent::__construct();
                 
        $this->_objectId = 'id';
	    $this->_mode = 'postedit';
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml_translate';
		
       $this->_removeButton('save', array(
            'label'     => Mage::helper('translate')->__('Post'),
            'onclick'   => 'posteditForm.submit();',
			'class'		=> '',
         ), 1);
		$gridLabel = Mage::helper('translate')->getGridLabel();
        $this->_removeButton('delete', 'label', Mage::helper('translate')->__('Delete Item'));
 		$this->_removeButton('reset', 'label', Mage::helper('translate')->__('Reset'));
		$this->_removeButton('back','class','','label', Mage::helper('translate')->__($gridLabel));
 
		
		$this->_addButton('back1', array(
            'label'     => Mage::helper('translate')->__('Back'),
             'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/edit/isedit/yes/rid/', array('rid' => $this->getRequest()->getParam('rtxnid'))) . '\')',
			 'class'     => 'back',
        ), 1
		);
		 $this->_addButton('save', array(
            'label'     => Mage::helper('translate')->__('Post'),
            'onclick'   => 'posteditForm.submit();',
			'class'		=> 'save',
         ), 2);
		$this->_addButton('back2', array(
            'label'     => Mage::helper('translate')->__($gridLabel),
             'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
			 'class'     => 'back',
        ), -1);
		
		 
        $this->_formScripts[] = "

	    posteditForm = new varienForm('postedit_form', '');

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
 		return Mage::helper('translate')->__('Request a Translation');
     }
}