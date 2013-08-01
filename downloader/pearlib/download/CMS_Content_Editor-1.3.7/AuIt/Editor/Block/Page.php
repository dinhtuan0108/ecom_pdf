<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Page extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
    	if ( !Mage::helper('auit_editor')->getIsInInlineMode() )
    		return '';
    	$pageData = array();
   		$pageData = Mage::helper('auit_editor/content')->getPageData();

   		$path = Mage::helper('auit_editor')->getCurrentPageUrl();
    	//$pageData['BASE_URL']= Mage::helper('auit_editor')->getBaseStore()->getUrl('');
    	$pageData['BASE_URL']= Mage::helper('auit_editor')->getAdminUrl();
    	
    	
      	//$pageData['BASE_URL']= Mage::helper('auit_editor')->getBaseStore()->getUrl('');
    	
    	$pageData['JS_URL']= Mage::helper('auit_editor')->getBaseStore()->getBaseUrl('js');
    	$pageData['STORE_URL']="{{store direct_url='$path'}}";
    	$pageData['STORE_ID']=Mage::app()->getStore()->getId();
		
    	
		$html= '<script type="text/javascript">
		AUIT_PAGEDATA='.Zend_Json::encode($pageData).';';
		$html .= '</script>';
//			$surl = Mage::getUrl('auit_editor');
   			$req = Mage::app()->getFrontController()->getRequest();
			$surl = $req->getScheme().'://'.$req->getHttpHost().$req->getBaseUrl().'/auit_editor';
			$html.= '<iframe src="'.$surl.'" style="display:none"></iframe>';
	//	$url= Mage::getBaseUrl('js').'auit/editor/cross.js';
		//$html .= '<script type="text/javascript" src="'.$url.'"></script>';
		//$html.= '<img src="'.$surl.'" />';
		return  $html;
    }
}
/*
 		if (0 && Mage::helper('auit_editor')->getIsInInlineMode() )
		{
			$html .= <<<EndHTML

				document.addEventListener("click",function(event){
					var el = event.target;
					var anker=null;
					var InCont=false;
					while ( el&&el.nodeName)
					{
						if ( el.nodeName == 'A' && !anker)
							anker=el;
						if ( el.contentEditable===true || el.contentEditable==='true')
						{
							InCont=true;
							anker=null;
							break;
						}
						el = el.parentNode;
					}
					if ( anker )
					{
						var url = anker.href; 
						if ( url.indexOf('?') < 0 )
							url+='?';
						url+='&AUITINLINEEDIT=1';
						window.location.href=url;
						event.preventDefault(); 
						event.stopPropagation();
						return false;
					}
					if ( InCont )
					{
						event.preventDefault(); 
						event.stopPropagation();
						return false;
					}
				},false);

EndHTML;
		}
*/ 