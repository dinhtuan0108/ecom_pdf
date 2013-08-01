<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  	 	
		
      $form = new Varien_Data_Form(array(
                                      'id' => 'add_form',
									  'name' => 'add_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
 
    $form->addField('isAddMode', 'hidden', array('name' => 'isAddMode', 'value' => 'continue'));
	
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}