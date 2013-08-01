<?php
/**** Developed By Pankaj Gupta ****/
class Lingotip_Translate_Model_Languagepair extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('translate/languagepair');
    }
 
	function deleteAllLangauge()
	  {
		$allids = Mage::getResourceModel('translate/languagepair_collection')->getAllIds();
		if(is_array($allids))
		{
		  foreach ($allids as $key => $languageId)
		  {
			try {
			  $langauge = Mage::getSingleton('translate/languagepair')->load($languageId);
			  Mage::dispatchEvent('install_controller_langauge_delete', array('langauge' => $langauge));
			  $langauge->delete();          
			} catch (Exception $e) {
			 Mage::getSingleton('adminhtml/session')->addError("Can't delete langauge w/ id: ".$languageId);
			}
		  } 
		}
	  } 
}