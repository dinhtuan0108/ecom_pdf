<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/saveEdit', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
	  
	  $this->_mode = 'edit'; 
      $form->setUseContainer(true);
	  $form->addField('isAddMode', 'hidden', array('name' => 'isAddMode', 'value' => 'continue'));
	  $isedit = $this->getRequest()->getParam('isedit');
	  
	  if(isset($isedit) && $isedit !="" && $isedit == "yes"){  // case when page comes from grid in edit mode
	 	 $id = $this->getRequest()->getParam('rid');
		 $model = Mage::getModel('translate/request')->load($id);
		 $form->addField('isedit', 'hidden', array('name' => 'isedit', 'value' => 'yes'));
 		 $form->addField('rtxnid', 'hidden', array('name' => 'rtxnid', 'value' => $id));// rtxn id (maintain in hidden field) is used in the LT17 for edit case
 	  }else{
	  		$id = $this->getRequest()->getParam('id'); // case when page comes after submit estimate again
 			$rtxnid = $this->getRequest()->getParam('rtxnid'); // rtxn id (maintain in hidden field) is used in the LT17 for edit case
			$form->addField('rtxnid', 'hidden', array('name' => 'rtxnid', 'value' => $rtxnid));
 			$model = Mage::getModel('translate/translate')->load($id);
	  }
 	  if(isset($id) && $id != "")
	  {	
			 $form->addField('level_id', 'hidden', array('name' => 'level_id', 'value' => $model->getLevelId()));
			$form->addField('src_name', 'hidden', array('name' => 'src_name', 'value' => $model->getSrcName()));
			$form->addField('trg_names', 'hidden', array('name' => 'trg_names', 'value' => $model->getTrgNames()));
	  }
			
      $this->setForm($form);
      return parent::_prepareForm();
  }
}