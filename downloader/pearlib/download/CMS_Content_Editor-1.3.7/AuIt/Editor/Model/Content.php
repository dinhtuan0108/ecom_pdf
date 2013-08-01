<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2008 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Model_Content
{
	protected $_models=array();
	public function importContent(&$rData,$data,$Request)
	{
		Mage::getDesign()->setArea('frontend');
		
		for ( $step=0; $step < 2; $step++ )
		{
			$this->_models=array();
			// STEP 0 ->  Default store 
			// STEP 1 -> Working store
			foreach ( $data as $d)
			{
				if ( !is_array($d) )
					continue;
				$handler = 'import'.$d['factory'].$d['typ'];
				$this->storeId = 0;
				if ( isset($d['storeId']) ) 	
					$this->storeId = $d['storeId'];
				
				if ( !method_exists($this,$handler ) || !($d['page_id']>0) )
					Mage::throwException(Mage::helper('auit_editor')->__('AuIt-Editor: Handler not found : %s',$handler));
				$this->$handler($step,$d,$rData);
			}
			foreach ( $this->_models as $key => $model )
			{
				$mcls = explode('#',$key);
				switch ($mcls[0])
				{
					case 'cms/page':
						//$result['data']['stores']=$page->getStoreId();
						if ( !$model->getData('stores') ) 
							$model->setData('stores',$model->getStoreId()); 
						Mage::dispatchEvent('cms_page_prepare_save', array('page' => $model, 'request' => $Request));
						break;
					case 'cms/block':
						if ( !$model->getData('stores') ) 
							$model->setData('stores',$model->getStoreId()); 
						break;
					case 'blog/post':
						$this->beforeSaveBlog($model);
						break;
					default:
						break;
				} 
				
	           	$model->save();
			}
		}
		//Mage::throwException(Mage::helper('auit_editor')->__('AuIt-Editor: Handler not found : %s','AAA'));
	}
	public function translateHTMLtoLocal(&$rData,$data)
	{
		$rData['data']=array();
		for ( $step=0; $step < 2; $step++ )
		{
			$this->_models=array();
			// STEP 0 ->  Default store 
			// STEP 1 -> Working store
			$d = $data;
				
			$handler = 'import'.$d['factory'].$d['typ'];
			$this->storeId = 0;
			if ( isset($d['storeId']) ) 	
				$this->storeId = $d['storeId'];
			if ( !method_exists($this,$handler ) || !($d['page_id']>0) )
				Mage::throwException(Mage::helper('auit_editor')->__('AuIt-Editor: Handler not found : %s',$handler));
			$this->$handler($step,$d,$rData);

			foreach ( $this->_models as $key => $model )
			{
				$mcls = explode('#',$key);
				
				switch ($mcls[0])
				{
					case 'cms/page':
						$rData['data']=array(
								'page_id'=>$model->getId(),
								'content'=>$model->getContent());
						break;
					case 'cms/block':
						$rData['data']=array(
								'page_id'=>$model->getId(),
								'content'=>$model->getContent());
						break;
					case 'catalog/category':
						$rData['data']=array(
								'page_id'=>$model->getId(),
								'content'=>$model->getData($d['data_field']['name']));
						break;
					case 'catalog/product':
						$rData['data']=array(
								'page_id'=>$model->getId(),
								'content'=>$model->getData($d['data_field']['name']));
						break;
					case 'blog/post':
						$rData['data']=array(
								'page_id'=>$model->getId(),
								'content'=>$model->getData($d['data_field']['name']));
						break;
					default:
						$rData['data']=array(
								'page_id'=>-1,
								'content'=>$mcls[0]);
						break;
				} 
			}
		}
		$rData['data']['content'] = $this->filterMarkups($rData['data']['content']);
		//Mage::throwException(Mage::helper('auit_editor')->__('AuIt-Editor: Handler not found : %s','AAA'));
	}
	protected function filterMarkups($value)
	{
		
    	$h=array();
        if(preg_match_all(Varien_Filter_Template::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER|PREG_OFFSET_CAPTURE )) {
        	$cursor=0;
        	$result='';
            foreach($constructions as $index=>$construction) 
            {
            	$directive=$construction[1][0];
            	$pos=$construction[0][1];
            	$marker=$construction[0][0];
                $result .= substr($value,$cursor,$pos-$cursor);
                
                $blockParameters = $this->_getIncludeParameters($construction[2][0]);
            	$cursor = $pos + strlen($marker);
                
            	$marker='{{'.$directive;
                foreach ( $blockParameters as $key => $param )
                {
                	switch ( $key )
                	{
                		case 'url':
                		case 'direct_url':
                			$marker.=" $key='".$param."'";
                			break;
                		default:
                			$marker.=" $key=\"".$param."\"";
                			break;
                	}
                }
                $marker.='}}';
           		$result .= $marker;
            }
            $result .= substr($value,$cursor);
            $value = $result;
        }
        if (Mage::getStoreConfig('auit_editor/editor/replacenl') == 1 )
        {
        	$value = str_replace("\n",'',$value); // Replace nl for nl2br
        }
        return $value;
	}
    protected function _getIncludeParameters($value)
    {
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);
        $params = $tokenizer->tokenize();
        foreach ($params as $key => $value) {
        	if (substr($value, 0, 1) === '$') {
        	    $params[$key] = $this->_getVariable(substr($value, 1), null);
        	}
        }
        return $params;
    }
	
	protected function getModel($modeClass,$id,$storeId=0,$bload=true)
	{
		$model=null;
		$key ="$modeClass#$id#$storeId";
		if ( !isset($this->_models[$key]) )
		{
			$this->_models[$key]= $model=Mage::getModel($modeClass);
			if ( $modeClass == 'catalog/category' || $modeClass == 'catalog/product' )
			{
				$this->_models[$key]->setStoreId($storeId);
				if ( $modeClass == 'catalog/category' )
				{
					$model->setAttributeSetId($model->getDefaultAttributeSetId());
				}
			}
				
			$model->load($id);
			if ($model->getId() != $id )
				Mage::throwException(Mage::helper('auit_editor')->__('AuIt-Editor: can\'t load Object: %s (%d)',$modeClass,$id));
		}
		return $this->_models[$key];
	}
	function DecToUtf ($UtfCharInDec,$bcheckSpez=0)
	{
			if ( $bcheckSpez==1 && ($UtfCharInDec == 60 ||  $UtfCharInDec == 62 || $UtfCharInDec == 38) ) // < > &
			{
				return '&#'.$UtfCharInDec.';';
			}
			$OutputChar = "";
			if($UtfCharInDec<128) $OutputChar .= chr($UtfCharInDec);
			else if($UtfCharInDec<2048)$OutputChar .= chr(($UtfCharInDec>>6)+192).chr(($UtfCharInDec&63)+128);
			else if($UtfCharInDec<65536)$OutputChar .= chr(($UtfCharInDec>>12)+224).chr((($UtfCharInDec>>6)&63)+128).chr(($UtfCharInDec&63)+128);
			else if($UtfCharInDec<2097152)$OutputChar .= chr($UtfCharInDec>>18+240).chr((($UtfCharInDec>>12)&63)+128).chr(($UtfCharInDec>>6)&63+128). chr($UtfCharInDec&63+128);
			return $OutputChar;
	}	
	/**
	 * 
	 * @param $xhtml
	 * @return unknown_type
	 */	
	protected function transformHTMLData($xhtml)
	{
		$xhtml = str_replace('&amp;','[%AMPAMPER%]',$xhtml);
		$xhtml = html_entity_decode($xhtml,ENT_NOQUOTES,'UTF-8');
		$xhtml = str_replace('[%AMPAMPER%]','&amp;',$xhtml);
		 
	 	$xmlstr = '<?xml version="1.0"  encoding="UTF-8"?>'.$xhtml.'';
		$xml = new DOMDocument();
		$xml->formatOutput=true;
		$xml->preserveWhiteSpace =false;
		if (Mage::getStoreConfig('auit_editor/editor/replacenl') == 1 )
        {
			$xmlstr=str_replace(array("\n","\r","\t")," ",$xmlstr);
        }
		if ( @$xml->loadXML($xmlstr) )
		{
			$helper= Mage::helper('auit_editor');
			$xpath = new DOMXPath($xml);
			$entries = $xpath->query('//*[contains(@class,"auit-edit-block")]');
			foreach ($entries as $entry) 
			{
				/** @var DOMNode $entry */
				$inlines =  $xpath->query('*[contains(@class,"auit-edit-inlineblock-description")]/*[@class="source"]',$entry);
				if ( $inlines && $inlines->length  > 0 )
				{
					foreach ( $inlines as $inline )
					{
						/** @var DOMNode $inline */
						$blockData = "\n".base64_decode($inline->nodeValue)."\n";
						$txtNode = $xml->createTextNode ( $blockData );
						$entry->parentNode->insertBefore($txtNode,$entry);	
					}
					$entry->parentNode->removeChild($entry);
				}
			}
			
			// _moz_resizing="true"
			//IMG alt="" src="file:///C:/Users/
			$query = '//*[@href or @src or @id or @_moz_dirty or @type="_moz" or @_moz_resizing  or @class or @style]';
			$entries = $xpath->query($query);
			foreach ($entries as $entry) 
			{
				$src= $entry->getAttribute('src');
				if ( $src ){
					$entry->setAttribute('src',$helper->unmaskAttribute($src));
				}
				$href= $entry->getAttribute('href');
				if ( $href )
					$entry->setAttribute('href',$helper->unmaskAttribute($href));
				$id= $entry->getAttribute('id');
				if ( $id && strpos($id,'ext-') !== false || strpos($id,'AUIT_') !== false )
					$entry->removeAttribute('id');
				$entry->removeAttribute('_moz_dirty');
				$entry->removeAttribute('_moz_resizing');
				$type= $entry->getAttribute('type');
				if ( $type == '_moz')
					$entry->removeAttribute('type');
				$class= $entry->getAttribute('class');
				if ( isset($class) &&  $class ==''){
					$entry->removeAttribute('class');
				}
				$style= $entry->getAttribute('style');
				if ( isset($style) &&  $style ==''){
					$entry->removeAttribute('style');
				}
				
			}
			// Empty elments
			
			$query = '//*[name() = "span" or name() = "li" or name() = "a"]';
			$entries = $xpath->query($query);
			foreach ($entries as $entry) 
			{
				if ( $entry->textContent == '' && !$entry->hasChildNodes() ) //&& !$entry->hasAttributes() )
				{
					$entry->parentNode->removeChild($entry); 
				}else {
				}
			}
			$query = '//*[name() = "p" or name() = "div"]';
			$entries = $xpath->query($query);
			foreach ($entries as $entry) 
			{
				if ( $entry->textContent == '' && !$entry->hasAttributes() && !$entry->hasChildNodes() )
				{
					$entry->parentNode->removeChild($entry); 
				}
			}
			
			$query = '//*[@resize_width or @resize_height]';
			$entries = $xpath->query($query);
			foreach ($entries as $entry) 
			{
				if ( strtolower ($entry->nodeName) == 'img' )
				{
					$src = 	$entry->getAttribute('src');
					if (Mage::getStoreConfig('auit_editor/editor/auto_resize') == 1 )
					{ 
						$rwidth  = $entry->getAttribute('resize_width');
						$rheight  = $entry->getAttribute('resize_height');
						$entry->setAttribute('src',$this->resizeImageDirectiveTo($src,$rheight,$rwidth));
					}else {
						//$entry->setAttribute('src',$src);
					}
				}
				$entry->removeAttribute('resize_width');
				$entry->removeAttribute('resize_height');
			}
			$html='';
			foreach ( $xml->documentElement->childNodes as $child )
			{
				$html.= $xml->saveXML($child)."\n";
			}
			$result = $helper->cleanMask($html);
			$result = $this->filterMarkups($result);
			return $result; 
		}else {
			Mage::log($xhtml);
			Mage::log(libxml_get_last_error());
		}
		Mage::throwException(Mage::helper('auit_editor')->__('AuIt-Editor: can\'t transform HTML-Data to XHTML'));
		
	}
	protected function resizeImageDirectiveTo($directive,$rheight,$rwidth)
	{
		if ( strpos($directive,'{{') === false )
		{
			return $directive;
		}
		$directive = str_replace('[MASK_QUOTE]','"',$directive);
		$directive = str_replace('[MASK_QUOTE_SINGLE]',"'",$directive);
		$info = Mage::helper('auit_editor')->translateDirectiveToUrl($directive);
		$url = $info['url'];	
		$helper= Mage::helper('auit_editor/skinmedia');
		$fromFile =  $helper->convertIdToPath($helper->convertUrlToPathArea($url),false);

		if ( $helper->isSubDir(dirname($fromFile)) )
		{
			$dirname = dirname($fromFile);
			if ( strpos(basename(dirname($fromFile)),'snm_size_') === 0 )
			{
				$dirname = dirname($dirname);
				if ( file_exists($dirname.DS.basename($fromFile)) )
				{
					$fromFile=$dirname.DS.basename($fromFile);//Use Originalfile
				}
			} 
			$toFile = $dirname.DS."snm_size_{$rwidth}_{$rheight}".DS.basename($fromFile);
			if ( !file_exists($toFile) && file_exists($fromFile))
			{
				$todir = "snm_size_{$rwidth}_{$rheight}";
				
				$dom = new DOMDocument();
				$root = $dom->createElement('config');
				$dom->appendChild($root);
				$target = $dom->createElement('target');
				$root->appendChild($target);
				$target->setAttribute ( 'name', 'tmp' );
			
				$tmp = $dom->createElement('mkdir');
				$tmp->setAttribute ( 'dir', $todir );
				$target->appendChild($tmp);
				
				$transform = $dom->createElement('transform');
				$transform->setAttribute ( 'tofile', $toFile );
				$target->appendChild($transform);
				
				$tmp = $dom->createElement('resize');
				$tmp->setAttribute ( 'width', $rwidth );
				$tmp->setAttribute ( 'height', $rheight );
				$transform->appendChild($tmp);
				
				$params=array();
				$params['FULLNAME']=$fromFile;
				$params['DIR']=$dirname;
				$params['FILENAME']=basename($fromFile);
				if ( Mage::helper('auit_editor/dirdirective')->executeDirective($dom,'tmp',$params) )
				{
					if ( file_exists($toFile) )
					{
						$directive = $helper->getDirective($toFile,'[MASK_QUOTE_SINGLE]');
					}
				}
				
			}
		}
		$directive = str_replace('"','[MASK_QUOTE]',$directive);
		$directive = str_replace("'",'[MASK_QUOTE_SINGLE]',$directive);
		
		return $directive;
	}
	protected function importCMSPageXHTML($step,array $d,array &$rData)
	{
		if ( $step > 0 ) return;
		$model = $this->getModel('cms/page',$d['page_id']);
		$model->setData($d['data_field']['name'],$this->transformHTMLData($d['xhtml']));
	}
	protected function importCMSBlockXHTML($step,array $d,array &$rData)
	{
		if ( $step > 0 ) return;
		$model = $this->getModel('cms/block',$d['page_id']);
		$model->setData($d['data_field']['name'],$this->transformHTMLData($d['xhtml']));
	}
	protected function importCMSPageProperties($step,array $d,array &$rData)
	{
		if ( $step > 0 ) return;
		$rData['reload']=true;
		$data = $d['data']['modifiedData'];
		if ( isset($data['store_id']) && !is_array($data['store_id']) ){
			$data['store_id']=explode(',',$data['store_id']);
		}
		$model = $this->getModel('cms/page',$d['page_id']);
		foreach ( $data as $name => $value)
			$model->setData($name,$value);
	}
	protected function importCMSBlockProperties($step,array $d,array &$rData)
	{
		if ( $step > 0 ) return;
		$rData['reload']=true;
		$data = $d['data']['modifiedData'];
		if ( isset($data['store_id']) && !is_array($data['store_id']) ){
			$data['store_id']=explode(',',$data['store_id']);
		}
		$model = $this->getModel('cms/block',$d['page_id']);
		foreach ( $data as $name => $value)
			$model->setData($name,$value);
	}
	protected function beforeSaveBlog($model)
	{
		$data = $model->getData();
		if ( isset($data['store_id']) && !isset($data['stores']))
		{
			if ( !is_array($data['store_id']) )
				$data['store_id'] = explode(',',$data['store_id']);
			$model->setData('stores',$data['store_id']);
		}
		if ( isset($data['cat_id']) && !isset($data['cats']))
		{
			if ( !is_array($data['cat_id']) )
				$data['cat_id'] = explode(',',$data['cat_id']);
			$model->setData('cats',$data['cat_id']);
		}
	}
	protected function importBlogXHTML($step,array $d,array &$rData)
	{
		if ( $step > 0 ) return;
		$model = $this->getModel('blog/post',$d['page_id']);
		$model->setData($d['data_field']['name'],$this->transformHTMLData($d['xhtml']));
	}
	protected function importBlogProperties($step,array $d,array &$rData)
	{
		if ( $step > 0 ) return;
		$rData['reload']=true;
		$data = $d['data']['modifiedData'];
		
/*		
		if ( !isset($data['store_id']) && isset($d['data']['data']['store_id']) )
			$data['store_id']=$d['data']['data']['store_id'];
			
		if ( !isset($data['cat_id']) && isset($d['data']['data']['cat_id']) )
			$data['cat_id']=$d['data']['data']['cat_id'];
		if ( isset($data['store_id']) && !is_array($data['store_id']) ){
			$data['stores']=explode(',',$data['store_id']);
			unset($data['store_id']);
		}
		if ( isset($data['cat_id']) && !is_array($data['cat_id']) ){
			$data['cats']=explode(',',$data['cat_id']);
			unset($data['cat_id']);
		}
		
		if ( isset($data['store_id']) && !is_array($data['store_id']) ){
			$data['store_id']=explode(',',$data['store_id']);
		}
		if ( isset($data['cat_id']) && !is_array($data['cat_id']) ){
			$data['cat_id']=explode(',',$data['cat_id']);
		}
		*/
		$model = $this->getModel('blog/post',$d['page_id']);
		foreach ( $data as $name => $value)
			$model->setData($name,$value);
	}
	
	protected function importCategoryPageXHTML($step,array $d,array &$rData)
	{
		if ( $d['data_field']['use_store']== 1 )
		{
			if ( $step == 0 ) return;
			$model 	= $this->getModel('catalog/category',$d['page_id'],$d['storeId']);
		}
		else {
			if ( $step > 0 ) return;
			$model = $this->getModel('catalog/category',$d['page_id'],0);
		}
		$model->setData($d['data_field']['name'],$this->transformHTMLData($d['xhtml']));
	}
	protected function importProductPageXHTML($step,array $d,array &$rData)
	{
		if ( $d['data_field']['use_store']== 1 )
		{
			if ( $step == 0 ) return;
			$model 	= $this->getModel('catalog/product',$d['page_id'],$d['storeId']);
		}
		else {
			if ( $step > 0 ) return;
			$model = $this->getModel('catalog/product',$d['page_id'],0);
		}

		$model->setData($d['data_field']['name'],$this->transformHTMLData($d['xhtml']));
	}
	protected function importCategoryPageProperties($step,array $d,array &$rData)
	{
		$rData['reload']=true;
		$model=null;
		$modelAttribute= Mage::getModel('catalog/category'); 
		foreach ( $d['data']['modifiedData'] as $name => $value )
		{
			/*
			if ( $name == 'image' && !isset($_FILES['image']))
			{
				// Dummy ansonsten gibts Fehler da immer ein File gewünscht wird
				$_FILES['image']=array('tmp_name'=>'',
										'tmp_name'=>'');
			}
			 
			*/
			if ( $step > 0 && $this->storeId > 0  )
			{
				$attribute = $modelAttribute->getResource()->getAttribute($name);
				if ( !$attribute )
				{
					Mage::log("Can't find Attribute for $name");
				}
				else if ( $attribute->isScopeStore() )
				{
					if ( !isset($model) )
						$model	= $this->getModel('catalog/category',$d['page_id'],$this->storeId);
					if ( isset($d['data']['use_store'][$name]) && $d['data']['use_store'][$name] == 1 )
						$model->setData($name,$value);
					else 
						$model->setData($name,Mage::getVersion()< 1.4?null:false);
				}
			}else if ( $step == 0  && ( !isset($d['data']['use_store'][$name]) || $d['data']['use_store'][$name] != 1) ){
				if ( !isset($model) )
					$model	= $this->getModel('catalog/category',$d['page_id'],0);
				$model->setData($name,$value);
			}
		}
	}

	protected function importProductPageProperties($step,array $d,array &$rData)
	{
		$rData['reload']=true;
		$model=null;
		$modelAttribute= Mage::getModel('catalog/product');
		foreach ( $d['data']['modifiedData'] as $name => $value )
		{
			if ( $step > 0 && $this->storeId > 0  )
			{
				$attribute = $modelAttribute->getResource()->getAttribute($name);
				if ( !$attribute )
				{
					Mage::log("Can't find Attribute for $name");
				}
				else if ( $attribute->isScopeStore() )
				{
					if ( !isset($model) )
						$model	= $this->getModel('catalog/product',$d['page_id'],$this->storeId);
						
					if ( isset($d['data']['use_store'][$name]) && $d['data']['use_store'][$name] == 1 )
					{
						$model->setData($name,$value);
					}
					else{ 
						$model->setData($name,Mage::getVersion()< 1.4?null:false);
					}
				}
			}else if ( $step == 0  && ( !isset($d['data']['use_store'][$name]) || $d['data']['use_store'][$name] != 1) ){
				if ( !isset($model) )
					$model	= $this->getModel('catalog/product',$d['page_id'],0);
				$model->setData($name,$value);
			}
		}
	}
	protected function importInlineBlockProperties($step,array $d,array &$rData)
	{
		
	}
}