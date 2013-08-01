<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Paypal extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    { 
        parent::__construct();
                 
        $this->_objectId = 'id';
	    $this->_mode = 'paypal';
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml_translate';
		
       $this->_removeButton('save', array(
            'label'     => Mage::helper('translate')->__('Save'),
            'onclick'   => 'paypaltForm.submit();',
            'class'     => 'save',
        ), 1);
		
		$gridLabel = Mage::helper('translate')->getGridLabel();
		$this->_removeButton('reset', 'label', Mage::helper('translate')->__('Reset'));
        $this->_removeButton('delete', 'label', Mage::helper('translate')->__('Delete Item'));
 		$this->_removeButton('back','class','','label', Mage::helper('translate')->__($gridLabel));
 
		$this->_addButton('back1', array(
					'label'     => Mage::helper('translate')->__($gridLabel),
					 'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
					 'class'     => 'back',
				), -1
				);
		
		$directPaypal = $this->getRequest()->getParam('directPaypal');
		 if(isset($directPaypal) && $directPaypal == "yes")
		 {
			$this->_addButton('save', array(
            'label'     => Mage::helper('translate')->__('Pay'),
            'onclick'   => '',
            'class'     => 'disable-button',
        ), 2);
		
		 }else{
		$this->_addButton('save', array(
            'label'     => Mage::helper('translate')->__('Pay'),
            'onclick'   => 'paypalForm.submit();',
            'class'     => '',
        ), 2);
		
		}
			
		$this->_addButton('cancel', array(
            'label'     => Mage::helper('translate')->__('Cancel'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
        ), 3);
		
        $this->_formScripts[] = "

	    paypalForm = new varienForm('paypal_form', '');

            function toggleEditor() {
                if (tinyMCE.getInstanceById('translate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'translate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'translate_content');
                }
            }
 
        ";
		
    }
	public function getHeaderHtml()
    {
 
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }
	
      public function getHeaderText()
    {	
 
 		return Mage::helper('translate')->__('Request a Translation');
    }
	
}