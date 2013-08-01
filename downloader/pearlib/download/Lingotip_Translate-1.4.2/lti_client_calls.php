<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    <link href="lti.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

/*
 LTI sample lti_client_calls.php verions 2.01
 LTI client side when calls are initiated from LT
 Including: LT3, LT4, LT9, LT15
*/
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
Mage::app();
//require_once('lti_client_config.php');
//require_once('lti_utils.php');

if (isset($_POST['status'])) $status = $_POST['status'];
if (isset($_POST['id'])) $id = $_POST['id'];
if (isset($_POST['text'])) $text = $_POST['text'];
if (isset($_POST['txn'])) $txn = $_POST['txn'];
if (isset($_POST['txns'])) $txns = $_POST['txns'];

/*include('connection.inc');
 $status = 'ipn';$id = 4;
		$sql = "UPDATE lt_requests SET paid='yes' WHERE txns='$txns' || request_id='$id'";
		$query = mysql_query($sql);
							
die;*/ 
$rc = "OK";
 
$write = Mage::getSingleton('core/resource')->getConnection('core_write');

switch ($status) {
case 'ipn': // LT15 IPM stage
	// after payment IPN is handled on LT side and send this event to update the paid status on the client side.
 
	
	$write->query("UPDATE lt_requests SET paid='yes' WHERE txns='$txns' || rid='$id'" );
	$rc = "$txns is OK";
	break;
case 'question': // LT4 - message from LT to client

	$result = Mage::getResourceModel('translate/txn')->getTxnByTxn($txn); 
	
	if (isset($_POST['tid'])) $tid = $_POST['tid'];
	if (!$tid)
		$rc = "translator ID is missing";
	elseif (!$result)
		$rc = "Txn does not exist on client side";
	else {

 
		$write->query("UPDATE lt_txn SET status='active' WHERE txn=$txn && status='pending'" );
 	
		
		$write->query("INSERT INTO lt_notes (txn,note,incoming,tid) VALUES ($txn,'".addslashes($text)."','1', '$tid')" );
	}
	break;
case 'accept': // LT3 - Translation status change updates from LT - accept translation and complete (done) translation
	 $write->query("UPDATE lt_txn SET status='accept' WHERE txn=$txn" );
	  
	if (isset($_POST['tid'])) $tid = $_POST['tid'];
	
		$insertSql = "INSERT INTO lt_notes (txn,note,incoming,tid) VALUES ($txn,'Translator accepted translation.','1', '$tid')";
		$write->query($insertSql);

	 break;
	
case 'pending':
	$write->query("UPDATE lt_txn SET status='active' WHERE txn=$txn" );
	break;
	
case 'done':
	$write->query("UPDATE lt_txn SET status='$status' WHERE txn=$txn" );
	break;

// LT9 - LT language coordinator is providing feedback on the dispute.
case 'approved': // LT agrees that there is a problem with the translation and it will "alternate" or "refund" based on original dispute.
	$note = addslashes($text);
	$write->query("UPDATE lt_dispute SET stat='$status',note='$note' WHERE txn=$txn && stat='pending'" );
	break;
case 'denied': // LT thinks the translation is good.
	
	$note = addslashes($text);
	$write->query("UPDATE lt_dispute SET stat='$status',note='$note' WHERE txn=$txn && stat='pending'" );
	break;
case 'reexamine': // A message is sent to the customer and the translator, and the translator will fix and communicate the fix to the translator.
	$note = addslashes($text);
	$write->query("UPDATE lt_dispute SET stat='$status',note='$note' WHERE txn=$txn && stat='pending'" );
	break;

default:
	break;
}

echo "<?xml version='1.0' encoding='UTF-8'?>
<lti>
  <ltpp>$txn</ltpp> 
  <rc>$rc</rc> 
</lti>";


?>
<a href="index.php">back</a>
</body>
</html>