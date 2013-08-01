<?php

$attribute_code_score = 'customer_payone_credit_rating_score';
$attribute_code_date = 'customer_payone_credit_rating_date';
$attribute_code_secscore = 'customer_payone_credit_rating_secscore';

// load id for customer entity
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$eid = $read->fetchRow("select entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code = 'customer'");
$customer_type_id = $eid['entity_type_id'];

$attr_date = array(
	'type' => 'datetime',
	'input' => 'label',
	'label' => 'Payone credit rating request date',
	'global' => 1,
	'required' => 0,
	'default' => '',
    'position' => '100'
);

$attr_score = array(
	'type' => 'varchar',
	'input' => 'label',
	'label' => 'Payone credit rating score',
	'global' => 1,
	'required' => 0,
	'default' => '',
    'position' => '100'
);


$attr_secscore = array(
	'type' => 'varchar',
	'input' => 'label',
	'label' => 'Payone credit rating secure score',
	'global' => 1,
	'required' => 0,
	'default' => '',
    'position' => '100'
);

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute($customer_type_id, $attribute_code_date, $attr_date);
$setup->addAttribute($customer_type_id, $attribute_code_score, $attr_score);
$setup->addAttribute($customer_type_id, $attribute_code_secscore, $attr_secscore);

$installer->endSetup();

// EOF

