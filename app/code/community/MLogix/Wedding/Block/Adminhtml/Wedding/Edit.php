<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php

class MLogix_Wedding_Block_Adminhtml_Wedding_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'wedding';
        $this->_controller = 'adminhtml_wedding';
        
        $this->_updateButton('save', 'label', Mage::helper('wedding')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('wedding')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    public function getFormHtml()
    {
		//$html = "<div id=\"messages\">";
		//$html .= $this->getMessagesBlock()->getGroupedHtml();
		//$html .= "</div>";
    	
    	$html = $this->getChildHtml('wedding_edit_tabs');
    	$js = "<script type=\"javascript\">";	
    	$js .= "if(wedding_tabsJsTabs) wedding_tabsJsTabs.showTabContent(wedding_tabsJsTabs.tabs[0]);";
    	$js .= "editForm = new varienForm('edit_form', '');";   
    	$js .= "</script>";
    	
    	return $html . parent::getFormHtml(). $js;
    }    

    public function getHeaderText()
    {
        if( Mage::registry('wedding_data') && Mage::registry('wedding_data')->getId() ) {
            return Mage::helper('wedding')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('wedding_data')->getTitle()));
        } else {
            return Mage::helper('wedding')->__('Add Item');
        }
    }
}