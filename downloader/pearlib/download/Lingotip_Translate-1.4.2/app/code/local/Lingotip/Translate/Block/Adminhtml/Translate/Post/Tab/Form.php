<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Post_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
	 
      $fieldset = $form->addFieldset('translate_form', array('legend'=>Mage::helper('translate')->__('Post Translation')));
     
	  $id = (int) $this->getRequest()->getParam('id');
	  $model = Mage::getModel('translate/translate');
	  
	  $fieldset->addField('label', 'text', array(
          'label'     => Mage::helper('translate')->__('Label'),
          'name'      => 'label',  'required'  => true,
		  'title'	  =>'Give your request a name.',
		  'alt'		  =>'Give your request a name.',
		  'note'	  =>'Give your request a name.',
      )); 
	  
	  $fieldset->addField('note', 'textarea', array(
          'label'     => Mage::helper('translate')->__('Notes for the Translator'),
          'name'      => 'note',
      )); 
 
	  $fieldset->addField('captcha', 'text', array(
          'label'     => Mage::helper('translate')->__('Verification Code'),
		  'required'  => true,
          'name'      => 'captcha',
       )); 
	   
	   $captchaImagePath = $this->_getCaptchaHtml();
 	   $fieldset->addField('captcha_img_path','note',array(
                 		   'text' => $captchaImagePath,
       ));
  

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
		  'disabled'   => 'disabled',
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
		  'disabled'   => 'disabled',
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
  
   protected function _getCaptchaHtml()
    {
        $html = '';
		$helper = Mage::helper('translate');
		$loadInstallData = $helper->getUserDetail();
		$usermail = $loadInstallData->getEmail();
		$password = $loadInstallData->getPassword();
		$cid 	  = $loadInstallData->getCid();
		$SSL 	  = Lingotip_Translate_Helper_Data::SSL;
		$host  	  = Lingotip_Translate_Helper_Data::LTI_SERVER;
		$path 	  = Lingotip_Translate_Helper_Data::LTI_PATH;
		$LTstatApi= Lingotip_Translate_Helper_Data::LTstatApi;
		$path .= "/$LTstatApi"; //apiStatXML.php";
 
		$parameters="cid=$cid&u=$usermail&p=$password&status=getcaptcha";
		$lt_captcha = $helper->getLTIcall($host, $path, $parameters, $SSL, "CAPTCHA");
		//echo '<pre>';print_r($lt_captcha);die;
		$img = "http://$host/lti/tmp/$lt_captcha.jpg";

        $html .= "<img src='".$img."'/>";
        return $html;
    }
}