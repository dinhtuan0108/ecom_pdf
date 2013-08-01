<?php
/**
 * AuIt
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Admin_CmsController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();
	}
	protected function _getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}
	public function cmspageAction() {
		if ( ($pageId=$this->getRequest()->getParam('page_id')) )
		{
			$useStoreId=1;
			if ( $pageId == 'default')
			{
				$pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
				$useStoreId = $this->getRequest()->getParam('initstoreid');
				if ( $useStoreId <= 0 ) $useStoreId=1;
			}
			Mage::helper('auit_editor')->setInlineEdit(1);
			
			
			$page = Mage::getSingleton('cms/page');
			
			if ( $pageId == Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE) )
			{
				$page->setStoreId($useStoreId)
					->load($pageId, 'identifier');			
			}else {
				$page->load($pageId);
				if ( $page->getId() )
				{
					$pageStore =$page->getStoreId();
					if ( isset($pageStore) && is_array($pageStore) )
					{
						$useStoreId=array_shift ( $pageStore );
					}
				}
			}
			if ( !$page->getId() )
			{
				$useStoreId=1;
				$page = Mage::getSingleton('cms/page');
				$page->setStoreId(1);
				$page->load(Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE));
			}
			
			$indent = $page->getIdentifier();
			if ( $indent == Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE) )
				$indent = '';

			$controller = 'index'; //cms_index_index
			$ActionName = 'index';
			if ( $indent != '' )
			{
				$controller = 'page'; //cms_page_view
				$ActionName = 'view';
			}
			if ( $useStoreId == 0 ) $useStoreId=1;			
			$this->_swapToStore($useStoreId);
			
			$url = Mage::getUrl(null, array('_direct' => urlencode($indent)));

			$uri = Zend_Uri::factory($url);
            if ($uri->valid()) {
                $path  = $uri->getPath();
                $query = $uri->getQuery();
                if (!empty($query)) {
                    $path .= '?' . $query;
                }
                Mage::app()->getRequest()->setRequestUri($path );
                
            }			
			
			$this->getRequest()->setModuleName('cms')
			->setRouteName('cms')
			->setControllerName($controller)
			->setActionName($ActionName)
			->setParam('page_id', $pageId)
			->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,$page->getIdentifier());
			// neu laden
			Mage::app()->getRequest()->setPathInfo();
			Mage::helper('auit_editor')->setCurrentPageUrl($page->getIdentifier());
			if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
				$this->getResponse()->setBody("Cant't show page : $pageId");
			}
			
		}
	}
	protected function _swapToStore($newStoreId)
	{
		Mage::app()->setCurrentStore(Mage::app()->getStore($newStoreId));
		$locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
		if (!$locale) {
			$locale = Mage_Core_Model_Locale::DEFAULT_LOCALE;
		}
		Mage::app()->getLocale()->setLocaleCode($locale);
		Mage::app()->getTranslator()->setLocale($locale);
		Mage::app()->getTranslator()->init($this->getLayout()->getArea());//, true);

		//$Webid = Mage::app()->getStore()->getWebsiteId();
		//$websiteStores = Mage::app()->getWebsite()->getStores();
	}
	public function cmdAction()
	{
		$action = $this->getRequest()->getParam('action');
		if ( $action == 'block_preview')
		{
			$factory = $this->getRequest()->getParam('factory');
			$cmsPage = null;
			switch ( $factory )
			{
				case 'CMSPage':
					if ( ($pageId=$this->getRequest()->getParam('page_id')) )
					{
						if ( $pageId == 'default')
						{
							$pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
						}
						$cmsPage = Mage::getSingleton('cms/page');
						$cmsPage->load($pageId);
						$this->_swapToStore($this->getRequest()->getParam('storeId'));
					}
					break;
				default:
					$cmsPage = Mage::getSingleton('cms/page');
					$cmsPage->setId(-1);
					break;
			}

			try {
				if ( $cmsPage )
				{
					$cmsPage->setRootTemplate('empty');
					$cmsPage->setContent($this->getRequest()->getParam('data'));
					if (!Mage::helper('cms/page')->renderPage($this)) {
						$this->getResponse()->appendBody("Cant't create preview<br/>".$this->getRequest()->getParam('data'));
					}
				}
			} catch (Exception $e) {
				$this->getResponse()->appendBody("Cant't create preview<br/>".$e->getMessage());
			}
			$this->getResponse()->appendBody($this->getLayout()->getMessagesBlock()->getGroupedHtml());
			return;
		}
		$rData = array();
		$rData['success']=false;

		if ( ($data = $this->getRequest()->getPost()) ) {
			switch ($action)
			{
				case 'block_definition':
					$this->getBlockDefintion($rData,$data);
					break;
				case 'menutag_definition':
					$this->getMenuDefintion($rData,$data);
					break;
				case 'translate_html_to_local':
					$this->translateHTMLtoLocal($rData,$data);
					break;
				case 'translate_local_to_html':
					$this->translateLocaltoHTML($rData,$data);
					break;
				case 'translate_block_to_html':
					$this->translateBlocktoHTML($rData,$data);
					break;
				case 'block_preview':
					break;
				case 'save_css_template':
					$this->saveCSSTemplate($rData,$data);
					break;
				case 'save_widget_template':
					$this->saveWidgetTemplate($rData,$data);
					break;
				case 'save_blocks_template':
					$this->saveBlocksTemplate($rData,$data);
					break;
				case 'save_menus_template':
					$this->saveMenusTemplate($rData,$data);
					break;
				case 'save_html_template':
					$this->saveHTMLTemplate($rData,$data);
					break;
				default:
					Mage::getSingleton('adminhtml/session')->addError("Command not found");
					break;
			}
		}
		Mage::helper('auit_editor')->addMessages($rData);
		$this->getResponse()->setBody(Zend_Json::encode($rData));
	}
	protected function translateHTMLtoLocal(&$rData,$data)
	{
		$data = Zend_Json::decode($data['data']);
		$content = Mage::getModel('auit_editor/content');
		try {
			$content->translateHTMLtoLocal($rData,$data);
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			$rData['success']=false;
		}
	}
	protected function translateLocaltoHTML(&$rData,$data)
	{
		$data = Zend_Json::decode($data['data']);
		$helper = Mage::helper('auit_editor/content');
		try {
			$helper->translateLocaltoHTML($rData,$data);
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			$rData['success']=false;
		}
	}
	protected function translateBlocktoHTML(&$rData,$data)
	{
		$data = Zend_Json::decode($data['data']);
		$helper = Mage::helper('auit_editor/content');
		try {
			$helper->translateBlocktoHTML($rData,$data);
			$rData['success']=true;
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
			$rData['success']=false;
		}
	}
	protected function saveCSSTemplate(&$rData,$data)
	{
		$rData['success']=true;
		$data = Zend_Json::decode($data['data']);
		Mage::helper('auit_editor')->setArrayStoreConfig('auit_editor/styles/style',$data);
	}
	
	protected function saveWidgetTemplate(&$rData,$data)
	{
		$rData['success']=true;
		$data = Zend_Json::decode($data['data']);
		Mage::helper('auit_editor')->setArrayStoreConfig('auit_editor/widgets',$data);
	}
	protected function saveBlocksTemplate(&$rData,$data)
	{
		$rData['success']=true;
		$data = Zend_Json::decode($data['data']);
		foreach ( $data as $key => &$item )
		{
			$title= '';
			$identifier= '';
			$block  = Mage::getModel('cms/block');
			if ( isset($item['classname'])  && $item['classname'] ==  'auit-new-block' )
			{
				$blockData=array();
				$blockData['title']=$item['name'];
				$blockData['identifier']='block_'.uniqid();
				$blockData['stores']=array(0);
				$blockData['is_active']='0';
				$blockData['content']='';
				$block->setData($blockData);
				$block->save();
				$title= $block->getTitle();
				$identifier= $block->getIdentifier();
				$item['classname']=$block->getIdentifier();
				
			}else {
				$block->load($item['classname']);
				if ($block->getId() )
				{
					/* Geht nicht !!
					if ( $title !=  $item['name'] )
					{
						$block->setTitle($item['name']);
						$block->setData('stores',$block->getStoreId()); 
						$block->save();
						$title= $block->getTitle();
					}
					*/
					
					
				} 					                
			}
			$title= $block->getTitle();
			$identifier= $block->getIdentifier();
			$template= $item['template'];
			$sort= $item['sort_order'];
			$item['description']='<b>Block:</b><br/>'.$title.' ('.$identifier.')<br/><b>Template:</b><br/>'.$template.'<br/><b>Sort Order:</b> '.$sort;
			if ( isset($item['templates']) )
				unset($item['templates']);
			if ( isset($item['showat']) )
				unset($item['showat']);
			if ( isset($item['scheduleat']) )
				unset($item['scheduleat']);
				
		}
		Mage::helper('auit_editor')->setArrayStoreConfig('auit_editor/blocks',$data);
		$rData['blocks'] = $data;
	}
	
	protected function saveHTMLTemplate(&$rData,$data)
	{
		$rData['success']=true;
		$data = Zend_Json::decode($data['data']);
		 
		Mage::helper('auit_editor')->setArrayStoreConfig('auit_editor/templates/html',$data);
	}
	protected function getBlockDefintion(&$rData,$data)
	{
		$rData['success']=true;
		$rData['blockoption']=$this->getBlocksOption();

		$rData['staticblocks']=array();
		$collection = Mage::getModel('cms/block')->getCollection();
		$bl=array();
		foreach ( $collection as $block )
		{
			$bl[$block->getIdentifier()]=$block->getTitle();
		}
		$rData['staticblocks'][]=array('auit-new-block',Mage::helper('auit_editor')->htmlEscape(Mage::helper('auit_editor')->__('--Create new static block--')));
		foreach ( $bl as $identifier => $title)		
			$rData['staticblocks'][]=array($identifier,$title.' ('.$identifier.')');
			
		$rData['templates']=$this->getBlocksTemplates();
		$rData['showat']=array(
			array('0','All Pages'),
			array('cms-index','Homepage'),
			array('cms-page','CMS Pages'),
			array('catalog-category','Catalog Pages'),
			array('catalog-product','Product Pages'),
			array('checkout-cart','Cart Page'),
			array('customer-account','My Account'),
			array('beforelogin','Before Login'),
			array('afterlogin','After Login'),
			array('blog-index','AW-Blog'),
			array('blog-post','AW-Blog Post')
		);
		$rData['scheduleat']=array(
			array('','Every day','0'),
			array('Weekdays','Mondays','mon'),
			array('Weekdays','Tuesdays','tue'),
			array('Weekdays','Wednesdays','wed'),
			array('Weekdays','Thursdays','thu'),
			array('Weekdays','Fridays','fri'),
			array('Weekdays','Saturdays','sat'),
			array('Weekdays','Sunday','sun'),
			array('Special','Odd days','odd'),
			array('Special','Even days','even')
		);
	}
    protected function getBlocksTemplates()
    {
    	$blocks = array();
    	$magVersion = (int)(Mage::getVersion()*10);
     	$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/blocksdefinition/templates');
    	if ( is_array($t) )
    	foreach ($t as $item )
    	{
    		$v = (int)($item['version']*10);
    		if ( $v == 0 || ($v > 0 && $v == $magVersion) || ( $v > 0 && $v < $magVersion &&  $item['op'] == 1) )
    		{
    			$blocks[]=array($item['template'],$item['name']);
    		}
    	}
    	return $blocks;
    }
	protected function getBlocksOption()
    {
    	$blocks = array();
    	$magVersion = (int)(Mage::getVersion()*10);
 
    	$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/blocksdefinition/reference');
    	if ( is_array($t) )
    	foreach ($t as $item )
    	{
    		$v = (int)($item['version']*10);
    		if ( $v == 0 || ($v > 0 && $v == $magVersion) || ( $v > 0 && $v < $magVersion &&  $item['op'] == 1) )
    		{
    			$blocks[]=array($item['reference'],$item['name']);
    		}
    	}
    	return $blocks;
    	$blocks = array(
    		array('left', 'Left Column'),
    		array('content' , 'Main Content Area'),
    		array('bottom.container' , 'Page Footer'),
    		array('top.container' , 'Page Header'),
    		array('right' , 'Right Column')
    	);
    	return $blocks;
    }
	
    
	protected function getMenuDefintion(&$rData,$data)
	{
		$rData['success']=true;
		$rData['menutags']=$this->getMenuOption();
		$rData['storefield']=Mage::helper('auit_editor')->getStoreField();
	}
	protected function getMenuOption()
    {
    	$blocks = array();
    	$magVersion = (int)(Mage::getVersion()*10);
 
    	$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/menudefinition/reference');
    	if ( is_array($t) )
    	foreach ($t as $item )
    	{
    		$v = (int)($item['version']*10);
    		if ( $v == 0 || ($v > 0 && $v == $magVersion) || ( $v > 0 && $v < $magVersion &&  $item['op'] == 1) )
    		{
    			$blocks[]=array($item['reference'],$item['name']);
    		}
    	}
    	return $blocks;
    }
	protected function saveMenusTemplate(&$rData,$data)
	{
		$rData['success']=true;
		$data = Zend_Json::decode($data['data']);
		foreach ( $data as $key => &$item )
		{
			$title= '';
			$identifier= '';
			$item['classname']='menu';
			if ( isset($item['store_id']) )
				$storeIds = explode(',',$item['store_id']);
			else
				$storeIds=array(0);
			$store='';
			if (in_array(0, $storeIds) ) {
	            $store = Mage::helper('adminhtml')->__('All Store Views');
	        }else {
	        	foreach ($storeIds as $storeId )
	        	{
	        		if ( $store ) $store.=','; 
	        		$store .= Mage::app()->getStore($storeId)->getName();
	        	} 
	        }
			$link= $item['href'];
			$sort= $item['sort_order'];
			$item['description']='';
			if ( $store )
				$item['description'].='<b>Store View:</b><br/>'.$store.'<br/>';
				
			$item['description'].='<b>Link:</b><br/>'.$link.'<br/><b>Sort Order:</b> '.$sort;
			
			
		}
		Mage::helper('auit_editor')->setArrayStoreConfig('auit_editor/menus',$data);
		$rData['menus'] = $data;
	}
    
}
