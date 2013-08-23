<?php

class MLogix_Wedding_Block_Adminhtml_Wedding_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('weddingGrid');
      $this->setDefaultSort('wedding_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('wedding/wedding')->getCollection();

      $this->setCollection($collection);
	  
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('wedding_id', array(
          'header'    => Mage::helper('wedding')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'wedding_id',
      ));

      $this->addColumn('item_title', array(
          'header'    => Mage::helper('wedding')->__('Title'),
          'align'     =>'left',
          'index'     => 'item_title',
      ));

	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('wedding')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

      $this->addColumn('status', array(
          'header'    => Mage::helper('wedding')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('wedding')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('wedding')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('wedding')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('wedding')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('wedding_id');
        $this->getMassactionBlock()->setFormFieldName('wedding');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('wedding')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('wedding')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('wedding/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('wedding')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('wedding')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}