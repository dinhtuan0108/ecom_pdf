<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Adminhtml_Templates extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $magentoAttributes;
    public function __construct()
    {
        $this->addColumn('menu', array(
            'label' => Mage::helper('adminhtml')->__('Category'),
            'style' => 'width:100px'
        ));
    	$this->addColumn('name', array(
            'label' => Mage::helper('adminhtml')->__('Name'),
            'style' => 'width:100px'
        ));
        $this->addColumn('description', array(
            'label' => Mage::helper('adminhtml')->__('Description'),
            'style' => 'width:150px'
        ));
        $this->addColumn('template', array(
            'label' => Mage::helper('adminhtml')->__('Template'),
            'style' => 'width:220px'
        ));
        $this->addColumn('image', array(
            'label' => Mage::helper('adminhtml')->__('Preview Image'),
            'style' => 'width:160px'
        ));
        $this->addColumn('type', array(
            'label' => Mage::helper('adminhtml')->__('Use Template-Filter'),
    		'style' => 'width:30px'
        ));
        $this->_addAfter = true;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add new template');
        parent::__construct();
 	}
    protected function _renderCellTemplate($columnName)
    {
	    $column     = $this->_columns[$columnName];
    	$inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
    	if($columnName == 'image' )
    	{
    		return '<input readonly="readonly" type="text" name="' . $inputName . '" value="#{' . $columnName . '}" class="input-text" style="'.$column['style'] . '"/> <input type="file" name="XFILE' . $inputName . '" value="#{' . $columnName . '}" size="6"  class="' .
	            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
	            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    	}else if ($columnName == 'template'||$columnName == 'description')
        {
	        return '<textarea type="text" name="' . $inputName . '"  ' .
    	        ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
        	    (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            	(isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '>#{' . $columnName . '}</textarea>';
        }
    	return parent::_renderCellTemplate($columnName);
    }
}
