<?php
/**** Developed By Pankaj Gupta ****/
//require_once getcwd().'/XML/Parser/Simple.php';
class Lingotip_Translate_Model_Utils extends XML_Parser_Simple
{
	var $xml_arr = array();

	public function myParser()
	{
		$this->XML_Parser_Simple();
	}
	public function handleElement($name, $attribs, $data)
	{
		if ($this->getCurrentDepth()==1)
		$this->xml_arr[$name] = $data;
	}
}