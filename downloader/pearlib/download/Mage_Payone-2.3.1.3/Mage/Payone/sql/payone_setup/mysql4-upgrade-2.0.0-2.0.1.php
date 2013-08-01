<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Sales_Model_Mysql4_Setup('sales_setup');
$setup->addAttribute('order_payment', 'sequence_number', array('type'=>'int'));

$installer->endSetup();

// EOF

