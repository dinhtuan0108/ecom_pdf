<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Mysql4_Languagepair extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the translate_id refers to the key field in your database table.
        $this->_init('translate/languagepair', 'id');
    }
	
	public function getTargetLanguages($selectSource)
    {
         if (!empty($selectSource)){
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'targetlan');
			if ($selectSource){
 				$select->where('source = ?', $selectSource);
			}
			return $this->_getReadAdapter()->fetchOne($select);
        }
    }

	public function getSourceLanguages()
    {
        $result = array();
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'source');
			$twoDimensionArray = $this->_getReadAdapter()->fetchAll($select);
			foreach($twoDimensionArray as $oneDimension=>$val){
					$result[]=$val['source'];
			}
			return $result;
    }
	
	public function getLanguagePair()
    {
        $result = array();
            $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), '*')->order('source', 'desc');
 
 		 
		 
			
			$twoDimensionArray = $this->_getReadAdapter()->fetchAll($select); 
			//echo '<pre>';print_r($twoDimensionArray);die;
			foreach($twoDimensionArray as $oneDimension=>$val){
					$result[strtoupper($val['source'])]=$val['source'].":".$val['targetlan'];
			}
			return $result;
    }
}