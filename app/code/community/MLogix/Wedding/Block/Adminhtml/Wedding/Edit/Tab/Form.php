<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?>
<?php

class MLogix_Wedding_Block_Adminhtml_Wedding_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('wedding_form', array('legend'=>Mage::helper('wedding')->__('Item information')));
     
      
      
      
      $model = Mage::getModel('wedding/wedding');
      
      $categories = $model->getCategories(0,0);
      
      $ac = array();
      
      $ac[0] = array('value'=>0, 'label'=>Mage::helper('wedding')->__('Wedding Categories (Root)'));
      foreach($categories as $key=>$category)
      	$ac[] = array('value'=>$category['wedding_id'], 'label'=>Mage::helper('wedding')->__($category['item_title']));
      
      $fieldset->addField('parent_id', 'select', array(
      	'label' => Mage::helper('wedding')->__('Parent'),
      	'required' => true,
      	'name'=>'parent_id',
      	'values'=>$ac
      ));      
      
      $fieldset->addField('item_title', 'text', array(
          'label'     => Mage::helper('wedding')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'item_title',
      ));
      
      $fieldset->addField('description', 'text', array(
          'label'     => Mage::helper('wedding')->__('Description'),          
          'required'  => false,
          'name'      => 'description',
      ));      

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('wedding')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
	  
	  $weddingId = $this->getRequest()->getParam('id');
	  if($weddingId)
	  {
	  	  $weddingModel = Mage::getModel('wedding/wedding')->load($weddingId);	  	  
	  	  
	  	  $filename = $weddingModel->getFilename();
	  	  $mediaUrl = $weddingModel->getMediaUrl();	
	  	  
	  	  if($filename)
	  	  {
			  $fieldset->addField('img', 'note', array(
			  	'label'	=> 'Image',
			  	'required' => false,
			  	'text' => '<img src="'.$mediaUrl.$filename.'"/>'			  	
			  ));
	  	  }
	  }	  
	  
      $fieldset->addField('alt', 'text', array(
          'label'     => Mage::helper('wedding')->__('Alt'),          
          'required'  => false,
          'name'      => 'alt',
      ));      	  
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('wedding')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('wedding')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('wedding')->__('Disabled'),
              ),
          ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getWeddingData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEWeddingata());
          Mage::getSingleton('adminhtml/session')->setWeddingData(null);
      } elseif ( Mage::registry('wedding_data') ) {
          $form->setValues(Mage::registry('wedding_data')->getData());
      }
      return parent::_prepareForm();
  }
}