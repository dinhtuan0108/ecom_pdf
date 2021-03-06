<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

class MLogix_Gallery_Block_Adminhtml_Gallery_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_gallery';
        
        $this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Item'));
		
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
    	
    	$html = $this->getChildHtml('gallery_edit_tabs');
    	$js = "<script type=\"javascript\">";	
    	$js .= "if(gallery_tabsJsTabs) gallery_tabsJsTabs.showTabContent(gallery_tabsJsTabs.tabs[0]);";
    	$js .= "editForm = new varienForm('edit_form', '');";   
    	$js .= "</script>";
    	
    	return $html . parent::getFormHtml(). $js;
    }    

    public function getHeaderText()
    {
        if( Mage::registry('gallery_data') && Mage::registry('gallery_data')->getId() ) {
            return Mage::helper('gallery')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('gallery_data')->getTitle()));
        } else {
            return Mage::helper('gallery')->__('Add Item');
        }
    }
}