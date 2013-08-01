<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Admin_EditorController extends Mage_Adminhtml_Controller_Action
{
	public function preDispatch()
    {
        parent::preDispatch();
    }
	protected function _initAction() {
		$this->getStorage();
		return $this;
	}   
	public function pingAction() {
		Mage::helper('auit_editor')->setInlineEdit(1);
		$this->getResponse()->setBody('ok');
	}
	public function indexAction() {
		$this->_initAction();
		$this->loadLayout()->_setActiveMenu('cms/auit_editor');
		$this->getLayout()->getBlock('head')->addItem('js_css','auit/editor/css/frame.css');
		$this->getLayout()->getBlock('head')->addJs('auit/editor/core/loader.js');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(false);
		if ( $this->getLayout()->getBlock('root') )
			$this->getLayout()->getBlock('root')->unsetChild('footer');
		$this->getLayout()->getBlock('before_body_end')->append($this->getLayout()->createBlock('auit_editor/frame'));
		Mage::helper('auit_editor/skinmedia')->checkFolders();
		Mage::helper('auit_editor/dirdirective')->checkDefaults();
		$this->renderLayout();
	}   
	public function appAction() {
		Mage::helper('auit_editor')->setAdminLocaleCode(Mage::app()->getLocale()->getLocaleCode());
        $this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('auit_editor/app')
            ->setTemplate('app.phtml')
            ->toHtml());
	}
	public function validateAction() {
		$this->_initAction();
		$rData = array();
		if ($data = $this->getRequest()->getPost()) {
			Mage::getDesign()->setArea('frontend');
			switch ($data['action'])
			{
				case 'get-files':
				case 'get-folders':
				case 'create-folder':
				case 'rename-folder':
				case 'delete-folder':
				case 'rename-file':
				case 'delete-file':
				case 'load-directive':
				case 'save-directive':
				case 'delete-directive':
				case 'navto-directive':
				case 'imageedit':
					$processor = Mage::getModel('auit_editor/filebrowser');
					$rData = $processor->handleRequest($this->getRequest());
					break;
				case 'download-file':
				break;
			}
		}else {
			$action = $this->getRequest()->getParam('action');
			switch ($action)
			{
				case 'download-file':
					$nodeid = $this->getRequest()->getParam('nodeid');
					$helper = Mage::helper('auit_editor/skinmedia');
					$path = $helper->convertIdToPath($nodeid);
					if ( file_exists($path ) )
					{
						$this->getResponse()
			            ->setHttpResponseCode(200)
			            ->setHeader('Pragma', 'public', true)
			            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
			            ->setHeader('Content-type', 'application/octet-stream', true)
						->setHeader('Content-Length', filesize($path))
						->setHeader('Content-Disposition', 'attachment; filename="'.basename($path).'"');
		                $this->getResponse()->clearBody();
		                $this->getResponse()->sendHeaders();
		                $ioAdapter = new Varien_Io_File();
		                $ioAdapter->open(array('path' => $ioAdapter->dirname($path)));
		                $ioAdapter->streamOpen($path, 'r');
		                while ($buffer = $ioAdapter->streamRead()) {
		                    print $buffer;
		                }
		                $ioAdapter->streamClose();
		                if (!empty($content['rm'])) {
		                    $ioAdapter->rm($file);
		                }
		                return $this;
					}
				break;
			}
			exit();
			
		}
		
		$this->getResponse()->setBody(Zend_Json::encode($rData));
	}
	public function uploadAction() {

		$this->_initAction();
		$rData = array();
		if ($data = $this->getRequest()->getPost()) {
			$processor = Mage::getModel('auit_editor/filebrowser');
			$rData = $processor->uploadFiles($this->getRequest());
		}
		$this->getResponse()->setBody(Zend_Json::encode($rData));
	}
	public function savehtmlAction() {
		
		$this->_initAction();
		$rData = array();
		$rData['success']=true;
		if ($data = $this->getRequest()->getPost()) {
			$data = Zend_Json::decode($data['data']);
			if ( $data && is_array($data))
			{
				$content = Mage::getModel('auit_editor/content');
				if ( count($data) > 0 )
				{
					//$rData['success']=false;
					try {
						$content->importContent($rData,$data,$this->getRequest());
		            } catch (Exception $e) {
		            	$this->_getSession()->addError($e->getMessage());
		            	$rData['success']=false;
		            }
						
				}
			}
		}
		Mage::helper('auit_editor')->addMessages($rData);
		$this->getResponse()->setBody(Zend_Json::encode($rData));
	}
    protected function getStorage()
    {
        if (!Mage::registry('storage')) {
            Mage::register('storage', Mage::helper('auit_editor/skinmedia')->getStorage());
        }
        return Mage::registry('storage');
    }
	public function cmdAction() {
		$action = $this->getRequest()->getParam('action');
		switch ($action)
		{
			case 'widget-get-list':
				$this->getResponse()->setBody(Zend_Json::encode($this->getWidgetList()));
				break;
			case 'widget-frame':
				$this->loadLayout('popup');
				if ( $this->getLayout()->getBlock('root') )
				{
					$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
					$this->getLayout()->getBlock('head')->addItem('js_css','prototype/windows/themes/default.css');
					$this->getLayout()->getBlock('head')->addItem('js_css','prototype/windows/themes/magento.css');
					if ( Mage::getVersion() >= 1.4 )
					{
						$this->getLayout()->getBlock('head')->addJs('mage/adminhtml/wysiwyg/widget.js');
					}
					$newRoot = $this->getLayout()->createBlock('auit_editor/adminhtml_widgetFrame');
					$newRoot->setTemplate('widgetframe.phtml');
					$newRoot->importBlock($this->getLayout()->getBlock('root'));
					$this->getLayout()->setBlock('root', $newRoot);
				}
				$this->renderLayout();
				break;
				
		}
	}    
	protected function getWidgetList()
	{
		$rData=array();
		if ( Mage::getVersion() < 1.4 )
			return $rData;
		$allWidgets = Mage::getModel('widget/widget');
		if ($allWidgets )
		{
			$allWidgets=$allWidgets->getWidgetsArray();
			foreach ($allWidgets as $widget) {
				$widget['id']=$widget['name'];
				$widget['text']=$widget['name'];
				$widget['leaf']= true;
				$rData[]=$widget;
			}
		}
		return $rData;

	}
}
