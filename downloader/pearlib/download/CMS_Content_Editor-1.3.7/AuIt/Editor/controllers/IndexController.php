<?php

class AuIt_Editor_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	/*
        $gif = "GIF89a\x01\x00\x01\x00\x91\x00\x00\x00\x00\x00\xff\xff\xff\xff\xff\xff\x00\x00\x00\x21\xf9\x04\x05\x14\x00\x02\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x54\x01\x00\x3b";
		$this->getResponse()->setHeader('Content-type', 'image/gif');
        $this->getResponse()->setBody($gif);
        */
    	Mage::helper('auit_editor')->setInlineEdit(1);
		$jsFiles=array(
		    'auit/extjs/ext321/adapter/ext/ext-base.js',
	    	'auit/extjs/ext321/ext-all.js');
		if ( ($_SERVER["HTTP_HOST"] == '192.168.0.18' || $_SERVER["HTTP_HOST"] == 'www.snm.lde') && !Mage::getStoreConfigFlag('dev/js/merge_files') )
		{
			$jsFiles=array(
		    	'auit/extjs/ext321/adapter/ext/ext-base-debug.js'
				,'auit/extjs/ext321/ext-all-debug.js'
				);
		} 
		$jsFiles=array_merge($jsFiles,
		array(
			Mage::helper('auit_editor')->getLangJsPath(),
			'auit/editor/core/dom.js',
			'auit/editor/core/util.js',
			'auit/editor/core/action_base.js',
			'auit/editor/editor/action_edit.js',
			'auit/editor/editor/action_custom.js',
			'auit/editor/editor/props_html.js',
			'auit/editor/editor/proxy/range.js',
  			'auit/editor/editor/proxy/selection.js',
			'auit/editor/editor/proxy/action_proxy.js',
			'auit/editor/editor/proxy/block.js',
			'auit/editor/editor/proxy/ieditbase.js',
			'auit/editor/editor/proxy/iedit.js'
		));
		$jsUrl = Mage::helper('auit_editor')->getBaseStore()->getBaseUrl('js');
		$html = '<html><head>';
		if (Mage::getStoreConfigFlag('dev/js/merge_files'))
		{
			$html .= '<script type="text/javascript" src="'.$jsUrl.'index.php/x.js?f=';
			foreach ( $jsFiles as $idx => $file )
			{
				if ( $idx > 0 ) $html .= ',';
				$html .=$file;
			}
			$html .= '"></script>';
		}else {
			foreach ( $jsFiles as $idx => $file )
			{
				$url = $jsUrl.$file;
				$html .='<script type="text/javascript" src="'.$url.'"></script>';
			}
		}
    	$html .= '</head><body></body></html>';
    	$this->getResponse()->setBody($html);
    }
}