<?php
class AuIt_Widget_Block_Viddler_Direct
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    protected function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    protected function _toHtml()
    {
		$videoid = trim($this->getData('videoid'));
		$w = (int)trim($this->getData('width'));
		$h = (int)trim($this->getData('height'));
		$url = "http://www.viddler.com/player/$videoid/";
    	$html = <<<EndHTML
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$w" height="$h">
<param name="movie" value="$url" />
<param name="allowFullScreen" value="true" />
<embed src="$url" width="$w" height="$h" type="application/x-shockwave-flash" allowFullScreen="true"></embed>
</object>
EndHTML;
    	return $html;

    }
} 
