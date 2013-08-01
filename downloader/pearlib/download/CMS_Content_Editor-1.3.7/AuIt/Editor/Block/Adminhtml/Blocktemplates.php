<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Adminhtml_BlockTemplates extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $magentoAttributes;
    public function __construct()
    {
    	$this->addColumn('name', array(
            'label' => Mage::helper('adminhtml')->__('Name'),
            'style' => 'width:100px'
        ));
    	$this->addColumn('version', array(
            'label' => Mage::helper('adminhtml')->__('Magento Version'),
            'style' => 'width:100px'
        ));
    	$this->addColumn('op', array(
            'label' => Mage::helper('adminhtml')->__('>= (1)'),
            'style' => 'width:30px'
        ));
        $this->addColumn('template', array(
            'label' => Mage::helper('adminhtml')->__('Template path'),
            'style' => 'width:150px'
        ));
        $this->_addAfter = true;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add new template');
        parent::__construct();
 	}
}
