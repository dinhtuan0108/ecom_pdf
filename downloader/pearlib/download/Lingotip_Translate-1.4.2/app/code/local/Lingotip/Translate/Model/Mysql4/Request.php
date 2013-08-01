<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Mysql4_Request extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the translate_id refers to the key field in your database table.
        $this->_init('translate/request', 'request_id');
    }
	
	public function getPaymentStatusById($id)
    {
         if (!empty($id)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'paid');
			if ($id){
				$select->where('request_id = ?', $id);
			}
			return $this->_getReadAdapter()->fetchOne($select);
        }
    }
 
	
}