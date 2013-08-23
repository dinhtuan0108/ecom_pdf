<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php

class MLogix_Wedding_Model_Mysql4_Wedding extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the wedding refers to the key field in your database table.
        $this->_init('wedding/wedding', 'wedding_id');
    }
}