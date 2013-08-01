<?php

class Lingotip_Translate_Block_Adminhtml_Translate_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'view_form',
                                      'action' => $this->getUrl('*/*/saveResponse', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form->setUseContainer(true); 
      $this->setForm($form);
      return parent::_prepareForm();
  }
 
	
}