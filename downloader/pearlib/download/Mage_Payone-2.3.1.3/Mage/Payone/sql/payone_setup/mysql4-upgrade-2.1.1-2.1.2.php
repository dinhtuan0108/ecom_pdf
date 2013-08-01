<?php
$attributes = array(
    'customer_address_payone_credit_rating_score' => array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'PAYONE credit rating score',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '200',

        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 200,
        'is_required'       => 0,
        'adminhtml_only'    => 1
    ),
    'customer_address_payone_credit_rating_date' => array(
        'type' => 'datetime',
        'input' => 'text',
        'label' => 'PAYONE credit rating request date',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '200',

        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 200,
        'is_required'       => 0,
        'adminhtml_only'    => 1
    ),
    'customer_address_payone_credit_rating_secscore' => array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'PAYONE credit rating secure score',
        'global' => 1,
        'required' => 0,
        'default' => '',
        'position' => '200',

        'is_user_defined'   => 0,
        'is_system'         => 1,
        'is_visible'        => 1,
        'sort_order'        => 200,
        'is_required'       => 0,
        'adminhtml_only'    => 1
    )
);

$installer = $this;
$installer->startSetup();

$setup = new Mage_Customer_Model_Entity_Setup('core_setup');
$eavConfig = Mage::getSingleton('eav/config');

foreach($attributes as $attr_code => $attr_data) {
    $setup->removeAttribute('customer_address', $attr_code);
}
$setup = new Mage_Customer_Model_Entity_Setup('core_setup');
foreach($attributes as $attr_code => $attr_data) {
    $setup->addAttribute('customer_address', $attr_code, $attr_data);
    $attribute = $eavConfig->getAttribute('customer_address', $attr_code);
    $attribute->addData($attr_data);
    $attribute->setData('used_in_forms', array('adminhtml_customer_address'));
    $attribute->save();
}
$installer->endSetup();

// EOF

