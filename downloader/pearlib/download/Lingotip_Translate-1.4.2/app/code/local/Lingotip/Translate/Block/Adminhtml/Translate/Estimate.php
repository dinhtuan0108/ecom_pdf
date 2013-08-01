<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Estimate extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() 
    {
        parent::__construct(); 

        $this->_objectId = 'id';
        $this->_blockGroup = 'translate'; 
		$this->_mode = 'estimate';
        $this->_controller = 'adminhtml_translate';
 
		$gridLabel = Mage::helper('translate')->getGridLabel();
		
		
		$this->_addButton('estimateagain',array( 
			'label' => Mage::helper('translate')->__('Estimate Again'),
			'onclick'   => 'estimateForm.submit();',
		), 1);
		
		
		$this->_addButton('cont',array( 
			'label' => Mage::helper('translate')->__('Continue'),
			'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/post', array('id' => $this->getRequest()->getParam('id'))) . '\')',
		), 2);
		
		$this->_addButton('cancel', array(
            'label'     => Mage::helper('translate')->__('Cancel'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
        ), 3);
		
        $this->_removeButton('save', 'label', Mage::helper('translate')->__('Save'));
		$this->_removeButton('delete', 'label', Mage::helper('translate')->__('Delete Item'));
		$this->_removeButton('reset', 'label', Mage::helper('translate')->__('Reset'));
		$this->_removeButton('back','class','','label', Mage::helper('translate')->__($gridLabel));
 
		$this->_addButton('back1', array(
            'label'     => Mage::helper('translate')->__($gridLabel),
             'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
			 'class'     => 'back',
        ), -1);
		
		/*$this->_updateButton('save', array(
            'label'     => Mage::helper('translate')->__('Continue for Translation'),
            'onclick'   => 'estimateForm.submit();',
        ), 1);*/
 

        $this->_formScripts[] = "
		
			estimateForm = new varienForm('estimate_form', '');


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