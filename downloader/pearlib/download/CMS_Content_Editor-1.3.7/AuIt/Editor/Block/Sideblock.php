<?php
class AuIt_Editor_Block_Sideblock extends Mage_Core_Block_Template
{
	protected $_block;
    public function isActive()
    {
    	$this->loadBlock();
		if ( $this->_block->getId() && Mage::helper('auit_editor')->getIsInInlineMode() )
			return true;    	
		return $this->_block->getIsActive();
    }
    public function getContent()
    {
		if (!$this->isActive()) {
        	return '';
		}
    	return Mage::Helper('auit_editor')->translateDirective($this->_block->getContent());
    }
    public function getTitle()
    {
		if (!$this->isActive()) {
        	return '';
		}
    	return $this->_block->getTitle();
    }
    public function getBoxClass()
    {
    	$blockData = $this->getBlockData();
    	$blockClass = '';
    	if ( isset($blockData['blockclass']) )
    		$blockClass = $blockData['blockclass'];
    	return $blockClass;
    }
    public function getVersion()
    {
        return Mage::getVersion();
    }
    protected function _toHtml()
    {
		if (!$this->isActive()) {
        	return '';
		}
    	$blockData = $this->getBlockData();
		$template = '';
		
		if ( isset($blockData['template']) )
			$template = $blockData['template'];
		if (  $template )
		{
			$this->setTemplate($template);
			return parent::_toHtml();
		}
    	$title = $this->getTitle();
    	$blockClass = '';
    	if ( isset($blockData['blockclass']) )
    		$blockClass = $blockData['blockclass'];
    	$content = $this->getContent();
    	$html = <<<EndHTML
<div class="$blockClass">
    <div class="block-content">$content</div>
</div>  	
EndHTML;
    	return $html;
    }
    protected function loadBlock()
    {
    	if (!$this->_block )
    	{
    		$blockData = $this->getBlockData();
    		$blockId = '';
    		if ( isset($blockData['classname']) )
    			$blockId=$blockData['classname'];
	        if ($blockId ) 
	        {
	            $this->_block = Mage::getModel('cms/block')
	                ->setStoreId(Mage::app()->getStore()->getId())
	                ->load($blockId);
				if ( !$this->_block->getId() && Mage::helper('auit_editor')->getIsInInlineMode() )	 
				{
		            $this->_block = Mage::getModel('cms/block')->load($blockId);
		            if ( $this->_block->getId() )
		            {
		            	$stores = array_flip($this->_block->getStoreId());
		            	if ( !isset($stores[0]) && !isset($stores[Mage::app()->getStore()->getId()]))
		            	{
		            		$this->_block = Mage::getModel('cms/block');
		            	}
		            }
				}               
	        }
    	}	
    	if (!$this->_block )
    	{
    		$this->_block = Mage::getModel('cms/block');
    	}
    }
}
