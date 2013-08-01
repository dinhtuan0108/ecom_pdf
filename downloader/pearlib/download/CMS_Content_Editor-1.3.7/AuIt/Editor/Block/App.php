<?php
/**
a:7:{s:18:"_1269345097383_383";a:4:{s:4:"name";s:18:"Sidebar with title";s:7:"version";s:0:"";s:2:"op";s:0:"";s:8:"template";s:38:"auit/editor/blocks/sidebar_title.phtml";}s:18:"_1269345133840_840";a:4:{s:4:"name";s:21:"Sidebar without title";s:7:"version";s:0:"";s:2:"op";s:0:"";s:8:"template";s:32:"auit/editor/blocks/sidebar.phtml";}s:18:"_1269355477489_489";a:4:{s:4:"name";s:13:"Sidebar blank";s:7:"version";s:0:"";s:2:"op";s:0:"";s:8:"template";s:38:"auit/editor/blocks/sidebar_blank.phtml";}s:18:"_1269345158647_647";a:4:{s:4:"name";s:6:"Blank ";s:7:"version";s:0:"";s:2:"op";s:0:"";s:8:"template";s:30:"auit/editor/blocks/blank.phtml";}s:18:"_1270049083215_215";a:4:{s:4:"name";s:33:"Sidebar with title (Variante 1.3)";s:7:"version";s:0:"";s:2:"op";s:0:"";s:8:"template";s:42:"auit/editor/blocks/v13/sidebar_title.phtml";}s:18:"_1270049082621_621";a:4:{s:4:"name";s:36:"Sidebar without title (Variante 1.3)";s:7:"version";s:0:"";s:2:"op";s:0:"";s:8:"template";s:36:"auit/editor/blocks/v13/sidebar.phtml";}s:17:"_1270049082010_10";a:4:{s:4:"name";s:28:"Sidebar blank (Variante 1.3)";s:7:"version";s:0:"";s:2:"op";s:0:"";s:8:"template";s:42:"auit/editor/blocks/v13/sidebar_blank.phtml";}}
 * 
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_App extends Mage_Adminhtml_Block_Template
{
    public function renderView()
    {
    	Varien_Profiler::start(__METHOD__);
        $this->setScriptPath(Mage::getConfig()->getModuleDir('etc', 'AuIt_Editor'));
		$templateName = $this->getTemplate();
		$html = $this->fetchView($templateName);
        Varien_Profiler::stop(__METHOD__);
        return $html;
    }
	public function getJsonData()
	{
		Mage::helper('auit_editor')->setInlineEdit(1);
		Mage::helper('auit_editor')->setAdminUrl($this->getRequest()->getScheme().'://'.$this->getRequest()->getHttpHost());

  		$config = new Varien_Object();

		$config->setFileBrowserRoot(Mage::helper('auit_editor/skinmedia')->TreeRoots());
		$templates=array();
		$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/templates/html');
    	if ( is_array($t) )
	    	$templates = $t;

		$styles=array();
		$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/styles/style');
    	if ( is_array($t) )
	    	$styles = $t;

		$widgets=array();
		$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/widgets');
    	if ( is_array($t) )
	    	$widgets = $t;

		$blocks=array();
		$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/blocks');
    	if ( is_array($t) )
	    	$blocks = $t;

		$menus=array();
		$t = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/menus');
    	if ( is_array($t) )
	    	$menus = $t;
	    	
        $type = $this->getRequest()->getParam('type');
        $allowed = Mage::getSingleton('auit_editor/skinmedia_storage')->getAllowedExtensions($type);
        $labels = array();
        $files = array();
        foreach ($allowed as $ext) {
            $labels[] = '.' . $ext;
            $files[] = '*.' . $ext;
        }
        //,array('AUITINLINEEDIT'=>1)
        $TreeParams = array('_current'=>true, 'id'=>null,'store'=>null);
        
        

       /*
		$homePage = Mage::getSingleton('cms/page');
		$homePage->setStoreId(1);
		$homePage->load(Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE));
        */
		$config->setValidateUrl($this->getUrl('*/*/validate'))
			->setBaseStoreId(Mage::helper('auit_editor')->getBaseStoreId())
			->setSaveHtmlUrl($this->getUrl('*/*/savehtml'))
			->setPingUrl($this->getUrl('*/*/ping'))
			->setMediaUrl(Mage::helper('auit_editor/skinmedia')->getMediaBaseUrl())
			->setLic( base64_encode(Mage::getUrl().'#'.Mage::getStoreConfig('auit_editor/editor/license')))
			->setJsUrl($this->getJsUrl())
			->setHomePageId(Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE))
			//->setHomePageId($homePage->getId())
			->setEditorCmdUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_editor/cmd',array('page_id'=>'default')))
			->setWebUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_cms/cmspage',array('page_id'=>'default')))
			->setWebCmdUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_cms/cmd',array('page_id'=>'default')))
			//			->setWebUrl(Mage::getUrl(null))
			->setUploadUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/upload', array('type' => $type)))
			->setCategoryUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_category/categoriesJson', $TreeParams))
			->setCmsTreeUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_category/cmspageJson', $TreeParams))
			->setSpecialTreeUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_category/specialJson', $TreeParams))
			->setCmsPageUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_category/cmspagecmd', $TreeParams))
			->setLinkDlgUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_category/linkDlgJson', $TreeParams))
			->setMenuUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/admin_category/menuJson', $TreeParams))
			->setFileField('aimage')
			->setTemplates($templates)
			->setStyles($styles)
			->setWidgets($widgets)
			->setBlocks($blocks)
			->setMenus($menus)
		//	->setBlocksOption($this->getBlocksOption())
//            ->setPages($this->pageJS())
            ->setFilters(array(
                'images' => array(
                    'label' => $this->helper('cms')->__('Images (%s)', implode(', ', $labels)),
                    'files' => implode(';', $files)
                )
            ));
		return Zend_Json::encode($config->getData());
	}
}
