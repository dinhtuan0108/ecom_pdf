<?php

class Lingotip_Translate_Model_Mysql4_Install_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('translate/install');
    }
}