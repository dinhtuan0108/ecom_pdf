<?php
class AuIt_Editor_Model_Adminhtml_Arraytemplate extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    protected function _getAllowedExtensions()
    {
        return array('jpg', 'jpeg', 'gif', 'png');
    }
	protected function _afterLoad()
    {
    	$v = $this->getValue();
		if ( !is_array($v) && !is_string($v) )
			$v='';
    	if ( empty($v) )
    	{
    		$v = Mage::helper('auit_editor/config')->getDefaults($this->getPath());
    	}
    	if (!is_array($v)) 
    	{
    		if ( strpos($v,'base64:') === 0 )
    		{
    			$v = base64_decode(substr($v,7));
    		}
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
		$uploadDir = Mage::helper('auit_editor/skinmedia')->getPreviewRoot();
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        if (is_array($value)) {
	        foreach ( $value as $uniqid => $row )
	        {
	        	if ( isset($_FILES['XFILEgroups']) && isset($_FILES['XFILEgroups']['tmp_name'][$this->getGroupId()]) ) 
	        	{
		            try {
		                $file = array();
		                $file['tmp_name'] = $_FILES['XFILEgroups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'][$uniqid]['image'];
		                $file['name'] = $_FILES['XFILEgroups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value'][$uniqid]['image'];
						if ( $file['tmp_name'] && $file['name'] )
						{  
			                $uploader = new Varien_File_Uploader($file);
			                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
			                $uploader->setAllowRenameFiles(true);
		               		$uploader->save($uploadDir);
				            if ($filename = $uploader->getUploadedFileName()) {
				            	Mage::helper('auit_editor/dirdirective')->execute($uploadDir.DS.$filename);
								$value[$uniqid]['image']=$filename;
				            }
						}
		            } catch (Exception $e) {
		                Mage::throwException($e->getMessage());
		                return $this;
		            }
		
	        	}
	        }
	        $this->setValue('base64:'.base64_encode(serialize($value))); 
        }
       	//parent::_beforeSave();
    }
}
