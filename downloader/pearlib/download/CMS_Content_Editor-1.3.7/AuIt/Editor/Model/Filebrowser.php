<?php
class AuIt_Editor_Model_Filebrowser
{
	function getRootPath()
	{
		$basePath = '/';//realpath(dirname(__FILE__ ) . '/data') . '/';
		return $basePath; 
	}
	function handleRequest(Mage_Core_Controller_Request_Http $request)
	{
		$data = $request->getPost();
		$action = $data['action'];
		
		$storeID = $request->getParam('storeID');
		if ( $storeID > 0 ) {
			$store = Mage::app()->getStore($storeID);
			Mage::app()->getLocale()->emulate($store);
            Mage::getDesign()->setStore($store);
			Mage::getDesign()->setTheme('');
            Mage::getDesign()->setPackageName('');			
		}
		
		try {
			switch($action) {
			case 'imageedit':
				return $this->imageEdit($request);
				break;
			case 'load-directive':
				return $this->loadDirective($request);
				break;
			case 'save-directive':
				return $this->saveDirective($request);
				break;
			case 'delete-directive':
				return $this->deleteDirective($request);
				break;
				
			case 'navto-directive':
				return $this->getNavTo($request);
				break;
			case 'get-folders':
				return $this->getFolders($request);
				break;
			case 'create-folder':
				return $this->createFolder($request);
				break;
			case 'rename-folder':
				return $this->renameFolder($request);
				break;
			case 'delete-folder':
				return $this->deleteFolder($request);
				break;
			case 'rename-file':
				return $this->renameFile($request);
				break;
			case 'delete-file':
				return $this->deleteFile($request);
				break;
			}
		}catch (Exception $e)
		{
			$data['success']=false;
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::log("Exception:".$e->getMessage());
		}
		Mage::helper('auit_editor')->addMessages($data);
		return $data;
	}
	
	public function uploadFiles(Mage_Core_Controller_Request_Http $request)
	{
		$data = array(
			'success'	=> false
		);
		$helper = Mage::helper('auit_editor/skinmedia');
		try {
			if ( !$helper->isCurrentRootPath() )
	        {
	        	$type = trim($request->getParam('type'));
	        	$result = $helper->getStorage()->uploadFile($helper->getCurrentPath());
	        	$data['success']=true;
	        	$data['savefile']=$result['file'];
	        }
		}catch (Exception $e)
		{
			$data['success']=false;
			$data['messages'][]=$e->getMessage();
			
		}
		return $data;
	}
	
	protected function imageEdit(Mage_Core_Controller_Request_Http $request)
	{
		$data = array(
			'success'	=> false,
			'message '=>''
		);
		$nodeData=null;
		$imageData = $request->getParam('data','');
		$imageData = Zend_Json::decode($imageData);
		$helper = Mage::helper('auit_editor/skinmedia');
		
	//	$tmpDir = Mage::getConfig()->getOptions()->getMediaDir();
		//$tmpDir .= DS.'tmp'.DS.'snm-portal'.DS.session_id();
		
		if ( $imageData['mode'] )
		{
			$dom = new DOMDocument();
			$root = $dom->createElement('config');
			$dom->appendChild($root);
			$target = $dom->createElement('target');
			$root->appendChild($target);
			$target->setAttribute ( 'name', 'tmp' );
			
			$load = $dom->createElement('load');
			$target->appendChild($load);
			$load->setAttribute ( 'from', $imageData['src'] );
//			$load->setAttribute ( 'to', $tmpDir );
			switch ( $imageData['mode'] )
			{
				case 'REPLACEFILE':
					$resize = $dom->createElement('replacefile');
					$target->appendChild($resize);
					$resize->setAttribute ( 'from',  $imageData['src']);
					$resize->setAttribute ( 'to', $imageData['original_src']);
					$imageData['mode']='';
					break;
				default:
					$transform = $dom->createElement('transform');
					$target->appendChild($transform);
					switch ( $imageData['mode'] )
					{
						case 'CROP':
							$crop = $dom->createElement('crop');
							$transform->appendChild($crop);
							$crop->setAttribute ( 'top',  $imageData['crop_y']);
							$crop->setAttribute ( 'height', $imageData['crop_height']);
							$crop->setAttribute ( 'width', $imageData['crop_width']);
							$crop->setAttribute ( 'left', $imageData['crop_x'] );
							$imageData['mode']='';
							break;
						case 'RESIZE':
							$resize = $dom->createElement('resize');
							$transform->appendChild($resize);
							$resize->setAttribute ( 'width',  $imageData['resize_width']);
							$resize->setAttribute ( 'height', $imageData['resize_height']);
							$imageData['mode']='';
							break;
						case 'ROTATE':
							$resize = $dom->createElement('rotate');
							$transform->appendChild($resize);
							$resize->setAttribute ( 'angle',  $imageData['rotate']);
							$imageData['mode']='';
							$imageData['rotate']=0;
							break;
						case 'FLIPHORZ':
							$resize = $dom->createElement('fliph');
							$transform->appendChild($resize);
							$imageData['mode']='';
							break;
						case 'FLIPVERT':
							$resize = $dom->createElement('flipv');
							$transform->appendChild($resize);
							$imageData['mode']='';
							break;
					}
					break;
			}
			$Dirhelper = Mage::helper('auit_editor/dirdirective');
			$params=array();
			$Dirhelper->executeDirective($dom,'tmp',$params);
			$imageData['src'] = $helper->getUrl($params['FULLNAME']);
			$imageData['node'] = $nodeData=$this->getFileNodeData($params['FULLNAME']);
		}		
		
		$data['data']=$imageData;
		$data['success']=true;
		return $data;
		//$data['url']=$pathInfo['url'];
	}
	
	protected function deleteDirective(Mage_Core_Controller_Request_Http $request)
	{
		$helper = Mage::helper('auit_editor/skinmedia');
		$path = Mage::helper('auit_editor/skinmedia')->getCurrentPath();
		$dirFile = Mage::helper('auit_editor/dirdirective')->getDirectiveFor($path,true);
		$xmldata='';
		if ( $dirFile && is_file($dirFile))
		{
			@unlink($dirFile);
		} 
		$data = array(
			'success'	=> true,
			'message'=>''
		);
		return $data;
	}
	protected function saveDirective(Mage_Core_Controller_Request_Http $request)
	{
		$helper = Mage::helper('auit_editor/skinmedia');
		$path = Mage::helper('auit_editor/skinmedia')->getCurrentPath();
		
		$mode = $request->getParam('mode');
		$dirFile = Mage::helper('auit_editor/dirdirective')->getDirectiveFor($path,true);
		$dirFilename = Mage::helper('auit_editor/dirdirective')->getDirectiveFilename($path);
		
		if ( $dirFilename && ( ( $dirFile && $mode == 'replace' && is_file($dirFile)) || $mode == 'new'))
		{
			$json = $request->getParam('data');
			$json = Zend_Json::decode($json);
			$dom = new DOMDocument();
			$domformatOutput=true;
			$root = $dom->createElement('config');
			$dom->appendChild($root);
			$this->buildDirective($json,$dom,$root);
			
			@file_put_contents($dirFilename,$dom->saveXML());
		} 
		$data = array(
			'success'	=> true,
			'message'=>'',
			'directive'=>$dirFile
		);
		return $data;
	}
	protected function buildDirective($data,$dom,$parentNode)
	{
		$xml='';
		foreach ($data as $tag => $d)
		{
			if ( $tag != '@attributes')
			{
				$node = $dom->createElement($tag);
				$attr = @$d['@attributes'];
				if (  $attr )
					foreach ( $attr as $an => $ad )
					{
						$node->setAttribute ( $an, $ad );
					}
				$parentNode->appendChild($node);
				$this->buildDirective($d,$dom,$node);
			}
		}
	}
	protected function loadDirective(Mage_Core_Controller_Request_Http $request)
	{
		$helper = Mage::helper('auit_editor/skinmedia');
		
		$path = Mage::helper('auit_editor/skinmedia')->getCurrentPath();
		$dirFile = Mage::helper('auit_editor/dirdirective')->getDirectiveFor($path,true);
		$xmldata='';
		if ( $dirFile )
		{
			$xmldata = @simplexml_load_file($dirFile);
			
		} 
		$data = array(
			'success'	=> true,
			'message'=>'',
			'directive'=>$dirFile,
			'json'=>$xmldata
		);
		return $data;
	}
	protected function getNavTo(Mage_Core_Controller_Request_Http $request)
	{
		$data = array(
			'success'	=> false,
			'message '=>''
		);
		$directive = $request->getParam('directive','');
		$helper = Mage::helper('auit_editor/skinmedia');
		/*
		if ( $request->getParam('useRelative','') && $directive)
		{
			$updir = $helper->getStorage()->getConfigData('upload_root');
			$directive = '{{media url="'.$updir.'/'.$directive.'"}}';
		}
		*/
		$pathInfo= $this->translateDirectiveToTreePath($directive,$request->getParam('rootID'));
		
		$path = $pathInfo['path'];
		$file = $pathInfo['file'];

		//$data['navto']=$path.'/'.$helper->convertPathToId($pathInfo['localpath']);//Mage::helper('core')->urlEncode($file);
		$data['navto']=$path;
		$data['url']=$pathInfo['url'];
		$data['localpath']=$pathInfo['localpath'];
		$data['navtofile']=$file;
		
		$data['success']=true;
		return $data;
	}
	protected function translateDirectiveToTreePath($directive,$rootID)
	{
		if ( !$directive )  
			return array('path'=>'','file'=>'','localpath'=>'','directive'=>'');

		$helper = Mage::helper('auit_editor/skinmedia');
		
		$url= Mage::Helper('auit_editor')->translateDirective($directive);
    	
		$path = $helper->convertUrlToPathArea($url);
	    $localpath = $helper->convertIdToPath($path,false);
	  	
	  
	   
	    $file = basename($path);
		if ( $rootID == 'ROOT')
		{
			$catalogPath =  $helper->getCatalogRoot();
			if ( strpos($localpath,$catalogPath) === 0)
			{
	    		$splits = explode('/',$path);
				$pp = array_shift($splits);
				$pp .= '/'.array_shift($splits);
				$pp .= '/'.array_shift($splits);
				//$pp .= array_shift($splits);
				$path = '/ROOT/'.$helper->convertPathToId($catalogPath);
	    		while ( count($splits) )
	    		{
	    			$pp .= '/'.array_shift($splits);
	    			$path .= '/'.$helper->convertPathToId($pp);
	    		}
				
			}else if ( $path )
	    	{
	    		$splits = explode('/',$path);
	    		$pp = array_shift($splits);
	    		$path = '/ROOT/'.$pp;
	    		while ( count($splits) )
	    		{
	    			$pp .= '/'.array_shift($splits);
	    			$path .= '/'.$helper->convertPathToId($pp);
	    		}
			}
			return array('path'=>$path,'file'=>$file,
				'localpath'=>$localpath,
				'url'=>$url,
				'directive'=>$directive);
			
		}
   		$rootpath=$helper->convertIdToPath($rootID);
		$filepath=$helper->convertIdToPath($path,false);
		$path = '/'.$rootID.'/'.$rootID;
    	if ( strpos($filepath,$rootpath) === 0 )
    	{
    		$filepath = $helper->getRelative($filepath,$rootpath);
    		$splits = explode('/',$filepath);
    		$pp = $rootpath; 
    		while ( count($splits) )
    		{
    			$pp .= '/'.array_shift($splits);
    			$path .= '/'.$helper->convertPathToId($pp);
    		}
    	}
		return array('path'=>$path,
				'file'=>$file,
				'localpath'=>$localpath,
				'url'=>$url,
				'directive'=>$directive);
	}	
	protected function getFolders(Mage_Core_Controller_Request_Http $request)
	{
	
		$helper = Mage::helper('auit_editor/skinmedia');
        
		$jsonArray = array();
		if (!$helper->checkFolders() )
		{
			return $jsonArray;
		}
		if ( $helper->isCurrentRootPath() )
    	{
			//$directive = $request->getParam('directive','');
			/*
			$mode = $request->getParam('mode','all');
			if ( $mode == 'categorie' ) 
			{
				$path =  $helper->getCategoryRoot();
				$jsonArray[] = array(
	                'text'  => 'Categorie',
	                'id'    => AuIt_Editor_Helper_Skinmedia::CATEGORYROOT,
				 	'iconCls'   => 'bfolder',
	    			'leaf' => false,
	    			'isdir' => true,
					'root'=>true,
	    			'dirdirective'=>file_exists($path.DS.'.directive.xml')
				);
				
			}else {
			*/ 
				$path =  $helper->getMediaRoot();
	    		$jsonArray[] = array(
	                'text'  => 'Media',
	                'id'    => AuIt_Editor_Helper_Skinmedia::MEDIAROOT,
				 	'iconCls'   => 'bfolder',
	    			'leaf' => false,
	    			'isdir' => true,
	    			'root'=>true,
	    			'dirdirective'=>file_exists($path.DS.'.directive.xml')
	            );
				$path =  $helper->getCatalogRoot();
	    		$jsonArray[] = array(
	                'text'  => 'Category',
	                'id'    => $helper->convertPathToId($path),
				 	'iconCls'   => 'bfolder',
	    			'leaf' => false,
	    			'isdir' => true,
	    			'root'=>true,
	    			'dirdirective'=>file_exists($path.DS.'.directive.xml')
	            );
	            $path =  $helper->getSkinRoot();
	            $jsonArray[] = array(
	                'text'  => 'Skin (Default)',
	                'id'    => AuIt_Editor_Helper_Skinmedia::SKINROOT,
				 	'iconCls'   => 'bfolder',
	    			'leaf' => false,
	    			'isdir' => true,
	            	'root'=>true,
	    			'dirdirective'=>file_exists($path.DS.'.directive.xml')
	            );
	            $path =  $helper->getSkinThemeRoot();
	            $jsonArray[] = array(
	                'text'  => 'Skin (Theme)',
	                'id'    => AuIt_Editor_Helper_Skinmedia::SKINTHEMEROOT,
				 	'iconCls'   => 'bfolder',
	    			'leaf' => false,
	    			'isdir' => true,
	            	'root'=>true,
	    			'dirdirective'=>file_exists($path.DS.'.directive.xml')
	            );
	            
			//}
 			return $jsonArray;	
    	} 
        /*
        $collection = $helper->getStorage()->getDirsCollection($helper->getCurrentPath());
        foreach ($collection as $item) {
            $jsonArray[] = array(
                'text'  => $item->getBasename(),
                'id'    => $helper->convertPathToId($item->getFilename()),
                'cls'   => 'folder',
                'leaf' => false
            );
        }
        */
    	$path = $helper->getCurrentPath();
    	$filter = '';
    	if ( $request->getParam('isRoot') == 1 )
    	{
    		if ( $request->getParam('node','') != 'AUITFBR' )
    		{
    			$filter = basename($path);
    			$path = dirname($path);	
    		}
    	}
    	$rootID = $helper->convertIdToPath($request->getParam('rootID'));

    	
    	$collection = $helper->getStorage()->getFileDirsCollection($path);
        foreach ($collection as $item) {
        	if ( is_file($item->getFilename() ) )
        	{
	        	$jsonArray[] = $this->getFileNodeData($item->getFilename(),$rootID);
        	}else if ( is_dir($item->getFilename()) && (!$filter || $filter == basename($item->getFilename())))
        	{
				$jsonArray[] = array(
					'id'    => $helper->convertPathToId($item->getFilename()),
	                'iconCls'   => 'bfolder',
	                'leaf' => false,
					'text' => $item->getBasename(),
					'size' => 0,
					'isize'=> '',
					'isdir' => true,
					'date_modified' => '',
					'thumbnail' =>  '', 
					'url' =>  '',
					'directive' => '',
					'relative' => '',
					'dirdirective'=>file_exists($item->getFilename().DS.'.directive.xml'),
					'root'=>$filter?true:false
				//,'expanded'=>$filter?true:false
				
				);
        		
        	}
            
            
            
            
        }
        
        return $jsonArray;
	}
    protected function getShortFilename($filename, $maxLength = 15)
    {
        if (strlen($filename) <= $maxLength) {
            return $filename;
        }
        return preg_replace('/^(.{1,'.($maxLength - 3).'})(.*)(\.[a-z0-9]+)$/i', '$1..$3', $filename);
    }
	protected function createFolder($request)
	{
		$data = array(
			'success'	=> false
		);
		
        $name = trim($request->getParam('newName'));
		$helper = Mage::helper('auit_editor/skinmedia');
		
        if ( !$helper->isCurrentRootPath() && $name)
        {
        	$result = $helper->getStorage()->createDirectory($name,$helper->getCurrentPath());
        	$data['id']= $result['id'];
        	$data['success']=true;
        }
		return $data;
	}
	protected function renameFolder($request)
	{
		$data = array(
			'success'	=> false
		);
		
		
		$newName = trim($request->getParam('newName'));
		$helper = Mage::helper('auit_editor/skinmedia');
		
        if ( !$helper->isCurrentRootPath() )
        {
        	$newPath = $helper->getStorage()->renameDirectory($newName,$helper->getCurrentPath());
        	
        	$data['id']= $helper->convertPathToId($newPath);
        	$data['success']=true;
        }
		return $data;
	}
	protected function deleteFolder($request)
	{
		$data = array(
			'success'	=> false
		);
		$helper = Mage::helper('auit_editor/skinmedia');
        if ( !$helper->isCurrentRootPath() )
        {
        	$helper->getStorage()->deleteDirectory($helper->getCurrentPath());
        	$data['success']=true;
        }
		return $data;
	}
	protected function renameFile($request)
	{
		$data = array(
			'success'	=> false
		);
		$newName = trim($request->getParam('newName'));
		$oldName = trim($request->getParam('oldName'));
		$helper = Mage::helper('auit_editor/skinmedia');
		if ( !$helper->isCurrentRootPath() && $newName && $oldName)
        {
        	$newFilename = $helper->getStorage()->renameFile($newName,$helper->getCurrentPath().DS.$oldName);
        	$data = $this->getFileNodeData($newFilename);
        	$data['success']= true;
        }
		return $data;
	}
	protected function getFileNodeData($filename,$rootID='')
	{
		$iSize = @getimagesize ( $filename );
		if ( $iSize == false )
		{
			$iSize = '';
		}else {
			$iSize = sprintf('%d x %d',$iSize[0],$iSize[1]);
		}
		$helper = Mage::helper('auit_editor/skinmedia');
		return array(
					'id'=>	$helper->convertPathToId($filename),
					'text' => basename($filename),
					'size' => filesize($filename),
					'isize'=> $iSize,
					'isdir' => false,
					'date_modified' => filemtime($filename),
					'icon' =>  Mage::helper('auit_editor/dirdirective')->getThumbUrl($filename), 
					'url' =>  $helper->getDonwloadUrl($filename),
					'directive' => $helper->getDirective($filename),
					'relative' => $helper->getRelative($filename),
					'relative2' => $helper->getRelative($filename,$rootID),
	                'cls'   => 'bfile',
					'dirdirective'=>false,
	                'leaf' => true,
					'root'=>false);
		
	}
	protected function deleteFile($request)
	{
		$data = array(
			'success'	=> false,
			'successful' => array(),
			'failed' => array()
		
		);
		$helper = Mage::helper('auit_editor/skinmedia');
        if ( !$helper->isCurrentRootPath() )
        {
			$message = '';
        	$delFiles = $request->getParam('files');
        	if ($delFiles && is_array($delFiles) )
        	{
        		$path = $helper->getCurrentPath();
        		foreach ( $delFiles as $recordId => $file )
        		{
					try {
						$helper->getStorage()->deleteFile($path.DS.$file);
						$data['successful'][] = array(
								'recordId' => $recordId
						);
						Mage::helper('auit_editor/dirdirective')->thumbnailDelete($path.DS.$file);
						
					} catch(Exception $e) {
						$success = false;
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					}
        		}
				$data['success'] = count($data['failed'])?false:true;
        	} 
        }
		return $data;
	}
	
}
