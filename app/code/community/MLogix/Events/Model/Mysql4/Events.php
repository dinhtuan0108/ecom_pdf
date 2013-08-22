<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php

class MLogix_Events_Model_Mysql4_Events extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the events refers to the key field in your database table.
        $this->_init('events/events', 'events_id');
    }
}