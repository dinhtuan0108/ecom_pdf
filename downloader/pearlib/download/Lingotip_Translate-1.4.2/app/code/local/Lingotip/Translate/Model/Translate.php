<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Translate extends Mage_Core_Model_Abstract
{
	const SOURCE_ENGLISH = 'English';
    const SOURCE_CHINESE = 'Chinese-Simplified';
	const SOURCE_CHINESE_TRANDTIONAL = 'Chinese-Traditional';

    public function _construct()
    {
        parent::_construct();
        $this->_init('translate/translate');
    }

 
 

}