<?php
/**** Developed By Pankaj Gupta ****/

                
class Lingotip_Translate_Block_Textareas extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoOptions;

    public function __construct()
    {
        $this->addColumn('id', array(
            'label' => Mage::helper('translate')->__('Textarea CSS ID'),
            'size'  => 28,
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('translate')->__('Add textarea CSS ID');
        
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
}
