<?php

class Lingotip_Translate_Block_Adminhtml_Install_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('installGrid');
      $this->setDefaultSort('translate_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('translate/install')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('install_id', array(
          'header'    => Mage::helper('translate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'install_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('translate')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('translate')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('translate')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('translate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('translate')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('install_id');
        $this->getMassactionBlock()->setFormFieldName('translate');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('translate')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('translate')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('translate/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('translate')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('translate')->__('Status'),
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