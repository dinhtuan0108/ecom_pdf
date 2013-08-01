<?php
$behost="localhost"; $beuser="padmadef_rein_r"; $bepass="test"; $bedb='padmadef_shop';
if( !($dbLink = mysql_connect($behost,$beuser,$bepass))) { echo "No Conection to the Database"; exit; }
$brec = mysql_db_query($bedb, "SELECT * FROM customer_cal where entity_id='".$_GET['fid']."'");
$b_row = mysql_fetch_array($brec);
?>

<events>
	<event>
		<date><?php echo date ('d-m-Y', strtotime($b_row['be_date1']));?></date>
		<time><?php echo $b_row['be_time1'];?></time>
		<title><?php echo $b_row['be_title1'];?></title>
		<image><?php echo $b_row['be_image1'];?></image>
		<description><![CDATA[<font size="18"><?php echo $b_row['be_text1'];?></font>]]></description>
		<link><?php echo $b_row['be_link1'];?></link>
    </event>
    <event>
		<date><?php echo date ('d-m-Y', strtotime($b_row['be_date2']));?></date>
		<time><?php echo $b_row['be_time2'];?></time>
		<title><?php echo $b_row['be_title2'];?></title>
		<image><?php echo $b_row['be_image2'];?></image>
		<description><![CDATA[<font size="18"><?php echo $b_row['be_text2'];?></font>]]></description>
		<link><?php echo $b_row['be_link2'];?></link>
    </event>
    <event>
		<date><?php echo date ('d-m-Y', strtotime($b_row['be_date3']));?></date>
		<time><?php echo $b_row['be_time3'];?></time>
		<title><?php echo $b_row['be_title3'];?></title>
		<image><?php echo $b_row['be_image3'];?></image>
		<description><![CDATA[<font size="18"><?php echo $b_row['be_text3'];?></font>]]></description>
		<link><?php echo $b_row['be_link3'];?></link>
    </event>
</events>

