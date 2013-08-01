<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Mysql4_Install extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the install_id refers to the key field in your database table.
        $this->_init('translate/install', 'install_id');
    }
	
	public function getRegisterationData($model)
    {
		$read = $this->_getReadAdapter();
		$select = $read->select();
 		$select->from($this->getMainTable());
		$data = $read->fetchRow($select);
		//$data['cpath'] = Mage::getUrl();
		$model->setData( ( is_array($data) ) ? $data : array() );
     }
}