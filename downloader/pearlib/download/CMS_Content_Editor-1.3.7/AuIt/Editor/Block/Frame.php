<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Frame extends Mage_Adminhtml_Block_Template
{
    protected function _toHtml()
    {
		if (!$this->_beforeToHtml()) {
			return '';
		}
		Mage::helper('auit_editor')->setInlineEdit(1);
		$version = Mage::app()->getConfig()->getModuleConfig('AuIt_Editor')->version;
		$config = new Varien_Object();
		$config->setAppUrl($this->getUrl('*/*/app'))->setVersion($version);
		$html='
<script type="text/javascript">
	var AUIT_LOADER= '.Zend_Json::encode($config->getData()).';
</script>
		<div id="AUIT_SPLASH"><div id="AUIT_SPLASHI"><div class="l">loading, please wait...</div><div class="r">Version: '.$version.'<br/><br/><br/><br/>License: '.Mage::getStoreConfig('auit_editor/editor/license').'</div></div></div>';
        return $html;
    }
}
