<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Source_EditableAreas
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'description',             'label' => Mage::helper('translate')->__('Product Description')),
            array('value' => 'short_description',       'label' => Mage::helper('translate')->__('Product Short Description')),
            array('value' => 'page_content',            'label' => Mage::helper('translate')->__('CMS Page Content')),
            array('value' => 'block_content',           'label' => Mage::helper('translate')->__('Static Block Content')),
            array('value' => '_generaldescription',     'label' => Mage::helper('translate')->__('Category Description')),
            array('value' => 'text',                    'label' => Mage::helper('translate')->__('Newsletter Template')),
            array('value' => 'template_text',           'label' => Mage::helper('translate')->__('Email Templates')),
        );
    }
}

