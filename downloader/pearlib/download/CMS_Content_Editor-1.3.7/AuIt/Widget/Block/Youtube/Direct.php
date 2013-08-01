<?php
class AuIt_Widget_Block_Youtube_Direct
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
		
		$url = "http://www.youtube.com/v/$videoid?video_id=$videoid&version=3&enablejsapi=1&playerapiid=ytplayer";
		
    	$html = <<<EndHTML
	<object data="$url" type="application/x-shockwave-flash" width="$w" height="$h">
	  <param name="movie" value="$url"/>
	  <param name="allowfullscreen" value="true"/>
	  <param name="quality" value="high"/>
	  <param name="wmode" value="opaque"/>
	  
	</object>
EndHTML;
    	return $html;

    }
} 