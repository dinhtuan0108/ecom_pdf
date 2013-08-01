<?php
class AuIt_Widget_Block_Youtube_Videobar
    extends AuIt_Widget_Block_Youtube_Abstract
{
    protected function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    protected function _toHtml()
    {
    	$barId = 'videoBar-'.uniqid(); 
		$orientation = trim($this->getData('orientation'));
		$largeresultset= trim($this->getData('largeresultset'));
		
		
		$ytchannel = trim($this->getData('ytchannel'));
		$ytfeed = trim($this->getData('ytfeed'));
		$search = trim($this->getData('search'));
		
		$list ='';
		$x = explode(',',$search);
		foreach ( $x as $item )
		{
			if ( $item ){
				if ( $list ) $list .=',';
 				$list .= '"'.str_replace('"','\"',$item).'"';
			}
		}
		$x = explode(',',$ytchannel);
		foreach ( $x as $item )
		{
			if ( $item ) {
				if ( $list ) $list .=',';
				$list .= '"ytchannel:'.str_replace('"','\"',$item).'"';
			}
		}
		$x = explode(',',$ytfeed);
		foreach ( $x as $item )
		{
			if ( $item ){
				if ( $list ) $list .=',';
				$list .= '"ytfeed:'.str_replace('"','\"',$item).'"';
			}
		}
		if ( !$list )
			$list='"Magento"';
    	$html = <<<EndHTML
  <div id="$barId">
    <span style="color:#676767;font-size:11px;margin:10px;padding:4px;">Loading...</span>
  </div>
EndHTML;
		$html .= $this->addDefaults();
		$html .= <<<EndHTML
<script type="text/javascript">
    GSearch.setOnLoadCallback(
    	new GSvideoBar(	document.getElementById("$barId"),
        				GSvideoBar.PLAYER_ROOT_FLOATING,
                        {
					        largeResultSet : $largeresultset,
					        horizontal : $orientation,
					        autoExecuteList : {
					          cycleTime : GSvideoBar.CYCLE_TIME_MEDIUM,
					          cycleMode : GSvideoBar.CYCLE_MODE_LINEAR,
					          executeList : [$list]
					        }
						}));
</script>
EndHTML;
    	return $html;
    }
} 
