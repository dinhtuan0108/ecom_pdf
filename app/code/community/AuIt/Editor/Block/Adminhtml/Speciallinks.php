<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Adminhtml_Speciallinks extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $magentoAttributes;
    public function __construct()
    {
    	$this->addColumn('menu', array(
            'label' => Mage::helper('adminhtml')->__('Menu'),
            'style' => 'width:100px'
        ));
    	$this->addColumn('link', array(
            'label' => Mage::helper('adminhtml')->__('Link'),
            'style' => 'width:250px'
        ));
    	$this->addColumn('label', array(
            'label' => Mage::helper('adminhtml')->__('Label'),
            'style' => 'width:100px'
        ));
    	$this->addColumn('modul', array(
            'label' => Mage::helper('adminhtml')->__('Modul'),
            'style' => 'width:100px'
        ));
    	$this->addColumn('comment', array(
            'label' => Mage::helper('adminhtml')->__('Comment'),
            'style' => 'width:150px'
        ));
        $this->_addAfter = true;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add new link');
        parent::__construct();
 	}
}
