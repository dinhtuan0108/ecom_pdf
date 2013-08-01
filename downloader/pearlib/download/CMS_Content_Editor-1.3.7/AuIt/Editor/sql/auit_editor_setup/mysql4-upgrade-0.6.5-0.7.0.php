<?php
$installer = $this;
/*
 * OLD Dirs
 * 
 */
$baseDir = Mage::getBaseDir();
$oldDirs=array('/js/auit/ext');
foreach ( $oldDirs  as $jsdir)
{ 
	$rd = $baseDir.$jsdir;
	//error_log($rd);
	if ( is_dir($rd) )
	{
	    $io = new Varien_Io_File();
	    try {
	        $io = new Varien_Io_File();
	        if ( !$io->rmdir($rd, true) )
	        {
	        	error_log("auit_editor: setup remove $rd ... failed.");
	        }
		} catch (Exception $e) {
			
	    }
	}
} 
$oldfiles=array('/js/auit/editor/fields.js');
foreach ( $oldfiles  as $file)
{ 
	$rd = $baseDir.$file;
	if ( is_file($rd) )
	{
		@unlink($rd);
	}
} 

