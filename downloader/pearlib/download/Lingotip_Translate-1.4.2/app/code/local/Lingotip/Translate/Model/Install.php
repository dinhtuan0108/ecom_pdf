<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Install extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('translate/install');
    }
	
	public function getRegisterationData($model)
    {
 		$this->_getResource()->getRegisterationData($this); 
        return $this;
    }
	
}