<?php
class AuIt_Editor_Model_Adminhtml_Array extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    protected function _afterLoad()
    {
    	$v = trim($this->getValue());
    	if ( empty($v) )
    	{
    		$v = Mage::helper('auit_editor/config')->getDefaults($this->getPath());
    	}
    	if (!is_array($v)) {
    		$v=@unserialize($v);
        }
        $this->setValue($v);
    }
	
    /**
     * Unset array element with '__empty' key
     *
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        parent::_beforeSave();
    }
}
