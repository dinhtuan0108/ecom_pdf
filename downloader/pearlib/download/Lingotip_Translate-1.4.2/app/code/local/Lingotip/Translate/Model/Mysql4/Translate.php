<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Mysql4_Translate extends Mage_Core_Model_Mysql4_Abstract
{
	const LEVEL_Fluent    = 2;
    const LEVEL_Advanced   = 3;
	const LEVEL_Professional   = 4;
	
    public function _construct()
    {    
        // Note that the translate_id refers to the key field in your database table.
        $this->_init('translate/translate', 'translate_id');
    }
	
	public function deleteAll()
    {
			$translateIds = Mage::getResourceModel('translate/translate_collection')->getAllIds();
			foreach ($translateIds as $translateId) {
                    $translate = Mage::getModel('translate/translate')->load($translateId);
                    $translate->delete();
                }
 
    }
	
	   /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::LEVEL_Fluent    => Mage::helper('translate')->__('Fluent'),
            self::LEVEL_Advanced   => Mage::helper('translate')->__('Advanced/Native'),
			self::LEVEL_Professional   => Mage::helper('translate')->__('Professional')
        );
    }
	
}