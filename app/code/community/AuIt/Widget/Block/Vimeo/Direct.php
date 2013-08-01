<?php
class AuIt_Widget_Block_Vimeo_Direct
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
		
		$show_title = trim($this->getData('show_title'));
		$show_byline = trim($this->getData('show_byline'));
		$show_portrait = trim($this->getData('show_portrait'));
		$color = trim($this->getData('color'));
		
		$url = "http://vimeo.com/moogaloop.swf?clip_id=$videoid&amp;server=vimeo.com&amp;show_title=$show_title&amp;show_byline=$show_byline&amp;show_portrait=$show_portrait&amp;color=$color&amp;fullscreen=1";
    	$html = <<<EndHTML
<object width="$w" height="$h">
<param name="allowfullscreen" value="true" />
<param name="allowscriptaccess" value="always" />
<param name="movie" value="$url" />
<embed src="$url" 
type="application/x-shockwave-flash" 
allowfullscreen="true" 
allowscriptaccess="always" width="$w" height="$h"></embed>
</object>
	
EndHTML;
    	return $html;

    }
} 
