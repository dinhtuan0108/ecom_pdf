<?php

class Lingotip_Translate_Block_Adminhtml_Install_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('install_form', array('legend'=>Mage::helper('translate')->__('User Information')));
     
      $fieldset->addField('f_name', 'text', array(
          'label'     => Mage::helper('translate')->__('First name:'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'f_name',
      ));
	  
	   $fieldset->addField('l_name', 'text', array(
          'label'     => Mage::helper('translate')->__('Last name:'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'l_name',
      ));
	  
	   $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('translate')->__('LingoTip email:'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email',
      ));
	  
	   $fieldset->addField('password', 'password', array(
          'label'     => Mage::helper('translate')->__('LingoTip password:'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'password',
      ));
	  
	 /*  $fieldset->addField('cpath', 'text', array(
          'label'     => Mage::helper('translate')->__('Client access path:'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'cpath',
      ));
	  */
  	   $checkbox = $this->_getCheckboxHtml(); $termsCondition = $this->_getTermsConditionHtml();
 	   $fieldset->addField('terms_con','note',array(
	   						'label'     => Mage::helper('translate')->__('Terms & Conditions:'),
                 		    'text' => $checkbox.'&nbsp;&nbsp;'.$termsCondition,
							'required'  => true,
        ));
	
	  
	
  
      if ( Mage::getSingleton('adminhtml/session')->getTranslateData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTranslateData());
          Mage::getSingleton('adminhtml/session')->setTranslateData(null);
      } elseif ( Mage::registry('install_data') ) {
          $form->setValues(Mage::registry('install_data')->getData());
      }
      return parent::_prepareForm();
  }
  
  protected function _getTermsConditionHtml(){
		$html = '';
		
		$userData = Mage::helper('translate')->getUserDetail(); // load user detail
		$lturl = $userData->getLturl();
		if ($lturl) {
			sscanf($lturl, "http://%[^/]%s", $host, $path);
		}
		
	  	$html .= 'I have read and accept the <a href="'.$lturl.'/tnc_en.html" target="_blank">Terms & Conditions</a>';
        return $html;
  }
  
  protected function _getCheckboxHtml(){
  	$html = '';
	if($this->getTermsCon()){ $checked =  'checked="checked"' ;}else{$checked = '';}
  	$html .='<input type="checkbox" name="terms_con" id="terms_con" class="required-entry required-entry" value="1" title="" '.$checked .' class="checkbox" />'; 
  return $html;
  }
}