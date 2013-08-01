<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Mysql4_Dispute extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the dispute_id refers to the key field in your database table.
        $this->_init('translate/dispute', 'dispute_id');
    }
	
	public function getNoteByTxn($txn)
    {
         if (!empty($txn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable());
			if ($txn){
 				$select->where('txn = ?', $txn);
			}
			return $this->_getReadAdapter()->fetchAll($select);
        }
    }
	
	public function getdByTxn($txn)
    {
         if (!empty($txn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(),'dispute_id');
			if ($txn){
 				$select->where('txn = ?', $txn);
			}
			return $this->_getReadAdapter()->fetchOne($select);
        }
    }
	
	public function getCountDispute($txn)
    {
	//will fetch only one translator who has made the dummy record while accept the status
        if (!empty($txn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(),'dispute_id');
			if ($txn){
 				//$select->where('incoming = ?', 1);
				$select->where('txn = ?', $txn);
			}
			return $this->_getReadAdapter()->fetchAll($select);
        }
    }
}