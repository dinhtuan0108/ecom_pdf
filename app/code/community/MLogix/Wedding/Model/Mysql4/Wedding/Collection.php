<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php

class MLogix_Wedding_Model_Mysql4_Wedding_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('wedding/wedding');
    }
}