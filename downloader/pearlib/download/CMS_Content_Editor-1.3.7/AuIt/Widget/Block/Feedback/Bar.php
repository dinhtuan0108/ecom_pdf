<?php
class AuIt_Widget_Block_Feedback_Bar
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    protected function _construct()
    {
        parent::_construct();
//        $this->setTemplate('auit/widgets/feedbar.phtml');
    }
    protected function _toHtml()
    {
    	$jsUrl = Mage::getBaseUrl('js');
    	$html = <<<EndHTML
<script src="{$jsUrl}auit/widgets/feedbackbar.js" type="text/javascript"></script>
EndHTML;
		return $html;
    }
} 

