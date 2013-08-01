<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2008 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Helper_Blocks extends Mage_Core_Helper_Abstract
{
	protected function canShow($item)
	{
		$bcanShow=false;
		if ( isset($item['opt_showat']) && $item['opt_showat'] != '')
		{
			$modulName = Mage::app()->getFrontController()->getRequest()->getModuleName();
			$urlRequest = Mage::app()->getFrontController()->getRequest()->getControllerName();
			$action = Mage::app()->getFrontController()->getRequest()->getActionName();
			$k2 = "$modulName-$urlRequest";
			$k3 = "$modulName-$urlRequest-$action";
			$pages = explode(',',$item['opt_showat']);
			foreach ( $pages as $pageType )
			{
				if ( trim($pageType)=='' )continue;
				//'cms-page' 'catalog-category' / 'catalog-product' 'checkout-cart' customer-account
				if ( $k2 == $pageType ) 
				{
					$bcanShow=true;
				}else 
				switch ( $pageType ) 
				{
					case '0':
						$bcanShow=true;
					break;
					case 'beforelogin':
						//Mage::getSingleton('customer/session')->isLoggedIn()
						if ( !Mage::getSingleton('customer/session')->isLoggedIn() )
							$bcanShow=true;
					break;
					case 'afterlogin':
						if ( Mage::getSingleton('customer/session')->isLoggedIn() )
							$bcanShow=true;
					break;
				}
				if ( $bcanShow ) break;
			} 
		}else 
			$bcanShow=true;
		 	
		if ( !$bcanShow )
			return $bcanShow;
	
		if ( !isset($item['opt_scheduleat']) || $item['opt_scheduleat'] == '')
			return $bcanShow;
		$bcanShow=false;
		$today = Mage::app()->getLocale()->date();
		$dayString = $today->toString(Varien_Date::DATE_INTERNAL_FORMAT);
		$timeString = $today->toString('HH:mm');
		$dateInfo = getdate($today->getTimestamp());
		$opts = explode(',',$item['opt_scheduleat']);
		foreach ( $opts as $opt )
		{
			if ( trim($opt)=='' )continue;
			switch($opt){
			case '0':
				$bcanShow=true;
				break;	
			case 'mon':
			case 'tue':
			case 'wed':
			case 'thu':
			case 'fri':
			case 'sat':
			case 'sun':
				if(strtolower(substr($dateInfo['weekday'], 0, 3)) == $opt )
					$bcanShow=true;
			break;
			case 'odd':
				if(($dateInfo['mday']%2))
					$bcanShow=true;
				break;
			case 'even':
				if(!($dateInfo['mday']%2))
					$bcanShow=true;
				break;
			}
		}
		if ( !$bcanShow )
			return $bcanShow;
		if ( isset($item['opt_startdate']) && trim($item['opt_startdate']))
		{
			$testDate =$item['opt_startdate'];
			if ( $testDate >  $dayString )
			{
				return false;
			}
		}
		if ( isset($item['opt_enddate'])  && trim($item['opt_enddate']))
		{
			$testDate =$item['opt_enddate'];
			if ( $dayString > $testDate   )
			{
				return false;
			}
		}
		
		if ( isset($item['opt_starttime']) && trim($item['opt_starttime']) )
		{
			$testZeit =$item['opt_starttime'];
			if ( $timeString < $testZeit )
			 	return false;
		}
		if ( isset($item['opt_endtime']) && trim($item['opt_endtime']))
		{
			$testZeit =$item['opt_endtime'];
			if ( $timeString > $testZeit  )
			 	return false;
		}
		return $bcanShow;
	}
	public function buildBlocks(Mage_Core_Model_Layout $layout){
		
		
		$blockDefintion = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/blocks');
		$references=array();
		foreach ( $blockDefintion as $item )
		{
			if ( $this->canShow($item) )
				$references[$item['reference']][$item['sort_order']][]=$item;
		} 
		foreach ( $references as $reference => $items)
		{
			$block = $layout->getBlock($reference);
			
			if ( $block && $block instanceof  Mage_Core_Block_Abstract)
			{
				ksort ($items);
				foreach ( $items as $sitems ) 
				{
					foreach ( $sitems as $item )
					{
						$bl = $layout->createBlock('auit_editor/sideblock')->setBlockData($item);
						$sortOrder = (int)$item['sort_order'];
						$sortOrder--;
						$childs = $block->getSortedChildren();
						if ( isset($childs[$sortOrder]) )
							$block->insert($bl,$childs[$sortOrder],false);//$sortOrder?true:false);
						else
							$block->append($bl);
					}
				}
			}
		}
	}
	public function buildMenus(Mage_Core_Model_Layout $layout){
		
		$blockDefintion = Mage::helper('auit_editor')->getArrayStoreConfig('auit_editor/menus');
		$references=array();
		foreach ( $blockDefintion as $item )
		{
			$references[$item['reference']][$item['sort_order']][]=$item;
		} 
		foreach ( $references as $reference => $items)
		{
			$block = $layout->getBlock($reference);
			if ( $block && $block instanceof  Mage_Page_Block_Template_Links)
			{
				rsort ($items);
				foreach ( $items as $sitems ) 
				{
					foreach ( $sitems as $item )
					{
						if ( isset($item['store_id']) )
							$stores = $item['store_id'];
						else
							$stores = 0;
						 
						if ( intval($stores) !== 0 && $stores !== '' )
						{
							$stores = array_flip(explode(',',$stores));
							if ( !isset($stores[Mage::app()->getStore()->getId()]) )
								continue;
						}  
						$url = Mage::helper('auit_editor')->translateDirective($item['href']);
						$position = (int)$item['sort_order'];
						if ( $position > 1 )
						{ 
							$links = $block->getLinks();
							foreach ( $links as $pos => &$items )
							{
								$position--;
								if ($position==1)
								{
									$position=$pos;break;
								} 
							}
						}
						$block->addLink($item['name'], $url, $item['title'],false,array(),$position);
					}
				}
			}
		}
	}	
}
