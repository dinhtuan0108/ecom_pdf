<?php
class AuIt_Editor_Helper_Skinmedia extends Mage_Core_Helper_Abstract
{
   	const MEDIAROOT='MEDIAROOT';
//   	const CATEGORYROOT='CATEGORYROOT';
   	const SKINROOT='SKINROOT';
   	const SKINTHEMEROOT='SKINTHEMEROOT';
   	const ROOT='ROOT';
   	const UNKNOWN='UNKNOWN';
    protected $_currentPath;
    protected $_currentUrl;
    
    
    public function TreeRoots()
    {
    	$defDir = $this->correctPath( $this->getStorage()->getConfigData('upload_root') );
		$defDir = sprintf('{{media url="%s/{0}"}}', $defDir);
		return array(
					'root'=>'ROOT',
					'root_directive'=>'{{media url="{0}"}}',
//					'media'=>$this->convertPathToId($this->getMediaRoot()),
	//				'media_directive'=>$defDir,
					'preview'=>$this->convertPathToId($this->getStorageRoot().DS.'snm-portal'.DS.'preview'),
					'preview_directive'=>'{{media url="snm-portal/preview/{0}"}}',
					'preview_url'=>$this->getStorageRootUrl().'snm-portal/preview/',
					'categorie'=>$this->convertPathToId($this->getStorageRoot().DS.'catalog'.DS.'category'),
					'categorie_directive'=>'{{media url="catalog/category/{0}"}}'
				);
    }
    public function checkFolders()
    {
    	// catalog\category
    	$rootFolders=array(	$this->getMediaRoot(),
    						$this->getStorageRoot().DS.'snm-portal'.DS.'preview',
    						$this->getStorageRoot().DS.'catalog'.DS.'category');
    	foreach ( $rootFolders as $folder )
    	{
	        $io = new Varien_Io_File();
	        if (!$io->isWriteable($folder) && !$io->mkdir($folder)) 
	        {
				Mage::log(Mage::helper('cms')->__('Directory %s is not writable by server',$folder));
	            return false;
			}
    	}
    	return true;
    }
    public function getStorage()
    {
        return Mage::getSingleton('auit_editor/skinmedia_storage');
    }
    public function isCurrentRootPath()
    {
     	return $this->getStorageRoot() == $this->getCurrentPath();
    }
    public function isSubDir($dir)
    {
    	return strpos($dir,$this->getStorageRoot()) === 0 || strpos($dir,$this->getSkinRoot()) === 0;
    }
    /*
    public function buildCategoryDirective($filename)
    {
		if ( $filename && strpos($filename,'{{') === false )
		{
			$filename = '{{media url='.$this->getStorage()->getConfigData('category_root') .'/'.$filename.'}}';
		}
		return $filename;
    }
    public function getCategoryRoot()
    {
        $root = $this->correctPath( $this->getStorage()->getConfigData('category_root') );
        return Mage::getConfig()->getOptions()->getMediaDir() . DS . $root;
    }
    public function getCategoryBaseUrl()
    {
        $root = $this->correctPath( $this->getStorage()->getConfigData('category_root') );
        return Mage::getBaseUrl('media'). $root. DS;
    }
    public function getPreviewUrl()
    {
        $root = $this->correctPath( $this->getStorage()->getConfigData('preview_root') );
        return Mage::getBaseUrl('media'). $root. DS;
    }
    */
    public function getPreviewRoot()
    {
        $root = $this->correctPath( $this->getStorage()->getConfigData('preview_root') );
        return Mage::getConfig()->getOptions()->getMediaDir() . DS . $root;
    }
    public function getCatalogRoot()
    {
    	return $this->getStorageRoot().DS.'catalog'.DS.'category';
    }
    public function getMediaRoot()
    {
        $root = $this->correctPath( $this->getStorage()->getConfigData('upload_root') );
        return Mage::getConfig()->getOptions()->getMediaDir() . DS . $root;
    }
    public function getMediaBaseUrl()
    {
        $root = $this->correctPath( $this->getStorage()->getConfigData('upload_root') );
        return Mage::getBaseUrl('media'). $root. DS;
    }
    
    public function getStorageRootUrl()
    {
        return Mage::getBaseUrl('media');
    }
    
    public function getSkinBaseUrl()
    {
        return Mage::getDesign()->getSkinBaseUrl(array('_theme' => 'default','_package'=>'default'));
    }
    
    public function getSkinRoot()
    {
    	return Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default','_package'=>'default'));
    }
    public function getSkinThemeBaseUrl()
    {
        return Mage::getDesign()->getSkinBaseUrl();
    }
    
    public function getSkinThemeRoot()
    {
    	return Mage::getDesign()->getSkinBaseDir();
    }
    
    public function getRootArea($path)
    {
    	if ( strpos($path,$this->getMediaRoot()) !== false )
    		return $this->getMediaRoot();
    	if ( strpos($path,$this->getSkinThemeRoot()) !== false )
    		return $this->getSkinThemeRoot();
    	if ( strpos($path,$this->getSkinRoot()) !== false )
    		return $this->getSkinRoot();
//    	if ( strpos($path,$this->getCategoryRoot()) !== false )
  //  		return $this->getCategoryRoot();
    	return $this->getStorageRoot();
    }
    public function getRootAreaName($path)
    {
    	if ( strpos($path,$this->getMediaRoot()) !== false )
    		return self::MEDIAROOT;
    	if ( strpos($path,$this->getSkinThemeRoot()) !== false )
    		return self::SKINTHEMEROOT;
    	if ( strpos($path,$this->getSkinRoot()) !== false )
    		return self::SKINROOT;
//    	if ( strpos($path,$this->getCategoryRoot()) !== false )
//    		return self::CATEGORYROOT;
    	if ( strpos($path,$this->getStorageRoot()) !== false )
    		return self::ROOT;
    	return self::UNKNOWN;
    }
    public function getStorageRoot()
    {
    	return Mage::getConfig()->getOptions()->getMediaDir();
    }
    public function getTreeNodeName()
    {
        return 'node';
    }
    
    public function convertUrlToPathArea($url)
    {
    	$root = self::ROOT;
    	if ( strpos($url,$this->getMediaBaseUrl()) !== false )
    	{
    		return str_replace($this->getMediaBaseUrl(),self::MEDIAROOT.DS, $url);
    	}
    	if ( strpos($url,$this->getSkinThemeBaseUrl()) !== false )
    	{
    		return str_replace($this->getSkinThemeBaseUrl(),self::SKINTHEMEROOT.DS, $url);
    	}
    	if ( strpos($url,$this->getSkinBaseUrl()) !== false )
    	{
    		return str_replace($this->getSkinBaseUrl(),self::SKINROOT.DS, $url);
    	}
/*    	if ( strpos($url,$this->getCategoryBaseUrl()) !== false )
    	{
    		return str_replace($this->getCategoryBaseUrl(),self::CATEGORYROOT.DS, $url);
    	}
    	*/
    	if ( strpos($url,$this->getStorageRootUrl()) !== false )
    	{
    		return str_replace($this->getStorageRootUrl(),self::ROOT.DS, $url);
    	}
    	return '';
    }
    public function convertPathToArea($path)
    {
        return str_replace($this->getRootArea($path), $this->getRootAreaName($path), $path);
    }
    public function convertPathToId($path)
    {
    	return Mage::helper('core')->urlEncode($this->convertPathToArea($path));
    }
    public function convertIdToPath($id,$encode=true)
    {
    	switch ( $id )
    	{
    		case self::ROOT:
//    		case 'AUITFBR':
    			return $this->getStorageRoot();
    			break;
    		case self::MEDIAROOT:
    			return $this->getMediaRoot();
    			break;
    		case self::SKINTHEMEROOT:
    			return $this->getSkinThemeRoot();
    			break;
    		case self::SKINROOT:
    			return $this->getSkinRoot();
    			break;
//    		case self::CATEGORYROOT:
//    			return $this->getCategoryRoot();
//    			break;
    	}
        $path = $encode?Mage::helper('core')->urlDecode($id):$id;
        $fp = explode(DS,$path);
        switch (array_shift($fp))
        {
    		case self::MEDIAROOT:
    			$path = implode(DS,$fp);
	            $path = $this->getMediaRoot() .DS. $path;
    			break;
    		case self::SKINTHEMEROOT:
    			$path = implode(DS,$fp);
	            $path = $this->getSkinThemeRoot() .DS. $path;
    			break;
    		case self::SKINROOT:
    			$path = implode(DS,$fp);
	            $path = $this->getSkinRoot() .DS. $path;
    			break;
//    		case self::CATEGORYROOT:
//    			$path = implode(DS,$fp);
//	            $path = $this->getCategoryRoot() .DS. $path;
//    			break;
    		case self::ROOT:
    			$path = implode(DS,$fp);
	            $path = $this->getStorageRoot() .DS. $path;
    			break;
    		default:
		        if (!strstr($path, $this->getStorageRoot())) {
		            $path = $this->getStorageRoot() . $path;
		        }
    			break;
        }
        return $path;
    }
    public function correctPath($path, $trim = true)
    {
        $path = strtr($path, "\\\/", DS . DS);
        if ($trim) {
            $path = trim($path, DS);
        }
        return $path;
    }
    public function convertPathToUrl($path)
    {
        return str_replace(DS, '/', $path);
    }
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $currentPath = $this->getStorageRoot();
            $path = $this->_getRequest()->getParam($this->getTreeNodeName());
            if ($path) {
                $path = $this->convertIdToPath($path);
                if (is_dir($path)) {
                    $currentPath = $path;
                }
                else if (is_file($path) && is_dir(dirname($path))) {
                	$currentPath = dirname($path);
                }
            }
            $io = new Varien_Io_File();
            if (!$io->isWriteable($currentPath) && !$io->mkdir($currentPath)) {
                Mage::log(Mage::helper('cms')->__('Directory %s is not writable by server',$currentPath));
            }
            $this->_currentPath = $currentPath;
        }
        return $this->_currentPath;
    }

    public function getUrl($path)
    {
    	$RelPath = str_replace($this->getRootArea($path).DS, '', $path);
    	switch ( $this->getRootAreaName($path) ) 
    	{
    		case self::SKINTHEMEROOT:
    			$path =  $this->getSkinThemeBaseUrl().$RelPath;
    			break;
    		case self::SKINROOT:
    			$path =  $this->getSkinBaseUrl().$RelPath;
    			break;
    		case self::MEDIAROOT:
    			$path = $this->getMediaBaseUrl().$RelPath;
    			break;
//    		case self::CATEGORYROOT:
//    			$path = $this->getCategoryBaseUrl().$RelPath;
//    			break;
    		case self::ROOT:
    			$path = Mage::getBaseUrl('media').$RelPath;
    			break;
    		default:
    			$path = '';
    			break;
    	}
    	return $path;
    }
    public function getDonwloadUrl($path)
    {
    	return $this->getUrl($path);
    }
    
    public function getRelative($path,$root='')
    {
    	if ( !$root )
    		$root= $this->getRootArea($path);
    	return str_replace($root.DS, '', $path);
    }
    public function getDirective($path,$quote='"')
    {
    	$root = $this->getRootAreaName($path);
    	$path = str_replace($this->getRootArea($path).DS, '', $path);
    	switch ( $root ) 
    	{
    		case self::SKINTHEMEROOT:
    			return  sprintf('{{skin url='.$quote.'%s'.$quote.'}}', $path);
    			break;
    		case self::SKINROOT:
    			return  sprintf('{{skin url='.$quote.'%s'.$quote.'}}', $path);
    			break;
    		case self::MEDIAROOT:
    			$root = $this->correctPath( $this->getStorage()->getConfigData('upload_root') );
    			return  sprintf('{{media url='.$quote.'%s/%s'.$quote.'}}', $root,$path);
    			break;
//    		case self::CATEGORYROOT:
//    			$root = $this->correctPath( $this->getStorage()->getConfigData('category_root') );
//    			return  sprintf('{{media url="%s/%s"}}', $root,$path);
//    			break;
    		case self::ROOT:
    			return  sprintf('{{media url='.$quote.'%s'.$quote.'}}', $path);
    			break;
    	}
    	return $path;
    }
    
}
