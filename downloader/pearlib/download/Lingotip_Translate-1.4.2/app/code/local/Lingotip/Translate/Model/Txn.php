<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Txn extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('translate/txn');
    }
}