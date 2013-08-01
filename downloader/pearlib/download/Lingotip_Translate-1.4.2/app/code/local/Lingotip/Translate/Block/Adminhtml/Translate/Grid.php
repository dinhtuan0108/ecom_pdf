<?php
class Lingotip_Translate_Block_Adminhtml_Translate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  { 
      parent::__construct();
      $this->setId('translateGrid');
	  $this->setShowGlobalIcon(true);
      $this->setDefaultSort('request_id');
      $this->setDefaultDir('desc');
      $this->setSaveParametersInSession(true);
	  $this->setMessageBlockVisibility(false);
  }

  protected function _prepareCollection()
  {
	  $collection = Mage::getModel('translate/request')->getCollection();
	  $this->setCollection($collection);
	  return parent::_prepareCollection();
  }
 
  protected function _prepareColumns()
  {
	$isForSelectTT = $this->getRequest()->getParam('textboxid');
	  if(isset($isForSelectTT) && $isForSelectTT != "")
		{
			$languageLabel = 'Select Translated Text';
			$width = '50px';
		}
		else
		{
			$languageLabel = 'Target Language(s)';
			$width = '80px';
		}
      $this->addColumn('request_id', array(
          'header'    => Mage::helper('translate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'request_id',
      ));


	   $this->addColumn('label', array(
			  'header'    => Mage::helper('translate')->__('Label'),
			  'align'     =>'left',
			  'index'     => 'label',
		  ));

      $this->addColumn('src_name', array(
          'header'    => Mage::helper('translate')->__('Source Language'),
		  'width'	  =>$width,
          'align'     =>'left',
          'index'     => 'src_name',
      ));
	  
	  $this->addColumn('trg_names', array(
          'header'    => Mage::helper('translate')->__($languageLabel),
          'align'     =>'left',
          'index'     => 'trg_names',
		  'renderer'  => 'Lingotip_Translate_Block_Adminhtml_Translate_Grid_Renderer_Target',
      ));
 	
 
      $this->addColumn('source', array(
			'header'    => Mage::helper('translate')->__('Source Text'),
			'width'     => '250px',
			'index'     => 'source',
			'renderer'  => 'Lingotip_Translate_Block_Adminhtml_Translate_Grid_Renderer_Source',
      ));
 	  
	  if(!isset($isForSelectTT) && $isForSelectTT == ""){
	  // No need to show them if we are coming from any cms select source text
	  $this->addColumn('level_id', array(
			'header'    => Mage::helper('translate')->__('Translation Level'),
			'width'     => '50px',
			'index'     => 'level_id',
			'type'  => 'options',
			'options' => Mage::getResourceModel('translate/translate')->getOptionArray(),
			'renderer'  => 'Lingotip_Translate_Block_Adminhtml_Translate_Grid_Renderer_Level',
      ));
	  
	  $this->addColumn('price', array(
			'header'    => Mage::helper('translate')->__('Total'),
			'width'     => '50px',
			'index'     => 'price',
      ));
	  
      $this->addColumn('action',
		array(
			'header'    =>  Mage::helper('translate')->__('Action'),
			'width'     => '200',
			'type'      => 'action',
			'getter'    => 'getId',
			'filter'    => false,
            'sortable'  => false,
			'renderer'  => 'Lingotip_Translate_Block_Adminhtml_Translate_Grid_Renderer_Actions',
        ));
	}	
       return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
		$isForSelectTT = $this->getRequest()->getParam('textboxid');
		if(!isset($isForSelectTT) && $isForSelectTT == "")
		 {
				$this->setMassactionIdField('request_id');
				$this->getMassactionBlock()->setFormFieldName('translate');
				$this->getMassactionBlock()->addItem('delete', array(
					 'label'    => Mage::helper('translate')->__('Delete'),
					 'url'      => $this->getUrl('*/*/massDelete'),
					 'confirm'  => Mage::helper('translate')->__('Are you sure?')
				));
				return $this;
		 }	
    }

  public function getRowUrl($row)
  {	
  	  return '';
  	  $paidYesOrNo = Mage::getResourceModel('translate/request')->getPaymentStatusById($row->getId());
	  if($paidYesOrNo == "no")
	  {
      	return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	  }	
  }

   public function getGlobalIcon()
   {	
        return '<img src="'.$this->getSkinUrl('images/fam_link.gif').'" alt="'.$this->__('Global Attribute').'" title="'.$this->__('This attribute shares the same value in all the stores').'" class="attribute-global"/>';
   }

}