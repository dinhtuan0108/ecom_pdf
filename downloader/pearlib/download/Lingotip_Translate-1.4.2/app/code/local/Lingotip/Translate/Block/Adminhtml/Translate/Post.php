<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Post extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    { 
        parent::__construct();
                 
        $this->_objectId = 'id';
	    $this->_mode = 'post';
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml_translate';
		
       $this->_removeButton('save', array(
            'label'     => Mage::helper('translate')->__('Post'),
            'onclick'   => 'postForm.submit();',
            'class'     => 'save',
			'title'		=>'Click here to post your translation request',
			'alt'		=>'Click here to post your translation request',
        ), 1);
		//echo $this->getButtonHtml();
 		
		$this->_removeButton('back1', array(
            'label'     => Mage::helper('translate')->__('Back'),
             'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/estimate', array('id' => $this->getRequest()->getParam('id'))) . '\')',
			 'class'     => 'back',
        ), -1
		);
		
		$gridLabel = Mage::helper('translate')->getGridLabel();
        $this->_removeButton('delete', 'label', Mage::helper('translate')->__('Delete Item'));
		$this->_removeButton('reset', 'label', Mage::helper('translate')->__('Reset'));
		$this->_removeButton('back', 'label', Mage::helper('translate')->__($gridLabel),0);
		
		
		$this->_addButton('back1', array(
				'label'     => Mage::helper('translate')->__($gridLabel),
				 'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
				 'class'     => 'back',
			), -1
			);
		
		$this->_addButton('id_back',array( 
			'label' => Mage::helper('translate')->__('Back'),
			'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/estimate', array('id' => $this->getRequest()->getParam('id'))) . '\')',
			 'class'     => 'back',
			), 1);		
				
		$this->_addButton('save', array(
            'label'     => Mage::helper('translate')->__('Post'),
            'onclick'   => 'postForm.submit();',
            'class'     => 'save',
        ), 2);
		
        $this->_formScripts[] = "

	    postForm = new varienForm('post_form', '');

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