<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Post_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  	 	
		
      $form = new Varien_Data_Form(array(
                                      'id' => 'post_form',
                                      'action' => $this->getUrl('*/*/savePost', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
 	
	  $id = $this->getRequest()->getParam('id');
	  if(isset($id) && $id != "")
	  {	
	  		$modelTranslate = Mage::getModel('translate/translate')->load($id);
			$form->addField('level_id', 'hidden', array('name' => 'level_id', 'value' => $modelTranslate->getLevelId()));
			$form->addField('src_name', 'hidden', array('name' => 'src_name', 'value' => $modelTranslate->getSrcName()));
			$form->addField('trg_names', 'hidden', array('name' => 'trg_names', 'value' => $modelTranslate->getTrgNames()));
			$form->addField('source', 'hidden', array('name' => 'source', 'value' => $modelTranslate->getSource()));
	  }
	  
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}