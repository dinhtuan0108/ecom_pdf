<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
require_once 'Mage/Adminhtml/controllers/Catalog/CategoryController.php';
class AuIt_Editor_Admin_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{
    protected function newAWBlogPost(&$rData)
    {
    	$model=null;
    	try {
			$model = Mage::getModel('blog/comment'); // Ist AWBlog installiert?
			if ( $model )
			{
				$model = Mage::getModel('blog/post');// Wird überlagert
			}
    	}catch ( Exception $e)
    	{
    	}
		if ( $model )
		{
			
			// Hiden setzen un doverwrite cats mit hide
	    	$data=array();
	    	$data['stores']= array('0');
	    	$categories = array();
		  	$collection = Mage::getModel('blog/cat')->getCollection()->setOrder('sort_order', 'asc');
			foreach ($collection as $cat) {
				$categories[] = $cat->getCatId();
			}
	    	$data['cats']=$categories;
	    	$data['identifier']='blog_'.time();
	    	$data['title']='Blog';
	    	$data['status']=3;
	    	$model->setData($data);
	    	$model->setCreatedTime(now())->setUpdateTime(now());
	    	$username = Mage::getSingleton('admin/session')->getUser()->getFirstname() . " " . Mage::getSingleton('admin/session')->getUser()->getLastname();
			$model->setUser($username)->setUpdateUser($username);
	    	try {
	    		// save the data
	    		$model->save();
	    		$rData['success']=true;
				$rData['id']		='TBLOGPOST'.$model->getId();
				$rData['page_id']=$model->getId();
				$rData['text']	=$model->getTitle();
				$rData['clickurl']=  Mage::getUrl(Mage::helper('blog')->getRoute()."/" .$model->getIdentifier());
				
	    	} catch (Exception $e) {
	    		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	    	}
		}else {
			Mage::getSingleton('adminhtml/session')->addError('AWBlog modul not found');
		}
    	Mage::helper('auit_editor')->addMessages($rData);
    }
    protected function newCMSPage(&$rData)
    {
    	$model = Mage::getModel('cms/page');

    	$data=array();
    	$data['stores']= array('0');
    	$name = 'New Page '.Mage::helper('core')->formatDate(null, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);
    	$data['identifier']='new_page_'.time();
    	$data['title']=$name;
    	$data['is_active']=0;
    	$data['root_template']='two_columns_left';
    	
    	$model->setData($data);
    	try {
    		// save the data
    		$model->save();
    		$rData['success']=true;
			$rData['id']		='TCMS'.$model->getId();
			$rData['page_id']=$model->getId();
			$rData['text']	=$model->getTitle();
			$rData['clickurl']= Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_cms/cmspage',array('page_id'=>$model->getId()));
			
    	} catch (Exception $e) {
    		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    	}
    	Mage::helper('auit_editor')->addMessages($rData);
    }
    protected function dupCMSPage(&$rData,$data)
    {
    	$oldPageId = 0;
    	if ( isset($data['page_id']) )
    		$oldPageId=(int)$data['page_id'];
    	if ( $oldPageId > 0)
    	{
    		$model = Mage::getModel('cms/page');
    		$model->load($oldPageId);
    		if ( $model->getId() == $oldPageId)
    		{
    			$data =$model->getData(); 
    			$data['is_active']=0;
    			$data['title'] ='Copy of '.$data['title'];
    			$data['identifier'].='_copy';
    			$model->setData($data);
		    	try {
		    		$model->setId(null);
					if ( !$model->getData('stores') ) 
						$model->setData('stores',$model->getStoreId()); 
		    		
		    		// save the data
		    		$model->save();
		    		$rData['success']=true;
					$rData['id']		='TCMS'.$model->getId();
					$rData['page_id']=$model->getId();
					$rData['text']	=$model->getTitle();
					$rData['clickurl']= Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_cms/cmspage',array('page_id'=>$model->getId()));
					
		    	} catch (Exception $e) {
		    		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		    	}
    		}
    	}
		Mage::helper('auit_editor')->addMessages($rData);
    }
    protected function delCMSPage(&$rData,$data)
    {
    	$PageId = 0;
    	if ( isset($data['page_id']) )
    		$PageId=(int)$data['page_id'];
    	if ( $PageId > 0)
    	{
            $title = "";
            try {
                $model = Mage::getModel('cms/page');
                $model->load($PageId);
                $title = $model->getTitle();
                $model->delete();
                //Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('Page was successfully deleted'));
                Mage::dispatchEvent('adminhtml_cmspage_on_delete', array('title' => $title, 'status' => 'success'));
                $rData['success']=true;
                $rData['page_id']=$PageId;
            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_cmspage_on_delete', array('title' => $title, 'status' => 'fail'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
		Mage::helper('auit_editor')->addMessages($rData);
    }
    protected function dupCMSBlock(&$rData,$data)
    {
    	$BlockId = 0;
    	if ( isset($data['page_id']) )
    		$BlockId=(int)$data['page_id'];
		try {
			if (Mage::app()->isSingleStoreMode()) {
				Mage::getSingleton('adminhtml/session')->addError("This feature is only available in multi-store environments!");
			}
	    	else if ( $BlockId > 0)
	    	{
	    		$fromStoreId=(int)$data['fromStoreId'];
	    		$forStoreId=(int)$data['forStoreId'];
	            $block = Mage::getModel('cms/block')
	                ->setStoreId($forStoreId)
	                ->load($BlockId);
				if ( $block->getId() == $BlockId )	 
				{
			        $stores = array_flip($block->getStoreId());
					if ( count($stores) > 1 && isset($stores[$forStoreId]) )
					{
						unset($stores[$forStoreId]);
						$stores = array_flip($stores);
						$block->setStores($stores);
						$block->save();
						$block->setId(null);
						$block->setStores(array($forStoreId));
						$block->save();
						$rData['success']=true;
						$rData['reload']=true;
					}else if ( isset($stores[$forStoreId]) ) {
						;// bereits vorhanden
					}else {
						$stores = array_flip($stores);
						$block->setId(null);
						$block->setStores(array($forStoreId));
						$block->save();
						$rData['reload']=true;
						$rData['success']=true;
//						Mage::getSingleton('adminhtml/session')->addError("Block already exits from: $fromStoreId : to: $forStoreId - ".implode(",", $block->getStoreId()) );
					}				
				}else {
					Mage::getSingleton('adminhtml/session')->addError("Can't found Block!");
				}
	    	}
		} catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $rData['success']=false;
        }
    }
    
    public function cmspagecmdAction()
    {
		$rData = array();
		$rData['success']=false;
		if ($data = $this->getRequest()->getPost()) {
			if ( isset($data['action']))
			switch ($data['action'])
			{
				case 'cms_newpage':
					$this->newCMSPage($rData);
					break;
				case 'cms_duppage':
					$this->dupCMSPage($rData,$data);
					break;
				case 'cms_delpage':
					$this->delCMSPage($rData,$data);
					break;
				case 'awblog_newpost':
					$this->newAWBlogPost($rData);
					break;
				case 'cms_dupblock':
					$this->dupCMSBlock($rData,$data);
					break;
					/*
				case 'translate_html_to_local':
					$this->translateHTMLtoLocal($rData,$data);
					break;
				case 'translate_local_to_html':
					$this->translateLocaltoHTML($rData,$data);
					break;
					*/
				default:
    				Mage::getSingleton('adminhtml/session')->addError("Command not found");
					break;
			}
		}
		Mage::helper('auit_editor')->addMessages($rData);
    	$this->getResponse()->setBody(Zend_Json::encode($rData));
    }
    static function cmp_treechilds($a,$b)
    {
    	return strnatcasecmp($a['text'],$b['text']);
    }
    static function cmp_pages($a,$b)
    {
    	if ( !$a->getStoreId() )
    		return 1;
    	return -1;
    }
    protected function _NormTree($tree,$storeId,$storeName)
    {
    	$result = array();
    	usort ( $tree, array($this, "cmp_treechilds"));
    	foreach ( $tree as $link )
    	{
    		if ( isset($link['link']) )
    		{
				$Directive = $link['link'];
				$pageTitle = Mage::helper('auit_editor')->getText($link['label'],$link['modul']);
				$clickurl = Mage::helper('auit_editor')->translateDirective($Directive);
				$cmsurl =  $clickurl;
				if ( $clickurl != $Directive ) 
					$cmsurl .=  '##'.rawurlencode($Directive).'##';
				if ( $link['text'] != $pageTitle )
				{
					$link['qtip'] .= '<b>Store:'.$storeName.'</b><br/>'.$pageTitle."<br/>";
				}
				$link['stores'][$storeId]=array(
					'clickurl'=>$clickurl,
					'cmsurl'=>$cmsurl,
					'draghtml'=>'<a href="'.$cmsurl.'" title="'.$pageTitle.'">'.$pageTitle.'</a>'
				);
    		}    		
    		if ( isset($link['children']) )
    			$link['children'] = $this->_NormTree($link['children'],$storeId,$storeName);
    		$result[]=$link;
    	}
    	return $result;
    }
    public function specialJsonAction()
    {
    	$tree=array();
    	$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/speciallinks/reference');
    	if ( !is_array($t) )
    		$t = array();
    	foreach ( $t as $link )
    	{
    		$menu = $link['menu'];
    		$treepath = explode('/',$menu);
    		$c = count($treepath);
    		$parent =  &$tree;
    		foreach ( $treepath as $menuname )
    		{
    			if ( trim($menuname) == '' ) continue;
				if ( !isset($parent[$menuname]) )
				{
					$parent[$menuname]=array(
						'children'=>array(),
						'id'=>'SL'.uniqid(),
						'text'=>$menuname,
						'leaf'=>0,
						'qtip'=>'',
						'allowDrag'=>0
					);
				}
    			$item=&$parent[$menuname];
    			$parent = &$parent[$menuname]['children'];
				$c--;
				if ( $c == 0 && $link['link'])
				{
					$parent[]=array(
						'id'=>'SL'.uniqid(),
						'leaf'=>1,
						'qtip'=>'',
						'allowDrag'=>1,
						'text'=> Mage::helper('auit_editor')->getText($link['label'],$link['modul']),
						'stores'=>array(),
						'link'=>$link['link'],
						'label'=>$link['label'],
						'modul'=>$link['modul']
					);
				}
    		}
    	} 
		$stores = Mage::app()->getStores();
        foreach ( $stores as $store )
        {
        	$storeId=$store->getId();
        	Mage::app()->setCurrentStore($store);
        	$localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
        	Mage::app()->getTranslator()->setLocale($localeCode);
			Mage::app()->getTranslator()->init(Mage_Core_Model_App_Area::AREA_FRONTEND,true);
	    	$tree = $this->_NormTree($tree,$storeId,$store->getName());
        }
    	$this->getResponse()->setBody(Zend_Json::encode($tree));
    	return;
    	/*
    	if ( is_array($t) )
    	foreach ($t as $item )
    	{
    		$v = (int)($item['version']*10);
    		if ( $v == 0 || ($v > 0 && $v == $magVersion) || ( $v > 0 && $v < $magVersion &&  $item['op'] == 1) )
    		{
    			$blocks[]=array($item['reference'],$item['name']);
    		}
    	}
		*/
		
    	$url = Mage::helper('auit_editor')->translateDirective("{{store url='customer/account'}}");
    	//
    	//Mage::helper('customer')->__('Customer Login')
    	$pageTitle = 'login'.$url;
// 				$cmsurl = $urlModel->getUrl(null, array('_direct' => $page->getIdentifier()) );;//
	//			$cmsurl =  $cmsurl.'##'.rawurlencode("{{store direct_url='$Identifier'}}").'##';
    	
		$item['id']='SL1';
		$item['page_id']='0815';
		$item['text']=$pageTitle;
		$item['children']=array();
		$item['leaf']=true;
		$item['clickurl']=$clickurl;
		$item['cmsurl']=$cmsurl;
		//$item['identifier']=$Identifier;
		$item['allowDrag']=1;
		$item['draghtml']='<a href="'.$cmsurl.'" title="'.$pageTitle.'">'.$pageTitle.'</a>';
    	$tree[]=$item;
    	
		usort ( $tree, array($this, "cmp_treechilds"));
		$this->getResponse()->setBody(Zend_Json::encode($tree));
	}
    
    public function cmspageJsonAction()
    {
    	$homeIdentifier = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
    	
//		$cstore = Mage::app()->getStore();
        Mage::app()->setCurrentStore(Mage::helper('auit_editor')->getBaseStore());
    	
        $collection = Mage::getModel('cms/page')->getCollection();
//        $pageUrl = Mage::helper('cms/page')->getPageUrl($pageId);
        $collection->setFirstStoreFlag(true);
        $pages=array();
		foreach ( $collection as $page )
		{
			$storeCode = $page->getStoreCode();
			$page = Mage::getModel('cms/page')->load($page->getId());
			$page->setStoreCode($storeCode);
			$pages[$page->getIdentifier()][]=$page;
		}    	
		$tree=array();
		
		
        /*
					$item['id']='TCMS'.$page->getId();
					$item['page_id']=$page->getId();
					$item['text']=$title;
					$item['children']=array();
					$item['leaf']=true;
					$item['clickurl']=$clickurl;
					$item['cmsurl']=$cmsurl;
					$item['identifier']=$Identifier;
					$item['allowDrag']=1;
					$item['draghtml']='<a href="'.$cmsurl.'" title="'.$pageTitle.'">'.$pageTitle.'</a>';
		*/
		
		foreach ( $pages as $identifier => $spage )
		{
			$item=array();
			usort ( $spage, array($this, "cmp_pages"));
			foreach ( $spage as $idx => $page )
			{
				$x='';
				$navStoreId=1;
				if ( $page->getStoreId() )
				foreach ( $page->getStoreId() as $storeId)
				{
					$navStoreId=$storeId;
					if ( $x )$x.=',';
					 $x.=Mage::getSingleton('adminhtml/system_store')->getStoreName($storeId);
				}
				$pageTitle = $page->getTitle();
				$title = $pageTitle;
				if ( $x )
					$title.=' ('.$x.')';
				
				$Identifier=$page->getIdentifier();
				if ( $Identifier == $homeIdentifier )
					$Identifier = '';
				$params=array();
				//$params['_direct']=$Identifier;
				$params['_query']=array();
				//$params['_query']['page_id'] = $page->getId();
				$params['page_id'] = $page->getId();
				//$params['_query']['AUITINLINEEDIT'] = 1;
//				$params['_query']['_store'] = $page->getStoreCode();
				//if ( $idx > 0 )
					//$params['_query']['___usesid'] = $page->getStoreCode();
				
				//Mage::app()->setCurrentStore(Mage::app()->getStore($navStoreId));
				
				$urlModel = Mage::getModel('core/url');//->setStore($navStoreId);
				if ($idx > 0 ) 
				{
					$params['_query']['___store'] = Mage::app()->getStore($navStoreId)->getCode();
					$urlModel->setStore($navStoreId);
				}
 				$clickurl = $urlModel->getUrl('auit_editor/admin_cms/cmspage', $params);
 				
 				//$clickurl = Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_cms/cmspage',array('page_id'=>$page->getId()));
 				
				//$cmsurl = Mage::getUrl(null, array('_direct' => $page->getIdentifier()));
 				$cmsurl = $urlModel->getUrl(null, array('_direct' => $page->getIdentifier()) );;//
 				
				$cmsurl =  $cmsurl.'##'.rawurlencode("{{store direct_url='$Identifier'}}").'##';
 				
 				
				if ( $idx == 0 )
				{
					$item['id']='TCMS'.$page->getId();
					$item['page_id']=$page->getId();
					$item['text']=$title;
					$item['children']=array();
					$item['leaf']=true;
					$item['clickurl']=$clickurl;
					$item['cmsurl']=$cmsurl;
					$item['identifier']=$Identifier;
					$item['allowDrag']=1;
					$item['draghtml']='<a href="'.$cmsurl.'" title="'.$pageTitle.'">'.$pageTitle.'</a>';
					
				}else {
					$store=array();
					$item['id']='TCMS'.$page->getId();
					$store['page_id']=$page->getId();
					$store['text']=$title;
					$store['children']=array();
					$store['leaf']=true;
					$store['clickurl']=$clickurl;
					$store['cmsurl']=$cmsurl;
					$store['identifier']=$Identifier;
					$item['allowDrag']=1;
					$store['draghtml']='<a href="'.$cmsurl.'" title="'.$pageTitle.'">'.$pageTitle.'</a>';
					unset($item['leaf']);
					$item['children'][]=$store;
					
				}
			}
			$tree[]=$item;			
		}
		usort ( $tree, array($this, "cmp_treechilds"));

		$homepages=array();
		$homepages['id']='HOMPAGEROOT';
        $homepages['text'] = 'All Store Views';
		$homepages['children'] =array();
        $AwebSites=array();
        $webSites = Mage::app()->getWebsites();
        foreach ($webSites as $webSite )
        {
        	$item = array();
        	$item['id']='WEBSITE'.$webSite->getId();
        	$item['text'] = $webSite->getName();
        	$item['code'] = $webSite->getCode();
        	$store['clickurl']='';
        	$item['allowDrag']=0;
        	$item['children'] =array();
        	foreach ( $webSite->getStores() as $st )
        	{
				$params=array();
				$params['_query']=array();
				$params['page_id'] = 'default';
				$params['_query']['initstoreid'] = $st->getId();
				$urlModel = Mage::getModel('core/url');
				$urlModel->setStore($st->getId());
 				$clickurl = $urlModel->getUrl('auit_editor/admin_cms/cmspage', $params);        		
        		$item['children'][]=array(
        			'id'=>'HOMPS'.$st->getId(),
        			'page_id'=>'default',
        			'text'=>$st->getName(),
        			'leaf'=>true,
        			'clickurl'=>$clickurl,
        			'allowDrag'=>0
        		);
        	}
        	$homepages['children'][]=$item;	
        		
        }
        array_unshift($tree,$homepages); 
		$this->getResponse()->setBody(Zend_Json::encode($tree));
	}
    protected function htmlEscape($data, $allowedTags = null)
    {
        return Mage::helper('core')->htmlEscape($data, $allowedTags);
    }	
	public function getSEOProductUrl($product)
    {
    	$categoryId = $product->getCategoryId();
    	if ( 1 || !$categoryId )
    	{
			$categoryId = $product->getCategoryIds();
            if(is_array($categoryId)){
                $categoryId = array_pop($categoryId);
            }
    	}
		$category = Mage::getModel('catalog/category')->load($categoryId);
            $saveCategory = Mage::registry('current_category');
            if ( $saveCategory != null )
    		Mage::unregister('current_category');
    	Mage::register('current_category', $category);
    	$url = $product->getProductUrl();
    	if ( $saveCategory )
    	{
    		Mage::unregister('current_category');
    		Mage::register('current_category', $saveCategory);
    	}
    	return $url; 
    }    
    protected function _initCategory($getRootInstead = false)
    {
        $categoryId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);
        if ($categoryId) {
            $category->load($categoryId);
        }
        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
    }
    protected function _addStoreCategoryInfo(&$tree)
    {
        $categoryIds = array();
        $treeMap=array();
        foreach ( $tree as &$child)
        {
			if ( isset($child['children']))
        		unset($child['children']);
			if ( isset($child['allowDrop']))
				unset($child['allowDrop']);
			$child['name']=substr($child['text'],0,strpos($child['text'],' ('));
			$child['allowDrag']=0;
			$child['qtip']='';
			$child['stores']=array();
			
			$categoryIds[]=	$child['id'];
			$treeMap[$child['id']]=&$child;
		}
		$helper = Mage::helper('auit_editor');
		$stores = Mage::app()->getStores();
        foreach ( $stores as $store )
        {
        	$storeId=$store->getId();
        	$collection = Mage::getResourceModel('catalog/category_collection')->setStoreId($store->getId());
            $collection->addAttributeToSelect(array('name','status','meta_title','is_active','is_anchor'));
            $collection->addFieldToFilter('entity_id', array('in' => $categoryIds));
            foreach ($collection as $scategory)
			{
				if ( $scategory->getLevel() <= 1 ) continue;
				$id = $scategory->getId();
				if ( isset($treeMap[$id]) ) 
				{
					$scategory->getUrlInstance()->setStore($storeId);
					$clickurl	= $scategory->getUrl();
    				$cmsurl = str_replace($scategory->getUrlInstance()->getDirectUrl(''),'',$clickurl);
    				$cmsurl = "{{store direct_url='$cmsurl'}}";
					$cmsurl 	=  $clickurl.'##'.rawurlencode ($cmsurl).'##';
					$title = $scategory->getMetaTitle();
					$name = $scategory->getName();
					$treeMap[$id]['stores'][$storeId]=array(
						'store'=>$storeId,
						'name'=>$name,
						'title'=>$title,
						'clickurl'=>$clickurl,
						'is_anchor'=>$scategory->getIsAnchor(),
						'level'=>$scategory->getLevel(),
						'isactive'=>$scategory->getIsActive(),
						'draghtml'=> '<a href="'.$cmsurl.'" title="'.$title.'">'.$name.'</a>'
					);
					$treeMap[$id]['allowDrag']=1;
				}
			}	            	
        }
        foreach ( $tree as &$child)
        {
        	$qtip='';
        	$name = $child['name'];
        	foreach ( $child['stores'] as $sid => $sinfo )
        	{
        		if ( $sinfo['name'] != $name )
					$qtip.= '<b>Store:'.Mage::app()->getStore($sid)->getName().'</b><br/>'.$sinfo['name']."<br/>";        		
        	}
        	$child['qtip']=$qtip;
		}
    }
    protected function _addStoreProductInfo(&$gtree,$productIds,$categoryId)
    {
        $collection = Mage::getResourceModel('catalog/product_collection')->setStoreId(0);
	    $collection->addAttributeToSelect(array('name','status','visibility'));
        $collection->addFieldToFilter('entity_id', array('in' => $productIds));
        $tree=array();
		foreach ($collection as $product)
		{
			$item = array(
				'id'=>	'P'.$product->getId().':'.$product->getProductUrl(),
				'pid'=>	$product->getId(),
				'text' => $product->getName(),
				'leaf' => true,
				'allowDrag'=>0,
				'stores'=>array(),			
				'qtip'=>''
			);
			$tree[]=$item;
		}
        $treeMap=array();
        foreach ( $tree as &$child)
        {
        	if ( isset($child['pid']) )
				$treeMap[$child['pid']]=&$child;
		}
		
		$cstore = Mage::app()->getStore();
		$stores = Mage::app()->getStores();
        foreach ( $stores as $store )
        {
        	$storeId=$store->getId();
        	Mage::app()->setCurrentStore($store);
        	$collection = Mage::getResourceModel('catalog/product_collection')->setStoreId($storeId);
		    $collection->addAttributeToSelect(array('name','status','visibility'));
	        $collection->addFieldToFilter('entity_id', array('in' => $productIds));
			foreach ($collection as $product)
			{
				$pid = $product->getId();
				if ( isset($treeMap[$pid]) ) 
				{
					$product->setCategoryId($categoryId);
					$product->setDoNotUseCategoryId(false);
					$product->setStoreId($storeId);					
					
					$clickurl	= $product->getProductUrl(false);
    				$cmsurl = str_replace($product->getUrlModel()->getUrlInstance()->getDirectUrl(''),'',$clickurl);
    				$cmsurl = "{{store direct_url='$cmsurl'}}";
					$cmsurl 	=  $clickurl.'##'.rawurlencode ($cmsurl).'##';
					$title = $product->getMetaTitle();
					$name = $product->getName();
					$treeMap[$pid]['stores'][$storeId]=array(
						'store'=>$storeId,
						'name'=>$name,
						'title'=>$title,
						'clickurl'=>$clickurl,
						'draghtml'=> '<a href="'.$cmsurl.'" title="'.$title.'">'.$name.'</a>'
					);
					$treeMap[$pid]['allowDrag']=1;
				}
			}
        }
        Mage::app()->setCurrentStore($cstore);
        
        foreach ( $tree as &$child)
        {
        	$qtip='';
        	$name = $child['text'];
        	foreach ( $child['stores'] as $sid => $sinfo )
        	{
        		if ( $sinfo['name'] != $name )
					$qtip.= '<b>Store:'.Mage::app()->getStore($sid)->getName().'</b><br/>'.$sinfo['name']."<br/>";        		
        	}
        	$child['qtip']=$qtip;
			$gtree[]=$child;
		}
    }
	public function categoriesJsonAction()
    {
    	$storeId=0;
   		$this->getRequest()->setParam('storeID',$storeId);
    	$this->getRequest()->setParam('store',$storeId);
        $categoryId=0;
        $bisRoot=false;
        if ($this->getRequest()->getPost('id') == 'ROOT') {
        	$categoryId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        	$bisRoot=true;
        }
        if ($categoryId || ($categoryId = (int) $this->getRequest()->getPost('id')) ) {

        	$this->getRequest()->setParam('id', $categoryId);
            $tree = array();
        	if ($category = $this->_initCategory()) {
				$bl = $this->getLayout()->createBlock('auit_editor/tree');
		        $tree = $bl->getTree($category);

		        $this->_addStoreCategoryInfo($tree);
	            
	            $productIds = $category->getProductsPosition();
                $productIds = array_keys($productIds);
                if (empty($productIds)) {
                    $productIds = 0;
                }
	            if ( $productIds != 0 )
	            {
	            	$this->_addStoreProductInfo($tree,$productIds,$categoryId);
	            }
        	}	        
            $this->getResponse()->setBody(Zend_Json::encode($tree));
        }
	}
	/*
	protected function addlinkDlgCmsTree()
    {
    	$tree=array();
        $collection = Mage::getModel('cms/page')->getCollection();
        $collection->setFirstStoreFlag(true);
        $pages=array();
		foreach ( $collection as $page )
		{
			$storeCode = $page->getStoreCode();
			$page = Mage::getModel('cms/page')->load($page->getId());
			$page->setStoreCode($storeCode);
			$pages[$page->getIdentifier()][]=$page;
		}    	
		foreach ( $pages as $identifier => $spage )
		{
			$item=array();
			foreach ( $spage as $idx => $page )
			{
				$x='';
				if ( $page->getStoreId() )
				foreach ( $page->getStoreId() as $storeId)
				{
					if ( $x )$x.=',';
					 $x.=Mage::getSingleton('adminhtml/system_store')->getStoreName($storeId);
				}
				$title = $page->getTitle();
				if ( $x )
					$title.=' ('.$x.')';
				$params=array();
				$params['_direct']=$page->getIdentifier();
				$params['_query']=array();
				$params['_query']['___store'] = $page->getStoreCode();
				
 				$url = Mage::getUrl(null, $params);			
 				$url = Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*'.'/admin_cms/cmspage',array('page_id'=>$page->getId()));
 				
 				
				if ( $idx == 0 )
				{
					$item['id']='TCMS'.$page->getId();
					$item['page_id']=$page->getId();
					$item['text']=$title;
					$item['children']=array();
					$item['leaf']=true;
					$item['clickurl']=$url;
				}else {
					$store=array();
					$item['id']='TCMS'.$page->getId();
					$store['page_id']=$page->getId();
					$store['text']=$title;
					$store['children']=array();
					$store['leaf']=true;
					$store['clickurl']=$url;
					unset($item['leaf']);
					$item['children'][]=$store;
					
				}
			}
			$tree[]=$item;			
		}
		return $tree;
    }
    */	
	public function linkDlgJsonAction()
    {
		$rData = array();
		$rData['success']=true;
		if ($data = $this->getRequest()->getPost()) {
			if ( isset($data['action']))
			switch ($data['action'])
			{
				case 'translate':
					$rData['info']= Mage::helper('auit_editor')->translateDirectiveToUrl($data['directive']);
					break;
				case 'translatetodirective':
					$rData['info']= Mage::helper('auit_editor')->translateUrlToDirective($data['url']);
					break;
				default:
    				Mage::getSingleton('adminhtml/session')->addError("Command not found");
					break;
			}
		}
		Mage::helper('auit_editor')->addMessages($rData);
    	$this->getResponse()->setBody(Zend_Json::encode($rData));
    }
	
	public function menuJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setIsTreeWasExpanded(false);
        }
        $categoryId=0;
        $bisRoot=false;
        if ($this->getRequest()->getPost('id') == 'ROOT') {
        	$categoryId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        	$bisRoot=true;
        }
        if ($categoryId || ($categoryId = (int) $this->getRequest()->getPost('id')) ) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $tree = $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
                    ->getTree($category);
            if ( 1 || $bisRoot)
            {
            	foreach ( $tree as &$childs)
            	//	foreach ( $childs as &$child)
						if ( isset($childs['children']))
          		          	unset($childs['children']);
            	
            }
            $this->getResponse()->setBody(Zend_Json::encode($tree));
        }
    }
    
}
