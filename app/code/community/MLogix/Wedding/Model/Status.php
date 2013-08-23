<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php

class MLogix_Wedding_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('wedding')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('wedding')->__('Disabled')
        );
    }
}