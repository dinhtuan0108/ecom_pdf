<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2008 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GENERATION_ENABLED = 'auit_producttools/producttools/enabled';
    protected $_htmlAttributes=array();
    protected $_currentPagePath;
    public function getLangJsPath()
    {
    	$lng = Mage::app()->getLocale()->getLocaleCode();
    	$mylngs=array(
    		'de_DE'=>'de_DE.js',
    		'de_CH'=>'de_DE.js',
    		'de_AT'=>'de_DE.js'
    	);
    	if ( isset($mylngs[$lng]) )
    		return 'auit/editor/locale/'.$mylngs[$lng];
    	return 'auit/editor/locale/en_US.js';
    }
    public function getLangExtJsPath()
    {
    	$lng = Mage::app()->getLocale()->getLocaleCode();
    	$mylngs=array(
    		'de_DE'=>'ext-lang-de.js',
    		'de_CH'=>'ext-lang-de.js',
    		'de_AT'=>'ext-lang-de.js',
    	);
    	if ( isset($mylngs[$lng]) )
    		return 'auit/extjs/ext321/locale/'.$mylngs[$lng];
    	return '';
    }
    public function getBaseStore()
	{
		return Mage::app()->getStore(1);
	}
	public function getBaseStoreId()
	{
		return 1;
	}
	public function setCurrentPageUrl($pageUrl)
	{
		$this->_currentPagePath=$pageUrl;
	}
	protected function setSessionVar($var,$value)
	{
		if ( !session_id() )
		{
			Mage::log("setSessionVar Keine sessionId!");
			return;
		}	
		Mage::getSingleton('adminhtml/session')->setData($var,$value);
	}
	protected function getSessionVar($var,$defvalue='')
	{
		if ( !session_id() )
		{
			Mage::log("setSessionVar Keine sessionId!");
			return;
		}	
		return Mage::getSingleton('adminhtml/session')->getData($var);
	}
	public function setInlineEdit($b=0)
	{
		$this->setSessionVar('inline_edit',$b?1:0);
	}
	public function setAdminUrl($url)
	{
		$key = 'auit_editor/base_admin_url';
		if ( $url != Mage::app()->getStore(0)->getConfig($key) )
		{
	        try {
	            Mage::getModel('core/config_data')
	                ->load($key, 'path')
	                ->setValue($url)
	                ->setPath($key)
	                ->save();
				Mage::app()->cleanCache(array(Mage_Core_Model_Config::CACHE_TAG));
	        } catch (Exception $e) {
	        }
		}	
	}
	public function getAdminUrl()
	{
		$key = 'auit_editor/base_admin_url';
		$url = Mage::app()->getStore(0)->getConfig($key);
		return $url;
	}
	public function setAdminLocaleCode($url)
	{
		$this->setSessionVar('admin_locale_code',$url);
	}
	public function getAdminLocaleCode()
	{
		return $this->getSessionVar('admin_locale_code');
	}
	public function getIsInInlineMode()
	{
    		
		$edit = Mage::app()->getRequest()->getParam('AUITINLINEEDIT',0);
		if ( !$edit )
		{
			$edit = $this->getSessionVar('inline_edit');
		}
		if ( $edit )
		{
			Mage::helper('auit_editor')->setInlineEdit(1);
		}
		return $edit;
	}
	public function getCurrentPageUrl()
	{
		if ( isset($this->_currentPagePath) )
			return $this->_currentPagePath;
			
		return substr(str_replace('','',$this->_getRequest()->getOriginalPathInfo()),1);
	}
    public function isEnabled($block,$id=null)
	{
		return Mage::getStoreConfigFlag("auit_producttools/$block/enabled",$id); 
	}
	public function translateDirective($directive)
    {
		if (  strpos($directive,'{{') !== false )  
		{  	
	    	$helper = Mage::helper('cms');
	    	if ( $helper && method_exists( $helper,'getPageTemplateProcessor' ) )
	    	{
	    		$processor = $helper->getPageTemplateProcessor();
	    	}else {
				$processor = Mage::getModel('core/email_template_filter');
	    	}
	        $directive = $processor->filter($directive);
		}
    	return $directive;
    }
	public function maskAttribute($marker)
    {
		if (  strpos($marker,'##') !== false )
			return $marker;
    	return $marker.'##'.rawurlencode ($marker).'##'; 
    }
	public function unmaskAttribute($construction)
    {
    	if ( ($p=strpos($construction,'##')) )
    	{
    		$v = substr($construction,$p+2);
    		$p2=strrpos($v,'##');
    		$x = rawurldecode(substr($v,0,$p2));
    		$x .= substr($v,$p2+2);
    		$construction=str_replace('"','[MASK_QUOTE]',$x);
    		$construction=str_replace("'",'[MASK_QUOTE_SINGLE]',$construction);
    	}
    	return $construction; 
    }
    public function cleanMask($html)
    {
    	$html = str_replace('[MASK_QUOTE]','"',$html);
    	$html = str_replace('[MASK_QUOTE_SINGLE]',"'",$html);
    	if (Mage::getStoreConfig('auit_editor/editor/replacenl') == 1 )
    	{
	    	$html = str_replace("\n",'',$html);
	    	$html = str_replace("\r",'',$html);
    	}
    	return $html; 
    }
/*
 * 
 * <img src="{{skin url=""}}images/media/bild_homepage1.png" 
 */
    public function prefilter($value)
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
            	switch($directive)
                {
                	case 'skin':
                	case 'media':
                	case 'store':
                	case 'protocol':
                		$result .= $this->maskAttribute($marker);
                		break;
                	case 'block':
                	case 'layout':
                	case 'widget':
                	case 'config':
                		$result .= Mage::helper('auit_editor/content')->addInlineBlock($marker,$directive);
                		break;
                	case 'var':
                	case 'include':
                	case 'depend':
                	case 'if':
                	case 'htmlescape':
                	default:
                		$result .= $marker;
                		break;
                }
            	$cursor = $pos + strlen($marker);
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
    public function addMessages(&$rData,$messagesStorage='adminhtml/session',$bremove=true)
    {
    	//Mage_Core_Model_Message_Collection
    	/*
    	const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const SUCCESS   = 'success';
    	 */
		$messages = Mage::getSingleton($messagesStorage)->getMessages($bremove);
        foreach ($messages->getItems() as $message) 
        {
    		if ( !isset($rData['messages']) )
    			$rData['messages']=array();
    		if ( !isset($rData['messages'][$message->getType()]))
    			$rData['messages'][$message->getType()]=array();
    		$rData['messages'][$message->getType()][]=$message->getText();
        }
    }
    
	public function translateUrlToDirective($url)
	{
		if ( !$url ) return '';
        $helper = Mage::helper('auit_editor/skinmedia');
		//$url= Mage::Helper('auit_editor')->translateDirective($directive);
		return array('url'=>$url,'directive'=>$url);
	}
    public function translateDirectiveToUrl($directive)
	{
		if ( !$directive ) return '';
        $helper = Mage::helper('auit_editor/skinmedia');
		$url= Mage::Helper('auit_editor')->translateDirective($directive);
		if ( stripos($url,'http') !== 0 )
		{
			$url='http://'.$url;
		} 
		return array('url'=>$url,'directive'=>$directive);
	}	
    public function getArrayStoreConfig($key)
    {
    	$value = Mage::getStoreConfig($key);
    	if ( !trim($value))
    		$value = Mage::helper('auit_editor/config')->getDefaults($key);
    	else {
    		if ( strpos($value,'base64:') === 0 )
    		{
    			$value = base64_decode(substr($value,7));
    		}
    	}
    	if ( !is_array($value) )
    		$value=@unserialize($value);
    	if ( $key == 'auit_editor/widgets' )
    		$value = Mage::helper('auit_editor/config')->checkWidgets($value);
    	return $value; 
    }
    public function setArrayStoreConfig($key,$data)
    {
    	$value=@serialize($data);
        try {
        	$value='base64:'.base64_encode($value);
            Mage::getModel('core/config_data')
                ->load($key, 'path')
                ->setValue($value)
                ->setPath($key)
                ->save();
    
			Mage::app()->cleanCache(array(Mage_Core_Model_Config::CACHE_TAG));
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save '.$key));
        }
    }
    public function isEditAttribute($attribute)
    {
		return ( 	$attribute && ($attribute->getFrontendInput() == 'textarea'|| $attribute->getFrontendInput() == 'text')
					&&
	    			( 
	    				(Mage::getVersion() >= 1.4 && $attribute->getIsWysiwygEnabled())
	    				||
	    				( Mage::getVersion() == 1.3 && $attribute->getIsHtmlAllowedOnFront()) 
	    			)
		)?true:false;    
    }
    public function getHTMLAttributeDescription($key,$attributes)
    {
    	if ( !isset($this->_htmlAttributes[$key]) )
    	{
	    	$htmlfields=array();
	    	switch ( $key )
	    	{
	    		case 'catalog_attributes':
	    			$htmlfields['description']=array('USE_FILTER'=>1);
	    		break;
	    		case 'product_attributes':
	    			foreach ( $attributes as $attribute )
	    			{
	    				if ( 	$this->isEditAttribute($attribute) )
    					{
    						$htmlfields[$attribute->getAttributeCode()]=array('USE_FILTER'=>1);
    					}
	    			}
    			break;
	    	}
	    	$this->_htmlAttributes[$key]=$htmlfields;
    	}
    	return $this->_htmlAttributes[$key];
    }
    public function checkProductAttribute(Mage_Catalog_Model_Product $product)
    {
    	 foreach ($product->getAttributes() as $attribute) {
    	 	if ( 	$this->isEditAttribute($attribute) )
    	 	{
    	 		$code = $attribute->getAttributeCode();
    	 		$data = $product->getData($code);
    	 		if (Mage::getStoreConfig('auit_editor/editor/replacenl') == 1 )
    			{
    				$data = str_replace(array("\n","\r"),'',$data);
    			}
    	 		$product->setData($code,$data);
    	 	}
    	 }    
    }
    public function checkCategoryAttribute(Mage_Catalog_Model_Category $category)
    {
    	 foreach ($category->getAttributes() as $attribute) {
    	 	if ( 	$this->isEditAttribute($attribute) )
    	 	{
    	 		$code = $attribute->getAttributeCode();
    	 		$data = $category->getData($code);
    	 		if (Mage::getStoreConfig('auit_editor/editor/replacenl') == 1 )
    			{
    				$data = str_replace(array("\n","\r"),'',$data);
    			}
    	 		$category->setData($code,$data);
    	 	}
    	 }    
    }
    public function productAttribute($callObject, $attributeHtml, $params)
    {
    	return $this->translateDirective($attributeHtml);
    }

    public function categoryAttribute($callObject, $attributeHtml, $params)
    {
    	return $this->translateDirective($attributeHtml);
	}
	
    public function getText($t,$modul='cms')
    {
    	$modul=trim($modul);
    	if ( !$modul )
    		$modul='cms';
  		try {
    		$helper = Mage::helper($modul);
  		}catch ( Exception $e)
  		{
  		
  		}
    	if ( !$helper )
    		$helper = Mage::helper('cms');
    	return $helper->__($t);
    }
	
    public function getStoreField()
    {
    	$StoreField=null;
        if (!Mage::app()->isSingleStoreMode()) {
			$StoreField=array(	'xtype'=>'GroupingComboBox','name'=>'store_id',
    							'fieldLabel'=> $this->getText('Store View'),
    							'mode'=> 'local',
    							'typeAhead'=> true,
    							'triggerAction'=> 'all',
    							'lazyRender'=> true,
    							'valueField'=> 'value',
    							'displayField'=> 'text',
    							'groupField'=> 'group',
								'multiselect'=>true,
    							'store'=> array(	
    								'xtype'=>'simplestore',
									//'id'=>2,
					        		'fields'=> array('group', 'text','value'),
									'data'=>$this->toGroupV( Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true) )
					    		)    					
    						);
        }
		return  $StoreField;
    }
    public function trimLabel($label)
    {
		return str_replace(array('&nbsp;'),'',$label);
    }
    
    public function toGroupV(array $opts,$group=false)
    {
    	$Ret = array();
    	foreach ( $opts as $opt )
    	{
    		if ( isset($opt['value']) && is_array($opt['value']) )
    		{
    			 $r = $this->toGroupV($opt['value'],$this->trimLabel($opt['label']));
    			 if ( count($r)== 0 )
    			     $Ret[]=array($this->trimLabel($opt['label']),false,false);
    			 foreach ( $r as $r2 )
    			 	$Ret[]=$r2;
    		}else{
    			$Ret[]=array($group,$this->trimLabel($opt['label']),$opt['value']);
    		} 
    	}
    	return $Ret;    					        	
    }
    
}