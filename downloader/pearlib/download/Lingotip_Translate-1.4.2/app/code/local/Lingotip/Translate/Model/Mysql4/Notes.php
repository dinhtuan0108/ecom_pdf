<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Mysql4_Notes extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the translate_id refers to the key field in your database table.
        $this->_init('translate/notes', 'notes_id');
    }
	
	public function checkIsDisputeAllow($txn)
    {
         if (!empty($txn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'tid');
			if ($txn){
 				$select->where('is_dispute = ?', 0); 
				$select->where('txn = ?', $txn);
			}
			 
			return $this->_getReadAdapter()->fetchOne($select);
        }
    }
	
	public function getAllNotes($txn,$get_tid)
    {
        if (!empty($txn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable());
			if ($txn){
 				$select->where('is_dispute = ?', 0);
				$select->where('txn = ?', $txn);
				 $select->where('tid = ?', $get_tid);
				//$select->where('incoming = ?', 0);
			}
			return $this->_getReadAdapter()->fetchAll($select);
        }
    }
	
	
	public function getAllTidPostedByLingotipTranslater($txn)
	{
		if (!empty($txn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(),'tid')->distinct('tid');
			if ($txn){
 				$select->where('is_dispute = ?', 0);
				$select->where('txn = ?', $txn);

			}
			return $this->_getReadAdapter()->fetchAll($select);
        }
	}
	
	public function getTranslator($txn)
    {
	//will fetch only one translator who has made the dummy record while accept the status
        if (!empty($txn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(),'tid');
			if ($txn){
 				//$select->where('incoming = ?', 1);
				$select->where('txn = ?', $txn);
				$select->where('note = ?', "Translator accepted translation.");
				//$select->setOrder('entrydate');
				 $select = $select."ORDER BY entrydate DESC LIMIT 0,1";
			}
			return $this->_getReadAdapter()->fetchOne($select);
        }
    }
	
	
	
}