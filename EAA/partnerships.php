<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>EURO ARAB ADVISORY - Partnerships</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="Scripts/swfobject_modified.js" type="text/javascript"></script>
</head>
<?php
if(isset($_POST['mail'])){
$Name = "Website Info"; //senders name
$email = "dce@biocitydevelopment.com"; //senders e-mail adress
$mail_body = "Please send me more information ".$_POST['mail']."."; //mail body
$subject = "Info from the website"; //subject
$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields

ini_set('sendmail_from', 'dce@biocitydevelopment.com'); //Suggested by "Some Guy"

mail($subject, $mail_body, $header); //mail command :)
}
?>
<body>
<div id="web">
<div id="top">
<div id="nav">
<ul>
<li><a href="index.php">About Us</a></li>
<li><img src="img/nav_break.gif" width="1" height="15" /></li>
<li><a href="company.php">Company Activities</a></li>
<li><img src="img/nav_break.gif" width="1" height="15" /></li>
<li><a href="why.php">Why Middle East &amp; Africa?</a></li>
<li><img src="img/nav_break.gif" width="1" height="15" /></li>
<li><a href="GCC.php">Why GCC?</a></li>
<li><img src="img/nav_break.gif" width="1" height="15" /></li>
<li><a href="asia.php">Why South Asia?</a></li>
<li><img src="img/nav_break.gif" width="1" height="15" /></li>
<li><a href="partnerships.php">Partnerships</a></li>
</ul>
</div>
</div>

<div class="img">
  <object id="FlashID" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="980" height="200">
    <param name="movie" value="flash/mov5.swf" />
    <param name="quality" value="high" />
    <param name="wmode" value="opaque" />
    <param name="swfversion" value="6.0.65.0" />
    <!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don’t want users to see the prompt. -->
    <param name="expressinstall" value="Scripts/expressInstall.swf" />
    <!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
    <!--[if !IE]>-->
    <object type="application/x-shockwave-flash" data="flash/mov5.swf" width="980" height="200">
      <!--<![endif]-->
      <param name="quality" value="high" />
      <param name="wmode" value="opaque" />
      <param name="swfversion" value="6.0.65.0" />
      <param name="expressinstall" value="Scripts/expressInstall.swf" />
      <!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
      <div>
        <h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
        <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" width="112" height="33" /></a></p>
      </div>
      <!--[if !IE]>-->
    </object>
    <!--<![endif]-->
  </object>
</div>
<div id="con">
<div id="img"><img src="img/sub_5.jpg" width="218" height="231" /></div>
<div id="tex">
<h1>KEY PARTNERSHIPS</h1>
<br />
Key partners of EAA include leading organizations in each of the major infrastructure design and engineering fields including construction, architecture and engineering, telecom and IT, hospital development, public infrastructure development, commercial and residential property development, transportation, and pharmaceutical, biotechnology and medical technology. 
<br /><br />
The combination of these partners allows EAA to customize infrastructure design packages and thereby to offer a full range of services to government development agencies and other clients.

</div>
<div id="search">
<div id="box"><h1>Contact Us</h1><br />
For further information on EAA, 
please use the following contact 
information:<br /><br />
<form action="index.php" method="post">
<ul><li><input name="mail" type="text" class="field" value="Email" /></li><li><input name="send" type="submit" value="send" class="sub" /></li></ul>

</form>
</div>
</div>

<div id="ie_clearing">&nbsp;</div>

</div>
<div id="footer">All rights reserved - copyright 2011 - EURO ARAB ADIVSORY</div>
<div id="bot"></div>
</div>
<script type="text/javascript">
swfobject.registerObject("FlashID");
</script>
</body>
</html>
