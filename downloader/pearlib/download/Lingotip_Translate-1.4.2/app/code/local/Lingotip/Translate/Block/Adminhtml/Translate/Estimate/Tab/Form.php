<?php
class Lingotip_Translate_Block_Adminhtml_Translate_Estimate_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
 
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
	  $model  = Mage::getModel('translate/translate');
      $fieldset = $form->addFieldset('translate_form', array('legend'=>Mage::helper('translate')->__('Estimate Translation')));
     
	  $id = (int) $this->getRequest()->getParam('id');
	  $data  = $model->load($id);
	  $source = $data->getSrcName();
	  $target = $data->getTrgNames();

	  $sourceLanguageHtml  = Mage::helper('translate')->getSourceHtml($source);
	  $fieldset->addField('src_name', 'note', array(
          'label'     => Mage::helper('translate')->__('Source Language'),
          'name'      => 'src_name',
		  'disabled'  =>true,
          'text'    => $sourceLanguageHtml,
      )); 

	 
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
		 // 'disabled'  =>true,
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
		//$loadData['source'] = substr($loadData['source'],0,2);
 
          $form->setValues($loadData);
      }
      return parent::_prepareForm();
  }
}