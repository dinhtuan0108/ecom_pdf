<?php
class AuIt_Widget_Block_Youtube_Abstract
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
	protected static $sent=false;
 	public function addDefaults()
 	{
		if ( self::$sent)
			return '';
		self::$sent=true;
 		return <<<EndHTML
  <script src="http://www.google.com/uds/api?file=uds.js&v=1.0&source=uds-vbw"    type="text/javascript"></script>
  <style type="text/css">
    @import url("http://www.google.com/uds/css/gsearch.css");
  </style>
  <script type="text/javascript">
    window._uds_vbw_donotrepair = true;
  </script>
 <style type="text/css">
    .playerInnerBox_gsvb .player_gsvb {
      width : 320px;
      height : 260px;
    }
  </style>  
  <script src="http://www.google.com/uds/solutions/videobar/gsvideobar.js?mode=new"    type="text/javascript"></script>
  <style type="text/css">
    @import url("http://www.google.com/uds/solutions/videobar/gsvideobar.css");
  </style>
EndHTML;
  
 	}
} 