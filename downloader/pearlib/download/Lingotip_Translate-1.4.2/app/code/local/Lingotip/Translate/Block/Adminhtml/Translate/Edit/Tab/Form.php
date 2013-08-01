<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

		
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
       $fieldset = $form->addFieldset('translate_form', array('legend'=>Mage::helper('translate')->__('Edit Translation')));

	$isedit = $this->getRequest()->getParam('isedit');
	$showblock = $this->getRequest()->getParam('showblock');
	
	if(isset($isedit) && $isedit != "" && $isedit == "yes" && !isset($showblock) && $showblock != "yes"){
		$id     = $this->getRequest()->getParam('rid'); // case when page comes from grid in edit mode
		$model  = Mage::getModel('translate/request')->load($id);
	}
	else{
			$id     = $this->getRequest()->getParam('id'); // case when page comes after submit estimate again
			$model  = Mage::getModel('translate/translate')->load($id);
	}
		  
	  $data  = $model->load($id);
	//  echo '<pre>';print_r($data );die;
	  $source = $data->getSrcName();
	  $target = $data->getTrgNames();
	
	  //$modelTranslate = Mage::getModel('translate/translate');
	  $sourceLanguageHtml  = Mage::helper('translate')->getSourceHtml($source);
	  $fieldset->addField('src_name', 'note', array(
          'label'     => Mage::helper('translate')->__('Source Language'),
          'name'      => 'src_name',
		  'disabled'  =>true,
          'text'    => $sourceLanguageHtml,
      )); 
	  
	
	  //$getTargetLanguage  = $modelTranslate->getTargetLanguagesArrayFormat($source,$target);
	  $targetLanguageHtml  = Mage::helper('translate')->getTargetHtml($source,$target);
	  $fieldset->addField('trg_names', 'note', array(
                'name'      => 'language[]',
				'disabled'  =>true,
                'label'     => Mage::helper('translate')->__('Target Language(s)'),
                'title'     => Mage::helper('translate')->__('Target Language(s)'),
                'required'  => true,
                'text'    => $targetLanguageHtml,
            ));
			
	 

      $fieldset->addField('level_id', 'select', array(
          'label'     => Mage::helper('translate')->__('Language Level'),
          'name'      => 'level_id',
		 'disabled'  =>true,
          'values'    => array(
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('translate')->__('Fluent'),
              ),

              array(
                  'value'     => 3,
                  'label'     => Mage::helper('translate')->__('Advanced/Native'),
              ),
			  
			  array(
                  'value'     => 4,
                  'label'     => Mage::helper('translate')->__('Professional'),
              ),
          ),
      ));



	 $fieldset->addField('source', 'textarea', array(
          'name'      => 'source',
          'label'     => Mage::helper('translate')->__('Text'),
          'title'     => Mage::helper('translate')->__('Text'),
          'style'     => 'width:275px; height:220px;',
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getTranslateData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTranslateData());
          Mage::getSingleton('adminhtml/session')->setTranslateData(null);
      } elseif ( Mage::registry('translate_data') ) {
          
		  $loadData = Mage::registry('translate_data')->getData();
		$loadData['source'] = stripslashes($loadData['source']);
	  
          $form->setValues($loadData);
 
      }

       return parent::_prepareForm();
  }



}