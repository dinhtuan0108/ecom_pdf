<?php
class AuIt_Editor_Helper_Skinmedia_GD2 extends Varien_Image_Adapter_Gd2
{
    public function createDummy($filename,$w=1,$h=1)
    {
        $this->_fileName = $filename;
        $this->getMimeType();
        $this->_getFileAttributes();
        $this->_imageHandler = @ImageCreate ($w,$h);
    }
}
class AuIt_Editor_Gd2 extends Varien_Image_Adapter_Gd2
{
    public function crop2($x=0, $y=0, $width=0, $height=0)
    {
        if( $x == 0 && $y == 0 && $width == 0 && $height == 0 ) {
            return;
        }
        $canvas = imagecreatetruecolor($width, $height);
        if ($this->_fileType == IMAGETYPE_PNG) {
            $this->_saveAlpha($canvas);
        }
        if ( ImageCopy ($canvas, $this->_imageHandler, 0, 0, $x,$y, $width,$height)) //$newWidth, $newHeight, $this->_imageSrcWidth, $this->_imageSrcHeight) )
        {
        	@imagedestroy($this->_imageHandler);
        	$this->_imageHandler = $canvas;
        	
        }
        $this->rotate(0);
        // Ist privet $this->refreshImageDimensions();
    }
	function flip ( $mode )
	{
	
	    $width                        =    $this->_imageSrcWidth;
	    $height                       =    $this->_imageSrcHeight;
	    $src_x                        =    0;
	    $src_y                        =    0;
	    $src_width                    =    $width;
	    $src_height                   =    $height;
	
	    switch ( $mode )
	    {
	
	        case '1': //vertical
	            $src_y                =    $height -1;
	            $src_height           =    -$height;
	        break;
	
	        case '2': //horizontal
	            $src_x                =    $width -1;
	            $src_width            =    -$width;
	        break;
	
	        case '3': //both
	            $src_x                =    $width -1;
	            $src_y                =    $height -1;
	            $src_width            =    -$width;
	            $src_height           =    -$height;
	        break;
	
	        default:
	            return $imgsrc;
	
	    }
	    $canvas  =    imagecreatetruecolor ( $width, $height );
	    imagecopyresampled ( $canvas, $this->_imageHandler, 0, 0, $src_x, $src_y , $width, $height, $src_width, $src_height );
        $this->_imageHandler = $canvas;
        $this->refreshImageDimensions2();
	    	
	}
    private function refreshImageDimensions2()
    {
        $this->_imageSrcWidth = imagesx($this->_imageHandler);
        $this->_imageSrcHeight = imagesy($this->_imageHandler);
    }
	
}
class AuIt_Editor_Helper_Dirdirective  extends Mage_Core_Helper_Abstract
{
	public function checkDefaults()
	{
		$previewFolder = Mage::helper('auit_editor/skinmedia')->getStorageRoot().DS.'snm-portal'.DS.'preview';
		if ( !$this->getDirectiveFor($previewFolder,true) )
		{
			$fromFile = Mage::getConfig()->getModuleDir('etc', 'AuIt_Editor');
			@copy(	$fromFile.DS.'dir.directive.xml',$this->getDirectiveFilename($previewFolder));
		}
	}
	public function getDirectiveFilename($dir)
	{
		if ( !Mage::helper('auit_editor/skinmedia')->isSubDir($dir) )
			return false;
	   	return $dir.DS.'.directive.xml'; 
	}
	public function getDirectiveFor($dir,$bexact=false)
	{
		do {
			if ( !Mage::helper('auit_editor/skinmedia')->isSubDir($dir) )
				return false;
	    	$directive = $dir.DS.'.directive.xml'; 
    		if ( file_exists($directive) )
				return $directive;
			if ( $bexact )
				return false;	
			$dir = dirname($dir);
		}while (true);
	}
    public function execute($filename,$mode='upload')
    {
    	$dir = dirname($filename);
    	$directive = $this->getDirectiveFor($dir);
    	if ( !$directive )
    		return;
    	/**
    	$directive = $dir.DS.'.directive.xml'; 
    	if ( !file_exists($directive) )
    		return;
    		*/
		$xml = new DOMDocument();
		$xml->preserveWhiteSpace =false;
		if ( !@$xml->load($directive) )
		{
			Mage::log("Can't load file $filename");
			return;
		}
		$params=array();
		$params['FULLNAME']=$filename;
		$params['DIR']=$dir;
		$params['FILENAME']=basename($filename);
		return $this->executeDirective($xml,$mode,$params);
    }	
    public function executeDirectiveString($xmlString,$target,&$params)
    {
		$xml = new DOMDocument();
		$xml->preserveWhiteSpace =false;
		if ( !@$xml->load($xmlString) )
		{
			Mage::log("Can't load xmlstring $xmlString");
			return false;
		}
    	return $this->executeDirective($xml,$mode,$params);
    }
    public function executeDirective(DOMDocument $xml,$target,&$params)
    {
		$xpath = new DOMXPath($xml);
    	$target = $xpath->query('//target[@name="'.$target.'"]');
		try {
			foreach ($target as $entry) 
			{
				foreach ( $entry->childNodes as $node )
				{
					$tageName = $node->nodeName;
					switch ($tageName)
					{
						case 'transform':
							$this->transformDirective($node,$params);
							break;
						case 'copy':
							$this->copyDirective($node,$params);
							break;
						case 'mkdir':
							$this->mkdirDirective($node,$params);
							break;
						case 'load':
							$this->loadDirective($node,$params);
							break;
						case 'replacefile':
							$this->replacefileDirective($node,$params);
							break;
					} 			
				}
				break;
			}
		}
		catch (Exception  $e )
		{
			Mage::log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			return false;
		}
		return true;
    }
    protected function createDirectory($newdir)
    {
        $io = new Varien_Io_File();
        if (!$io->isWriteable($newdir) && !$io->mkdir($newdir)) 
        {
        	Mage::throwException(Mage::helper('cms')->__('Directory %s is not writable by server',$newdir));
		}
    }
    protected function getTempDir()
    {
    	return Mage::getConfig()->getOptions()->getMediaDir().DS.'tmp'.DS.'snm-portal';
    }
    protected function cleanDir($tmpDirectory,$prefix='')
    {
    	if ( !is_dir($tmpDirectory))
    		return;
    	$yesterday = time() - 24*3600;
    	$io = new Varien_Io_File();
    	try {
        	$io->checkAndCreateFolder($tmpDirectory);
            $io->open(array('path'=>$tmpDirectory));
            $del=array();
	        if ($dh = opendir($tmpDirectory)) {
	            while (($entry = readdir($dh)) !== false) {
	                $fullpath = $tmpDirectory . DIRECTORY_SEPARATOR . $entry;
	                if( !is_dir($fullpath) ) {
                    	continue;
	                }
	                if ( !$prefix || substr($entry,0,strlen($prefix))==$prefix)
	                {
                		if ( filectime($fullpath) < $yesterday )
                		{
            				$del[]=$fullpath;   		
	                	}  
	            	}
	            }
	        }
	        foreach ( $del as $entry)
	        {
	        	$io->rmdir($entry, true);
	        }
            
        } catch (Exception $e) {
        	Mage::log($e->getMessage());
        }
    }
    static public function getNewFileName($destFile)
    {
        $fileInfo = pathinfo($destFile);
        if( file_exists($destFile) ) {
            $index = 1;
            $baseName = $fileInfo['filename'] . '.' . $fileInfo['extension'];
            while( file_exists($fileInfo['dirname'] . DIRECTORY_SEPARATOR . $baseName) ) {
                $baseName = $fileInfo['filename']. '_' . $index . '.' . $fileInfo['extension'];
                $index ++;
            }
            $destFileName = $baseName;
        } else {
            return $fileInfo['basename'];
        }

        return $destFileName;
    }
    protected function loadDirective(DOMElement $node,array &$params)
    {
    	$from = $node->getAttribute('from');
    	if ( !$from )
    		return;
    	$to = $node->getAttribute('to');
    	if ( !$to )
    	{
    		$tmpDir = $this->getTempDir();
    		$this->cleanDir($tmpDir,'snm-portal-');
			$to = $tmpDir.DS.'snm-portal-'.session_id();
    	}	
    	$this->createDirectory($to);
    	$toFile = $to.DS.basename($from);
    	$toFile = $to.DS.self::getNewFileName($toFile);
    	file_put_contents($toFile,file_get_contents($from));
    	$params['FULLNAME']=$toFile;
		$params['DIR']=$to;
		$params['FILENAME']=basename($toFile);
    }
    protected function replacefileDirective(DOMElement $node,array &$params)
    {
    	$helper= Mage::helper('auit_editor/skinmedia');
    	$from = $node->getAttribute('from');
    	$from =  $helper->convertIdToPath($helper->convertUrlToPathArea($from),false);
    	if ( !$from || !file_exists($from))
    		return;
    	$to = $node->getAttribute('to');
    	$to =  $helper->convertIdToPath($helper->convertUrlToPathArea($to),false);
    	if ( !$to || !file_exists($to))
    	{
    		return;
    	}
    	$this->execute($to,'replace');
    	$bok = @copy( $from , $to );
        if (!$bok) {
            Mage::throwException(Mage::helper('cms')->__('replacefileDirective: can\'t copy from "%s" to "%s"', $from , $to));
        }
        $this->thumbnailDelete($to);
    	$params['FULLNAME']=$to;
		$params['DIR']=dirname($to);
		$params['FILENAME']=basename($to);
    }
    protected function mkdirDirective(DOMElement $node,array $params)
    {
    	$subdir = $node->getAttribute('dir');
    	if ( !$subdir )
    		return;
    	$newdir = $params['DIR'].DS.$subdir;
    	$this->createDirectory($newdir);
    }
    protected function copyDirective(DOMNode $node,array $params)
    {
    	$newFile = $params['DIR'];
    	$subdir = $node->getAttribute('todir');
    	if ( $subdir )
	    	$newFile .= DS.$subdir;
	    $newFile .= DS.$params['FILENAME'];
	    $newdir = dirname($newFile);
		$file = $params['FULLNAME'];
        $ioAdapter = new Varien_Io_File();
        if (!$ioAdapter->fileExists($file)) {
            Mage::throwException(Mage::helper('cms')->__('File "%s" don\'t exist', $file));
        }
        $ioAdapter->setAllowCreateFolders(true);
        $ioAdapter->createDestinationDir($newdir);
        return $ioAdapter->cp($file,$newFile);
    }
    protected function transformDirective(DOMNode $transNode,array $params)
    {
    	$filename = $params['FULLNAME'];
		$image = new AuIt_Editor_Gd2();
    	$image->open($filename);
    	foreach ( $transNode->childNodes as $node )
		{
			$tageName = $node->nodeName;
			switch ($tageName)
			{
				case 'resize':
					$this->transformResize($image,$node,$params);
					break;
				case 'rotate':
					$this->transformRotate($image,$node,$params);
					break;
				case 'crop':
					$this->transformCrop($image,$node,$params);
					break;
				case 'fliph':
					$this->transformFlipH($image,$node,$params);
					break;
				case 'flipv':
					$this->transformFlipV($image,$node,$params);
					break;
				case 'watermark':
					$this->transformWatermark($image,$node,$params);
					break;
			} 			
		}
		if ( $transNode->getAttribute('tofile') )
			$image->save($transNode->getAttribute('tofile'));
		else
			$image->save($filename);
    }
    protected function getBoolValue($value)
    {
    	$value = strtolower(trim($value));
    	if ( $value == 'true' or $value > 0 )	return true;
    	return false; 
    }
    protected function getColorValue($value)
    {
    	$rgb=array();
    	$rgb[0] = hexdec(substr($value,1,2));
    	$rgb[1] = hexdec(substr($value,3,2));
    	$rgb[2] = hexdec(substr($value,5,2));
    	return $rgb; 
    }
    protected function transformResize(Varien_Image_Adapter_Abstract $image,DOMNode $node,array $params)
    {
    	/*
			<resize width="{{config path='auit_editor/wysiwyg/browser_resize_width'}}"
					height="{{config path='auit_editor/wysiwyg/browser_resize_height'}}"
					keepAspectRatio="true" keepFrame="true"
					backgroundColor="#FF0000"
					keepTransparency="true"
					constrainOnly="true"
					/>
    	*/
    	
    	if ( $node->hasAttribute('keepAspectRatio') )
    		$image->keepAspectRatio($this->getBoolValue($node->getAttribute('keepAspectRatio')));
    	if ( $node->hasAttribute('keepFrame') )
    		$image->keepFrame($this->getBoolValue($node->getAttribute('keepFrame')));
    	if ( $node->hasAttribute('keepTransparency') )
    		$image->keepTransparency($this->getBoolValue($node->getAttribute('keepTransparency')));
    	if ( $node->hasAttribute('constrainOnly') )
    		$image->constrainOnly($this->getBoolValue($node->getAttribute('constrainOnly')));
    	if ( $node->hasAttribute('backgroundColor') )
    		$image->backgroundColor($this->getColorValue($node->getAttribute('backgroundColor')));
    		
    	$frameWidth = null;
    	$frameHeight= null;
    	
    	if ( $node->hasAttribute('width') )
    	{
    		$frameWidth = $node->getAttribute('width');
    	}
    	if ( $node->hasAttribute('height') )
    	{
    		$frameHeight = $node->getAttribute('height');
    	}
    	$image->resize($frameWidth, $frameHeight);
    }
    protected function transformRotate(Varien_Image_Adapter_Abstract $image,DOMNode $node,array $params)
    {
    	/*<rotate angle="45"/>*/
    	if ( $node->hasAttribute('angle') )
    	{
    		$angle = (int)$node->getAttribute('angle');
    		$image->rotate($angle);
    	}
    }
    protected function transformCrop(Varien_Image_Adapter_Abstract $image,DOMNode $node,array $params)
    {
    	/* <crop top="0" bottom="0" right="0" left="0"/>*/
    	$top="0"; $width="0"; $height="0"; $left="0";
    	if ( $node->hasAttribute('top') )
    		$top = $node->getAttribute('top');
    	if ( $node->hasAttribute('width') )
    		$width = $node->getAttribute('width');
    	if ( $node->hasAttribute('height') )
    		$height = $node->getAttribute('height');
    	if ( $node->hasAttribute('left') )
    		$left = $node->getAttribute('left');
		$image->crop2($left,$top,$width,$height);
    }
    protected function transformWatermark(Varien_Image_Adapter_Abstract $image,DOMNode $node,array $params)
    {
    	if ( $node->hasAttribute('image') )
    	{
    		$file =$node->getAttribute('image');
    		$watermarkImage = Mage::helper('auit_editor/skinmedia')->getMediaRoot().DS.$file;
    		if ( file_exists($watermarkImage) )
    		{
	    		$positionX=0;$positionY=0; $watermarkImageOpacity=30; $repeat=false;
	    		if ( $node->hasAttribute('repeat') )
	    			$repeat = $this->getBoolValue($node->getAttribute('repeat'));
	    		if ( $node->hasAttribute('positionX') )
	    			$positionX = (int)$node->getAttribute('positionX');
	    		if ( $node->hasAttribute('positionY') )
	    			$positionY = (int)$node->getAttribute('positionY');
	    		if ( $node->hasAttribute('watermarkImageOpacity') )
	    			$watermarkImageOpacity = (int)$node->getAttribute('watermarkImageOpacity');
	    			
	    		$image->watermark($watermarkImage, $positionX, $positionY, $watermarkImageOpacity, $repeat);
    		}	
    	}
    	/*	<watermark image="pfad" positionX="0" positionY="0" watermarkImageOpacity="30" repeat="false"/>*/
    	
    }
    protected function transformFlipH(Varien_Image_Adapter_Abstract $image,DOMNode $node,array $params)
    {
   		$image->flip(2);
    }
	protected function transformFlipV(Varien_Image_Adapter_Abstract $image,DOMNode $node,array $params)
    {
   		$image->flip(1);
	}
	public function thumbnailDelete($filename)
	{
		
		$thum = $this->getThumbPath($filename);
		if ( file_exists($thum))
		{
			@unlink($thum);
		}
	}
    public function getThumbPath($filename)
    {
		$helper = Mage::helper('auit_editor/skinmedia');
    	$path = $filename;
    	$rootPath = $helper->getRootArea($path);
    	$AreaName = $helper->getRootAreaName($path);
    	if ( $AreaName == AuIt_Editor_Helper_Skinmedia::SKINROOT )
    		$rootPath = Mage::getBaseDir('skin');
    	$directory = Mage::getBaseDir('media') . DS.'catalog'.DS.'product'.DS.'cache'.DS.'thumbs'.DS.$AreaName;
    	$thumbFile = str_replace($rootPath, $directory, $path);
    	return $thumbFile;
    }
	
    public function getThumbUrl($filename)
    {
    	$thumbFile = $this->getThumbPath($filename);
    	if( !is_file($thumbFile)) {
    		$this->createThumbnail($filename,$thumbFile);
    	}
    	if( is_file($thumbFile)) {
			return str_replace(Mage::getBaseDir('media').DS,Mage::getBaseUrl('media'), $thumbFile);
		}
    	return '';
    }
    public function createThumbnail($filename,$thumbFile)
    {
        $thumbsPath = dirname($thumbFile);
        $io = new Varien_Io_File();
        if ($io->isWriteable($thumbsPath)) {
            $io->mkdir($thumbsPath);
        }
        try {
	        $image = Varien_Image_Adapter::factory('GD2');
	        $image->keepAspectRatio(true);
	        $image->constrainOnly(true);
	        $image->keepTransparency(true);
	        $image->backgroundColor(array(255,255,255));
	        $image->open($filename);
	        //$image->keepFrame(true);
	        $width = Mage::getStoreConfig('auit_editor/wysiwyg/browser_resize_width');
	        $height = Mage::getStoreConfig('auit_editor/wysiwyg/browser_resize_height');
	        $image->resize($width, $height);
	        $image->save($thumbFile);
        }
        catch (Exception $e)
        {
        	Mage::log("createThumbnail($filename) failed (create dummy): ".$e->getMessage());
        	try {
        		$image = new AuIt_Editor_Helper_Skinmedia_GD2();
        		$image->createDummy($filename);
	        	$image->save($thumbFile);
        	}
	        catch (Exception $e)
	        {
	        }        		
        }
    }
	
}