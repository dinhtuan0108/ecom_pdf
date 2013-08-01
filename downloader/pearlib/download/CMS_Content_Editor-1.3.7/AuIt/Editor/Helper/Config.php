<?php
class AuIt_Editor_Helper_Config extends Mage_Core_Helper_Abstract
{
	public function checkWidgets($v)
	{
		if ( Mage::getVersion() < 1.4 )
			return $v;
		$widgets = Mage::getModel('widget/widget');
		if ( !$widgets )
			return $v;
			
		$archiv=array('cms/widget_page_link','catalog/category_widget_link','catalog/product_widget_link');
		$allWidgets = $widgets->getWidgetsArray();

		$cw=array();
		foreach ($allWidgets as $widget) {
			$cw[$widget['type']]=1;
			if ( !isset($v[$widget['type']]) )
			{
				$w = $widgets->getXmlElementByType($widget['type']);
				$prev='';
				if ( $w->snm_editor && $w->snm_editor->preview )
					$prev=(string)$w->snm_editor->preview;
				$group = in_array($widget['type'],$archiv)?'Trash':'New';
				if ( $w->snm_editor && $w->snm_editor->group )
					$group=(string)$w->snm_editor->group;
				
				$v[$widget['type']]= array (
	    			'name' => $widget['name'],
	    			'menu' => $group,
	    			'image' => $prev,
	    			'description' => $widget['description'],
	    			'template' => '',
	    			'key' => $widget['type'],
					'code'=> $widget['code'],
					'classname'=> 'widget'
	  			);
			}
		}
		foreach ($v as $type => $widget) {
			if ( !isset($cw[$type]))
				unset($v[$type]);
		}
		return $v;
	}
	public function getDefaults($key)
	{
		$v=array();
		switch ($key)
		{
			case 'auit_editor/templates/html':
$v=array (
  1267452370571 => 
  array (
    'name' => 'Shadow box',
    'menu' => 'Shadows',
    'image' => 'shadowbox.jpg',
    'description' => 'Float shadow box for images.',
    'template' => '<div class="sample-shadow">
  <div>
    <p><img src="{{skin url=\'images/catalog/product/placeholder/thumbnail.jpg\'}}" alt=""/></p>
  </div>
</div>
',
    'key' => '1267452370571',
    'type' => 1,
  ),
  1267454426828 => 
  array (
    'name' => 'Shdow Box - for text',
    'menu' => 'Shadows',
    'image' => 'shadowtext.jpg',
    'description' => 'Use this for textbox',
    'template' => '<div class="sample-shadow clearfix" style="clear:both;width:100%">
  <div>
    <p>The content ...</p>
  </div>
</div>
',
    'key' => '1267454426828',
    'type' => 0,
  ),
  1267455356057 => 
  array (
    'name' => 'Clear ',
    'menu' => 'Helper',
    'image' => '',
    'description' => 'Helper div - clear both.',
    'template' => '<div style="clear:both;font-size:0px;height:0;line-height:0;overflow:hidden">x</div>
',
    'key' => '1267455356057',
    'type' => 0,
  ),
  1267457566003 => 
  array (
    'name' => 'Sample I',
    'menu' => 'Article',
    'image' => 'article.jpg',
    'description' => 'Simple articel with image, headline and textfield.',
    'template' => '<div style="clear:both;margin-bottom:12px;padding:6px 0px;border-bottom:1px solid #efefef" class="clearfix">
<img height="135" width="135" align="left" alt="" style="margin-right: 10px;"
src="{{skin url="images/catalog/product/placeholder/small_image.jpg"}}"/>
<h3>Title here</h3>
<p>Content here</p>
</div>
',
    'key' => '1267457566003',
    'type' => 1,
  ),
  1267458790493 => 
  array (
    'name' => 'Popular Tags',
    'menu' => 'Mangento Blocks',
    'image' => '',
    'description' => 'Insert magento inline block.',
    'template' => '{{block type="tag/popular" template="tag/popular.phtml"}}

',
    'key' => '1267458790493',
    'type' => 1,
  ),
);
				break;
			case 'auit_editor/attributes/product_attributes':
				$v=array();
				$v[uniqid()]=array(
    					'name'=>'description',
    					'type'=>1);
				$v[uniqid()]=array(
    					'name'=>'short_description',
    					'type'=>1);
				break;
			case 'auit_editor/attributes/catalog_attributes':
				$v=array();
				$v[uniqid()]=array(
    					'name'=>'description',
    					'type'=>1);
				break;
			case 'auit_editor/styles/style':
				$v=array (
  1267451360743 => 
  array (
    'name' => 'Polaroid - black',
    'menu' => 'Frame',
    'image' => '',
    'description' => 'To look like Polaroid pictures',
    'classname' => '',
    'style' => 'border: 1px solid rgb(102, 102, 102); padding: 10px 10px 30px; background-color: rgb(51, 51, 51);',
    'key' => '1267451360743',
  ),
  1267451733384 => 
  array (
    'name' => ' Polaroid - white',
    'menu' => 'Frame',
    'image' => '',
    'description' => 'To look like Polaroid pictures',
    'classname' => '',
    'style' => 'border: 1px solid rgb(102, 102, 102); padding: 10px 10px 30px; background-color: #ffffff;',
    'key' => '1267451733384',
  ),
  1267451766622 => 
  array (
    'name' => 'Simple gray box',
    'menu' => 'Box',
    'image' => '',
    'description' => '',
    'classname' => '',
    'style' => 'border: 2px solid rgb(220, 220, 220);',
    'key' => '1267451766622',
  ),
  1267457048626 => 
  array (
    'name' => 'Set to 100%',
    'menu' => 'Dimension',
    'image' => '',
    'description' => 'Set block elements to 100% width',
    'classname' => '',
    'style' => 'width:100%',
    'key' => '1267457048626',
  ),
);				break;
		case 'auit_editor/blocksdefinition/reference':
$v=array (
  '_1269344951477_477' => 
  array (
    'name' => 'Left Column',
    'version' => '',
    'op' => '',
    'reference' => 'left',
  ),
  '_1269344966133_133' => 
  array (
    'name' => 'Main Content Area',
    'version' => '',
    'op' => '',
    'reference' => 'content',
  ),
  '_1269344965660_660' => 
  array (
    'name' => 'Right Column',
    'version' => '',
    'op' => '',
    'reference' => 'right',
  ),
  '_1269344965227_227' => 
  array (
    'name' => 'Page Header',
    'version' => '1.4',
    'op' => '1',
    'reference' => 'top.container',
  ),
  '_1269344964811_811' => 
  array (
    'name' => 'Page Footer',
    'version' => '1.4',
    'op' => '1',
    'reference' => 'bottom.container',
  ),
  '_1269345023666_666' => 
  array (
    'name' => 'Page Header',
    'version' => '1.3',
    'op' => '',
    'reference' => 'top.menu',
  ),
  '_1269345023064_64' => 
  array (
    'name' => 'Page Footer',
    'version' => '1.3',
    'op' => '',
    'reference' => 'before_body_end',
  ),
);
		  	break;		
		case 'auit_editor/blocksdefinition/templates':
$v=
array (
  '_1269345097383_383' => 
  array (
    'name' => 'Sidebar with title',
    'version' => '',
    'op' => '',
    'template' => 'auit/editor/blocks/sidebar_title.phtml',
  ),
  '_1269345133840_840' => 
  array (
    'name' => 'Sidebar without title',
    'version' => '',
    'op' => '',
    'template' => 'auit/editor/blocks/sidebar.phtml',
  ),
  '_1269355477489_489' => 
  array (
    'name' => 'Sidebar blank',
    'version' => '',
    'op' => '',
    'template' => 'auit/editor/blocks/sidebar_blank.phtml',
  ),
  '_1269345158647_647' => 
  array (
    'name' => 'Blank ',
    'version' => '',
    'op' => '',
    'template' => 'auit/editor/blocks/blank.phtml',
  ),
  '_1270049083215_215' => 
  array (
    'name' => 'Sidebar with title (Variante 1.3)',
    'version' => '',
    'op' => '',
    'template' => 'auit/editor/blocks/v13/sidebar_title.phtml',
  ),
  '_1270049082621_621' => 
  array (
    'name' => 'Sidebar without title (Variante 1.3)',
    'version' => '',
    'op' => '',
    'template' => 'auit/editor/blocks/v13/sidebar.phtml',
  ),
  '_1270049082010_10' => 
  array (
    'name' => 'Sidebar blank (Variante 1.3)',
    'version' => '',
    'op' => '',
    'template' => 'auit/editor/blocks/v13/sidebar_blank.phtml',
  ),
);
		  	break;
		case 'auit_editor/menudefinition/reference':
$v=array (
  '_1269690824775_775' => 
  array (
    'name' => 'Top menu',
    'version' => '',
    'op' => '',
    'reference' => 'top.links',
  ),
  '_1269690826058_58' => 
  array (
    'name' => 'Footer menu',
    'version' => '',
    'op' => '',
    'reference' => 'footer_links',
  ),
);
		  	break;
		case 'auit_editor/speciallinks/reference':
$v=
array (
  '_1276247935782_782' => 
  array (
    'menu' => 'Customer',
    'link' => '{{store url="customer/account"}}',
    'label' => 'My Account',
    'modul' => '',
    'comment' => 'link to My Account',
  ),
  '_1276259584705_705' => 
  array (
    'menu' => 'Customer',
    'link' => '{{store url="customer/account/forgotpassword"}}',
    'label' => 'Forgot your password?',
    'modul' => '',
    'comment' => '',
  ),
  '_1276260480387_387' => 
  array (
    'menu' => 'Checkout',
    'link' => '{{store url="checkout"}}',
    'label' => 'Checkout',
    'modul' => '',
    'comment' => '',
  ),
  '_1276260635237_237' => 
  array (
    'menu' => 'Checkout',
    'link' => '{{store url="checkout/cart"}}',
    'label' => 'My Cart',
    'modul' => '',
    'comment' => '',
  ),
  '_1276260900008_8' => 
  array (
    'menu' => 'Contact',
    'link' => '{{store url="contacts"}}',
    'label' => 'Contact',
    'modul' => '',
    'comment' => '',
  ),
  '_1276261071893_893' => 
  array (
    'menu' => 'Search',
    'link' => '{{store url="catalogsearch/term/popular"}}',
    'label' => 'Search Terms',
    'modul' => '',
    'comment' => '',
  ),
  '_1276261073521_521' => 
  array (
    'menu' => 'Search',
    'link' => '{{store url="catalogsearch/advanced"}}',
    'label' => 'Advanced Search',
    'modul' => '',
    'comment' => '',
  ),
  '_1276261255869_869' => 
  array (
    'menu' => 'Catalog',
    'link' => '{{store url="catalog/seo_sitemap/category"}}',
    'label' => 'Site Map',
    'modul' => '',
    'comment' => 'Categories',
  ),
  '_1276261342331_331' => 
  array (
    'menu' => 'Catalog',
    'link' => '{{store url="catalog/seo_sitemap/product"}}',
    'label' => 'Site Map (Products)',
    'modul' => '',
    'comment' => 'Products',
  ),
  '_1276261798333_333' => 
  array (
    'menu' => 'Favorite/Websites',
    'link' => 'http://www.snm-portal.com',
    'label' => 'snm-portal.com',
    'modul' => '',
    'comment' => '',
  ),
  '_1276261997043_43' => 
  array (
    'menu' => 'Favorite/Search Engines',
    'link' => 'http://www.google.com',
    'label' => 'Google',
    'modul' => '',
    'comment' => '',
  )
);
		  	break;
		}
		return $v;
	}

}