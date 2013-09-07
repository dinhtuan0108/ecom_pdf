<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?>
<?php

class MLogix_Events_Block_Adminhtml_Events_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('events_form', array('legend'=>Mage::helper('events')->__('Item information')));
     
      
      
      
      $model = Mage::getModel('events/events');
      
      $categories = $model->getCategories(0,0);
      
      $ac = array();
      
      $ac[0] = array('value'=>0, 'label'=>Mage::helper('events')->__('Events Categories (Root)'));
      foreach($categories as $key=>$category)
      	$ac[] = array('value'=>$category['events_id'], 'label'=>Mage::helper('events')->__($category['item_title']));
      
      $fieldset->addField('parent_id', 'select', array(
      	'label' => Mage::helper('events')->__('Parent'),
      	'required' => true,
      	'name'=>'parent_id',
      	'values'=>$ac
      ));      
      
      $fieldset->addField('item_title', 'text', array(
          'label'     => Mage::helper('events')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'item_title',
      ));
      
      $fieldset->addField('description', 'textarea', array(
          'label'     => Mage::helper('events')->__('Description'),          
          'required'  => false,
          'name'      => 'description',
      ));      

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('events')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
	  
	  $eventsId = $this->getRequest()->getParam('id');
	  if($eventsId)
	  {
	  	  $eventsModel = Mage::getModel('events/events')->load($eventsId);	  	  
	  	  
	  	  $filename = $eventsModel->getFilename();
	  	  $mediaUrl = $eventsModel->getMediaUrl();	
	  	  
	  	  if($filename)
	  	  {
			  $fieldset->addField('img', 'note', array(
			  	'label'	=> 'Image',
			  	'required' => false,
			  	'text' => '<img src="'.$mediaUrl.$filename.'"/>'			  	
			  ));
	  	  }
	  }	  
	  
//      $fieldset->addField('alt', 'text', array(
//          'label'     => Mage::helper('events')->__('Photographer'),          
//          'required'  => false,
//          'name'      => 'alt',
//      ));      	  
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('events')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('events')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('events')->__('Disabled'),
              ),
          ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getEventsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEventsData());
          Mage::getSingleton('adminhtml/session')->setEventsData(null);
      } elseif ( Mage::registry('events_data') ) {
          $form->setValues(Mage::registry('events_data')->getData());
      }
      return parent::_prepareForm();
  }
}