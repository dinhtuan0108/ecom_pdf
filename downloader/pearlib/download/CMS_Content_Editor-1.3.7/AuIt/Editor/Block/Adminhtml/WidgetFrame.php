<?php
class AuIt_Editor_Block_Adminhtml_WidgetFrame extends Mage_Adminhtml_Block_Template
{
    public function renderView()
    {
    	Varien_Profiler::start(__METHOD__);
        $this->setScriptPath(Mage::getConfig()->getModuleDir('etc', 'AuIt_Editor'));
		$templateName = $this->getTemplate();
		$html = $this->fetchView($templateName);
        Varien_Profiler::stop(__METHOD__);
        return $html;
    }
    public function importBlock($block)
    {
    	$this->_children = $block->getChild();
    }
}