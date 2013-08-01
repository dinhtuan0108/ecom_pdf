<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Adminhtml_Attributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $magentoAttributes;
    public function __construct()
    {
        $this->addColumn('name', array(
            'label' => Mage::helper('adminhtml')->__('Attribute Name'),
            'style' => 'width:200px'
        ));
    	$this->addColumn('type', array(
            'label' => Mage::helper('adminhtml')->__('Use Template-Filter'),
    		'style' => 'width:30px'
        ));
        $this->_addAfter = true;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add new edit attribute');
        parent::__construct();
 	}
    protected function _renderCellTemplateXX($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
 
        if($columnName == 'name')
        	return parent::_renderCellTemplate($columnName);
        return '<input checked="0" type="checkbox" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
        	
        $rendered = '<select name="'.$inputName.'">';
		$rendered .= '<option value="standard">Standard - #{' . $columnName . '} dont use template filter</option>';
        $rendered .= '<option value="use_filter">Extended - use template filter</option>';
        $rendered .= '</select>';
        return $rendered;
    }
/*	
    protected $magentoOptions;

    public function __construct()
    {
        $this->addColumn('id', array(
            'label' => Mage::helper('adminhtml')->__('Textarea dddd ID'),
            'size'  => 28,
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add aaaatextarea CSS ID');
        
        parent::__construct();
        //$this->setTemplate('fontis/campaignmonitor/system/config/form/field/array_dropdown.phtml');
    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
    }
    */
}
