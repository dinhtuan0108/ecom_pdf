<?php
/**** Developed By Pankaj Gupta ****/

class Lingotip_Translate_Block_Translate extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTranslate()     
     { 
        if (!$this->hasData('translate')) {
            $this->setData('translate', Mage::registry('translate'));
        }
        return $this->getData('translate');
        
    }
}