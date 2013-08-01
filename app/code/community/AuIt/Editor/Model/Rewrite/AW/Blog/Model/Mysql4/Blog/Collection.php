<?php
class AuIt_Editor_Model_Rewrite_AW_Blog_Model_Mysql4_Blog_Collection extends AW_Blog_Model_Mysql4_Blog_Collection
{
	public function addEnableFilter($status)
    {
    	if ( Mage::helper('auit_editor')->getIsInInlineMode() )
    	{
        	$this->getSelect()->where('status = 3 or status = ?', $status);
    	}
    	else {
        	$this->getSelect()->where('status = ?', $status);
    	}
        //$this->getSelect()->orWhere('status = 3');
        //->where($this->_getConditionSql('status',$status));
        return $this;
    }
}
