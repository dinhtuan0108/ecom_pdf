<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_All
 * @copyright  Copyright (c) 2003-2009 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */ 


class TBT_Enhancedgrid_Block_System_Html
	extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		
		$html = "";
        $html .= "<div style=\"border: medium dotted ; padding: 6px; font-weight: bold; margin-bottom: 12px; width: 430px;\">".
                 "This extension was provided by WDCA. <a href='http://wdca.ca/solutions_page_magento_ext.html' target='_blank'>Click here</a> for more from WDCA.</a><BR /> ".
                 "Try our new <a href=\"http://www.GetSweetTooth.com\" target=\"_blank\">Sweet Tooth Points & Rewards</a> Magento Extension.</div>";
        $html .= "";//$this->_getFooterHtml($element);

        return $html;
    }

    protected function _getDummyElement()
    {
    	if (empty($this->_dummyElement)) {
    		$this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>1));
    	}
    	return $this->_dummyElement;
    }

    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }

	protected function _getFieldHtml($fieldset, $moduleName)
    {
		$configData = $this->getConfigData();
    	$path = 'advanced/modules_disable_output/'.$moduleName; //TODO: move as property of form
    	$data = isset($configData[$path]) ? $configData[$path] : array();

    	$e = $this->_getDummyElement();

		$moduleKey = substr($moduleName, strpos($moduleName,'_')+1);
		$ver = (Mage::getConfig()->getModuleConfig($moduleName)->version);

	
		if($ver){
			$field = $fieldset->addField($moduleName, 'label',
				array(
					'name'          => 'ssssss',
					'label'         => $moduleName,
					'value'         => $ver
				))->setRenderer($this->_getFieldRenderer());
			return $field->toHtml();
		}
		return '';
		
    }
}
