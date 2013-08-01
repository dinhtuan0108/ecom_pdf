<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Mysql4_Txn extends Mage_Core_Model_Mysql4_Abstract

{

    public function _construct()

    {    

        // Note that the translate_id refers to the key field in your database table.

        $this->_init('translate/txn', 'txn_id');

    }

	

	public function deleteByRequestId($requestId)

    {

        if (!empty($requestId)){

            $this->_getWriteAdapter()->delete(

                $this->getMainTable(),

                $this->_getWriteAdapter()->quoteInto('request_id=?', $requestId)

            );

        }

    }

	

	public function getIdByTxn($txn)

    {

         if (!empty($txn)){

            $select = $this->_getReadAdapter()->select()

            ->from($this->getMainTable(), 'txn_id');

			if ($txn){

 				$select->where('txn = ?', $txn);

			}

			return $this->_getReadAdapter()->fetchOne($select);

        }

    }

	

	

	public function getTxnByTxn($txn)

    {

         if (!empty($txn)){

            $select = $this->_getReadAdapter()->select()

            ->from($this->getMainTable(), 'txn');

			if ($txn){

 				$select->where('txn = ?', $txn);

			}

			return $this->_getReadAdapter()->fetchOne($select);

        }

    }

	

	public function getCodeByTxn($txn)

    {

         if (!empty($txn)){

            $select = $this->_getReadAdapter()->select()

            ->from($this->getMainTable(), 'code');

			if ($txn){

 				$select->where('txn = ?', $txn);

			}

			return $this->_getReadAdapter()->fetchOne($select);

        }

    }

	

	public function getStatusOnGrid($requestId,$target)

    {

         if (!empty($requestId)){

            $select = $this->_getReadAdapter()->select()

            ->from($this->getMainTable(), 'status');

			if ($requestId){

 				$select->where('request_id = ?', $requestId);

				$select->where('trg_name = ?', $target);

			}

			return $this->_getReadAdapter()->fetchOne($select);

        }

    }

	

	public function getTranslatedTextOnGrid($requestId,$target)
    {
          if (!empty($requestId)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'target');
			if ($requestId){
 				$select->where('request_id = ?', $requestId);
				$select->where('trg_name = ?', $target);
			}
			return $this->_getReadAdapter()->fetchOne($select);
        }
    }	

	public function checkFeedbackDone($feedbackTxn)
    {
          if (!empty($feedbackTxn)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'feedback');
			if ($feedbackTxn){
 				$select->where('txn = ?', $feedbackTxn);
 			}
			return $this->_getReadAdapter()->fetchOne($select);
        }
    }
	

}