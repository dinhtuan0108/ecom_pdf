<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Postedit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'postedit_form',
                                      'action' => $this->getUrl('*/*/savePostedit', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
 	
	   $id = (int) $this->getRequest()->getParam('id');// Edit Case : if we "Estimate Again", record insert in translate(lt_estimate) table , id generates and we load the data from  translate(lt_estimate) table 
 
	  if(!isset($id) && $id == "" || $id == 0)
	  {
		// Edit Case:  if we directly click on "Continue for Translation" the record does not save in translation(lt_estimate) table , so the id does not create , therefore we load the data from lt_request table 
		  $id = $this->getRequest()->getParam('rtxnid');
		  $model = Mage::getModel('translate/request')->load($id);
	  }else{

	  	$model = Mage::getModel('translate/translate')->load($id);
	  }
	  
	  $rtxnId = $this->getRequest()->getParam('rtxnid');
	  if(isset($id) && $id != "" && $id != 0)
	  {	

	  	  // Edit Case : if we "Estimate Again", record insert in translate(lt_estimate) table , id generates and we load the data from  translate(lt_estimate) table 
 			$form->addField('level_id', 'hidden', array('name' => 'level_id', 'value' => $model->getLevelId()));
			$form->addField('src_name', 'hidden', array('name' => 'src_name', 'value' => $model->getSrcName()));
			$form->addField('trg_names', 'hidden', array('name' => 'trg_names', 'value' => $model->getTrgNames()));
			$form->addField('source', 'hidden', array('name' => 'source', 'value' => $model->getSource()));
			$form->addField('txn', 'hidden', array('name' => 'txn', 'value' => $rtxnId));
			$form->addField('price', 'hidden', array('name' => 'price', 'value' => $model->getPrice()));
	  }

	 $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}