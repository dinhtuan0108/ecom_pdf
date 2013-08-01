<?php 
/**** 9|Developed By Pankaj Gupta|4 ****/
/**** 1|Class Translate Helper|3 ****/
//require_once getcwd().'/XML/myParser.php';

class Lingotip_Translate_Helper_Data extends Mage_Core_Helper_Abstract
{

	const lturl = '';

	const LTI_SERVER = 'www.lingotip.com'; // production server requires "www.lingotip.com"

	const LTI_PATH = '/lti';

	//const SSL =  false;  

	const SSL =  'ssl://'; 

	const HTTP =  'http' ;  

	const cid = 3481; // magento LTI CID

	const LTpostApi = "LTpostApi.php";

	const LTstatApi = "LTstatApi.php";

	const dueDate = 2;

	const paypalUrl = 'https://www.paypal.com/cgi-bin/webscr';

	

	public function getUserDetail() 

	{

		//error_reporting(0);

		$installModel = Mage::getModel('translate/install');

		$registerationData = $installModel->getRegisterationData($installModel); 

		$installId = $registerationData->getId(); 

		$loadInstallData = Mage::getModel('translate/install')->load($installId);

		if($loadInstallData['lturl'] == ""){

			$loadInstallData['lturl'] = 'http://www.lingotip.com/lti';

		}	

		return $loadInstallData;

	}

	

	public function installClient($parameters)

	{

		$host  		= self::LTI_SERVER;

		$path 		= self::LTI_PATH;

		$ssl   		= self::SSL;

		$HTTP  		= self::HTTP;

		$LTstatApi  = self::LTstatApi;



		$path .= "/$LTstatApi"; //apiStatXML.php";

		$result = $this->postRequest($host, $path, $parameters, $ssl); // actual request posted to LT

		$result = trim($result);

		$xmlObj = simplexml_load_string($result);

		$arrXml = $this->objectsIntoArray($xmlObj);

		return $arrXml;

		//echo '<pre>';print_r($arrXml);die;

		

		/*$p =  new myParser(); 

		$result = $p->setInputString($result); 

		$p->parse(); 

		return $p->xml_arr;*/

	}

	

	//This will be called once at the time of installation

	public function getLanguagePairFromLingoTiP() 

	 {

		$lturl 		= self::lturl;

		$host  		= self::LTI_SERVER;

		$path 		= self::LTI_PATH;

		$ssl   		= self::SSL;

		$HTTP  		= self::HTTP;

		$cid   		= 3483;

 		$LTstatApi  = self::LTstatApi;



		$userData = $this->getUserDetail(); // load user detail

 		$cid = $userData->getCid(); $usermail = $userData->getEmail();  $password = $userData->getPassword();

		$lturl = $userData->getLturl();

		if ($lturl) {

			sscanf($lturl, "http://%[^/]%s", $host, $path);

		}

 		$path .= "/$LTstatApi";

		$parameters = "cid=".$cid."&u=".$usermail."&p=".$password."&status=PAIRS";

		

		// actual request posted to LT

		$result = $this->postRequest($host, $path, $parameters, $ssl);  

		//echo '<pre>';print_r($result);die;

		$xmlObj = simplexml_load_string($result);

	

		$arrXml = $this->objectsIntoArray($xmlObj);

		return $arrXml;

		

		/*

		$p =  new myParser(); 

		$p =  new XML_Parser_Simple(); 

		//echo '<pre>';print_r($p->xml_arr);die;

		$result = $p->setInputString($result); 

		$success = $p->parse();



		if (PEAR::isError($success)) {

		  die('Parsing failed:' . $success->getMessage());

		}

 		return $p->xml_arr;*/

	}	

	

	

   

	public function objectsIntoArray($arrObjData, $arrSkipIndices = array())

	{

		$arrData = array();

	   

		// if input is object, convert into array

		if (is_object($arrObjData)) {

			$arrObjData = get_object_vars($arrObjData);

		}

 

		if (is_array($arrObjData)) {

			foreach ($arrObjData as $index => $value) {

				if (is_object($value) || is_array($value)) {

					$value = $this->objectsIntoArray($value, $arrSkipIndices); // recursive call

				}

				if (in_array($index, $arrSkipIndices)) {

					continue;

				}

				$index = strtoupper($index);

				$arrData[$index] = $value;

			}

		}

		return $arrData;

	}



	public function getXmlResult($level,$source,$t,$text,$action,$captcha='',$note='',$duedate='',$dispatch_list='',$txnId='',$paramArray='') 

	 {

 

 		$lturl 		= self::lturl;

		$host  		= self::LTI_SERVER;

		$path 		= self::LTI_PATH;

		$ssl   		= self::SSL;

		$HTTP  		= self::HTTP;

		$cid   		= self::cid;

		$LTpostApi  = self::LTpostApi;

		$LTstatApi  = self::LTstatApi;

		$duedate 	= self::dueDate;



		$userData = $this->getUserDetail(); // load user detail

		$cid = $userData->getCid(); $usermail = $userData->getEmail();  $password = $userData->getPassword();

		$lturl = $userData->getLturl();

		if ($lturl) {

			sscanf($lturl, "http://%[^/]%s", $host, $path);

		}

 

		if($action == "post")

		{

			$path .= "/$LTpostApi"; //api2postXML.php";

$parameters="cid=".$cid."&u=".$usermail."&p=".$password."&lvl=".$level."&s=".$source."&t=".$t."&text=".urlencode($text)."&mode=NEW".

			"&captcha=".$captcha."&apps=CONTENT"."&duedate=".$duedate."&note=".urlencode($note)."&dispatch_list=$dispatch_list";

		}

		else if($action == "edit")

		{

			 $path .= "/$LTstatApi"; $txn = $txnId;

			 $parameters="cid=".$cid."&u=".$usermail."&p=".$password."&lvl=".$level."&s=".$source."&t=".$t."&text=".urlencode($text)."&status=edit&txn=".$txn;

			 

		}

		

		else if($action == "feedback")

		{

			$txn = $paramArray['txn'];$note = '';$general='';$responsiveness='';$ontime='';$price='';

			if(isset($paramArray['note']) && $paramArray['note'] != ""){$note = $paramArray['note'];}

			if(isset($paramArray['general']) && $paramArray['general'] != ""){$general = $paramArray['general'];}

			if(isset($paramArray['responsiveness']) && $paramArray['responsiveness'] != ""){$responsiveness = $paramArray['responsiveness'];}

			if(isset($paramArray['ontime']) && $paramArray['ontime'] != ""){$ontime = $paramArray['ontime'];}

			if(isset($paramArray['price']) && $paramArray['price'] != ""){$price = $paramArray['price'];}

			

			$path .= "/$LTstatApi"; //apiStatXML.php";

			$note = stripslashes($note);

			$note = urlencode($note);

			$parameters="cid=$cid&u=$usermail&p=$password&status=feedback&txn=$txn&text=".$note."&general=$general".

			"&responsiveness=$responsiveness&ontime=$ontime&price=$price";

		}

		

		else if($action == "answer")

		{

			$txn = $paramArray['txn'];

			if(isset($paramArray['note']) && $paramArray['note'] != ""){$note = $paramArray['note'];}

			if(isset($paramArray['tid']) && $paramArray['tid'] != ""){$tid = $paramArray['tid'];}

			$path .= "/$LTstatApi"; //apiStatXML.php";

			$note = urlencode($note);

			$parameters="cid=$cid&u=$usermail&p=$password&status=answer&txn=$txn&text=$note&tid=$tid";

			//echo "$parameters<br>";

		}

		

		else if($action == "dispute")

		{

			$txn = $paramArray['txn'];

			if(isset($paramArray['note']) && $paramArray['note'] != ""){$note = $paramArray['note'];}

			if(isset($paramArray['mode']) && $paramArray['mode'] != ""){$mode = $paramArray['mode'];}

			$path .= "/$LTstatApi"; //apiStatXML.php";

			$note = urlencode($note);

			$parameters="cid=$cid&u=$usermail&p=$password&status=dispute&txn=$txn&text=".$note."&mode=$mode";

		}

		else

		{

			//estimate case

			$path .= "/$LTpostApi"; //api2postXML.php";

			$parameters="cid=".$cid."&u=".$usermail."&p=".$password."&lvl=".$level."&s=".$source."&t=".$t."&text=".urlencode($text)."&mode=ESTIMATE";	

		}

		

		$result = $this->postRequest($host, $path, $parameters, $ssl); 

		

		$xmlObj = simplexml_load_string($result);

		$arrXml = $this->objectsIntoArray($xmlObj);

		return $arrXml;

		

		//$p = new Lingotip_Translate_Model_Utils();		

		/*$p =  new myParser(); 

		// actual request posted to LT

		



		/*

		$p =  new XML_Parser_Simple(); 

		$result = $p->setInputString($result); 

		//echo '<pre>';print_r($result);die;

		$p->parse(); 

		return $p->xml_arr;*/

	}		

	

	

	public function xml2array($xml) {

      $arXML=array();

      $arXML['name']=trim($xml->getName());

      $arXML['value']=trim((string)$xml);

      $t=array();

      foreach($xml->attributes() as $name => $value) $t[$name]=trim($value);

      $arXML['attr']=$t;

      $t=array();

      foreach($xml->children() as $name => $xmlchild) $t[$name]=$this->xml2array($xmlchild);

      $arXML['children']=$t;

      return($arXML);

   }

   

   



	public function getLTIcall($host, $path, $parameters, $ssl, $node) 

	{

 

		$result = $this->postRequest($host, $path, $parameters, $ssl);

		$xmlObj = simplexml_load_string($result); 

		

		// $arrXml = $this->objectsIntoArray($xmlObj);

	

		

		if($node == "CAPTCHA")

		{

			$arrXml = $this->objectsIntoArray($xmlObj);

			if(isset($arrXml['RC']) && $arrXml['RC'] != "OK")

			{

				$RCError = $arrXml["RC"];

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Captcha has not rceived ".$RCError));

				$this->_redirect('*/*/');return;

			}

			else

			{

				return $arrXml['CAPTCHA'];

			}

		}

		

		else if($node == "TRANSLATE_TEXT")

		{

			$arrXml = $this->xml2array($xmlObj);

			

			$isRcOk = $arrXml['children']['rc']['value'];

			

			if(isset($isRcOk) && $isRcOk != "OK")

			{

				$RCError = $arrXml["RC"];

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Captcha has not rceived ".$RCError));

				$this->_redirect('*/*/');return;

			}

			else

			{

				

				return $arrXml['children']['translate_text']['value'];



			}

		}

		

		else{

			$arrXml = $this->objectsIntoArray($xmlObj);

		}

		

		return $arrXml;

		

		/*$p = new myParser();

		$p->setInputString($result);

		$p->parse();

 		return $p->xml_arr[$node];*/

	}

	public function postRequest($host, $path, $parameters, $ssl)

	{

 		if ($ssl)

			$socket = fsockopen($ssl.$host, 443, $errno, $errstr,5);

		else

			$socket = fsockopen($host, 80, $errno, $errstr,5);

	

		 if($errno != 0 || $errno != ""){

			

				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('install')->__("Server was temporarily unavailable please try again."));

				$this->_redirect('*/*/');return;

				

		 }

	

		if(!$socket) {

			echo "fsockopen failed\n";

			return false;

		}

	

		$req = "POST ".$path." HTTP/1.0\r\n";

		$req .= "Connection: close\r\n";

		$req .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";

		$req .= "Content-Length: ".strlen($parameters)."\r\n";

		$req .= "\r\n";

		$req .= $parameters;

		$req .= "\r\n";

		$req .= "\r\n";

	

	   fwrite($socket, $req);

		$tmp_headers = '';

		while ($str = trim(fgets($socket))) {

			$tmp_headers .= $str."\n";

		}

	

		$post_result='';

		while ((!feof($socket)) ) {

			$a= fgets($socket);

			$post_result .= $a;

		}

		fwrite($socket, "0\r\n\r\n");

		fclose($socket);

		return $post_result;

	

	}

	

	public function paypalHiddenFields($requestId,$returnUrl,$cancelUrl)

	 {

 	  $userData = $this->getUserDetail();

	  $bussinessPaypalEmail = $userData->getLtpp();

	  $requestData = Mage::getModel('translate/request')->load($requestId);

	  $txn = $requestData->getTxns();

	  $price = $requestData->getPrice();

	  $txn = eregi_replace(",", ":", $txn); // the IPN "custom" format is "txn-x:y:z,LTI,p" (where x:y:z is transaction id per each language and and p is the price.

	  $custom = "txn-$txn,LTI,$price";

	  $item = 'lti'.$txn;

 	  $http = self::HTTP;

	  $lti_path = self::LTI_PATH;

	  $lti_server = self::LTI_SERVER;

	  

	  $lturl = $userData->getLturl();

	  if ($lturl) {

		sscanf($lturl, "http://%[^/]%s", $lti_server, $lti_path);

	  }



	  $notify_url = $http."://".$lti_server.$lti_path."/lti_paypal_actions.php?action=ipn";

	  $itm_name = "Translation Fee";

	  $fields = array('business'=>$bussinessPaypalEmail,'cmd'=>'_xclick','rm'=>'2','amount'=>$price,'custom'=>$custom,'currency_code'=>'USD','target'=>'_self','no_shipping'=>'1','item_number'=>$item,'notify_url'=>$notify_url,'image_url'=>'https://www.lingotip.com/images/logo_white_w175.dig.gif','item_name'=>$itm_name,'return'=>$returnUrl,'cancel_return'=>$cancelUrl);

	  return $fields;

	}

	

	public function submit_paypal_post($requestId,$returnUrl,$cancelUrl) {

 

	echo '<html><head><META http-equiv=Content-Type content="text/html; charset=UTF-8">';



	echo "</head><body>\n";



	echo '<table dir="ltr" class="tabcenter" style="font-size:14px; font-family:Verdana; color:Black;" align="center" border="0" cellpadding="0" cellspacing="0" width="700">';

	echo '<tr><td><div dir=ltr>';

	echo '	<img src="https://www.lingotip.com/images/logo_white_w175.dig.gif" border="0" alt=""><br>';

	echo '</div></td></tr>';

	echo '<tr><td valign="top">';

	echo '	<hr><br><center>';

	echo '	<b>'._("Please wait, your order is being processed and you will be redirected to the paypal website.").'</b>';



      echo "<form style=\"MARGIN: 0px;\" method=\"post\" name=\"paypal_form\" ";

      echo "action=\"".self::paypalUrl."\">\n";

	 $fields = $this->paypalHiddenFields($requestId,$returnUrl,$cancelUrl);

      foreach ( $fields as $name => $value) {

         echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";

      }

      echo "<center><br/><br/>"._("If you are not automatically redirected to paypal within 10 seconds please click below...");

      echo "<br/><br/>\n";

      echo "<input class=\"form-button\" type=\"submit\" value=\""._("Click Here")."\"></center>\n";

	  echo '	</center>	<br><hr>';

	  echo '</td></tr>	</table><script>document.forms[\'paypal_form\'].submit();</script>';

      

      echo "</form>\n";

      echo "</body></html>\n";

    

   }	

   

   public function getTranslatedText($txn)

   {

 		$host  		= self::LTI_SERVER;

		$path 		= self::LTI_PATH;

		$ssl   		= self::SSL;

		$LTstatApi  = self::LTstatApi;

		

   		$userData = $this->getUserDetail(); // load user detail

		$cid = $userData->getCid(); $usermail = $userData->getEmail();  $password = $userData->getPassword();

		$lturl = $userData->getLturl();

		if ($lturl) {

			sscanf($lturl, "http://%[^/]%s", $host, $path);

		}

		$path .= "/$LTstatApi"; //apiStatXML.php";



		$rand = Mage::getResourceModel('translate/txn')->getCodeByTxn($txn);

		$parameters="cid=$cid&u=$usermail&p=$password&status=translation&txn=$txn&rand=$rand";

		return $trans_result = $this->getLTIcall($host, $path, $parameters, $ssl, "TRANSLATE_TEXT");

   }  

   

   public function getTargetHtml($selectSource,$targtLanguageinDB)

    {

		$commaSeperatedString = Mage::getResourceModel('translate/languagepair')->getTargetLanguages($selectSource);

	 	$commaSeperatedArray = explode(",",$commaSeperatedString);

		$targtLanguageinDBArray = explode(",",$targtLanguageinDB);

		 $html = '';$disabled = 'disabled = disabled' ; 

		

		$html .= '<select ' .$disabled . ' multiple="multiple" class=" required-entry select multiselect" size="10" name="language[]" id="trg_names">';	

		foreach($commaSeperatedArray as $targetLanguage){

			if(in_array($targetLanguage,$targtLanguageinDBArray)){$selected = "selected = selected"; 

				$html .= '<option class="show_un" ' .$selected .' value="'.$targetLanguage.'">'.$targetLanguage.'</option>';

			}else

			{

				$html .= '<option value="'.$targetLanguage.'">'.$targetLanguage.'</option>';	

			}

		}	

		$html .= '</select>';

		return $html;

     }



	public function getSourceHtml($source)

	 {

		$sourceLanguagesArray = Mage::getResourceModel('translate/languagepair')->getSourceLanguages();

		$htmlSource = ''; $disabled = 'disabled = disabled' ; 

		$htmlSource .= '<select '.$disabled . ' class="required-entry select" name="src_name" id="src_name">';

		foreach($sourceLanguagesArray as $sourceLanguage)

		{

			if($sourceLanguage == $source){$selected = "selected = selected";}else{$selected = '';}

			$htmlSource .= '<option '.$selected.' value="'.$sourceLanguage.'">'.$sourceLanguage.'</option>'; 

		}	

		$htmlSource .= '</select>';

		return $htmlSource;

 	 }	 

   

   public function getGridLabel()

   {

   	 return 'My Translations';

   }

   

     public function getLingoTipLeftIcon()

    {

        return '<img src="'.Mage::getDesign()->getSkinUrl('images/lingotip/lingotip.png').'" alt="'.$this->__('LingoTip').'" title="'.$this->__('LingoTip').'" class=""/>';

    }

}