<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Forms_Category_Page extends AuIt_Editor_Block_Form
{
	protected $_category;
	public function setCategory($category)
	{
		$this->_category=$category;
		return $this;
	}
	public function forms()
	{
		$this->setAdminLocale();
		
		$category = $this->_category;
		$categoryAttributes = $category->getAttributes();
		
		$htmlfields= Mage::helper('auit_editor')->getHTMLAttributeDescription('catalog_attributes',$categoryAttributes);
		$dataFields=array();
//		$oldStore = Mage::app()->getStore();
	//	Mage::app()->setCurrentStore(0);
		
		$pageInfo=array();
		$attributeSetId     = $category->getDefaultAttributeSetId();
		$groupCollection    = Mage::getResourceModel('eav/entity_attribute_group_collection')
		->setAttributeSetFilter($attributeSetId)
		->load();

		foreach ($groupCollection as $group) {
			/* @var $group Mage_Eav_Model_Entity_Attribute_Group */
			$attributes = array();
			foreach ($categoryAttributes as $attribute) {
				/* @var $attribute Mage_Eav_Model_Entity_Attribute */
				if ($attribute->isInGroup($attributeSetId, $group->getId())) {
					$attributes[] = $attribute;
				}
			}
			// do not add grops without attributes
			if (!$attributes) {
				continue;
			}
			$items = $this->_buildFields($attributes,$htmlfields,$dataFields);
			if (count($items)==0) {
				continue;
			}
			$pageInfo[]=array(
             'sort_order'=>$group->getSortOrder(),
        	 'title'=> $this->getText($group->getAttributeGroupName(),'catalog'),
			 'tabTip'=>$this->getText($group->getAttributeGroupName(),'catalog'),
        	 'useScopeColumn'=>true,
			 'iconCls'=>'tab-general-16',
			 'form_block_typ'=>'category',
             'fields'=>$items
			);
		}
		
		//Mage::app()->setCurrentStore($oldStore);
		
		
		
		usort ( $pageInfo , array("AuIt_Editor_Block_Form", "cmp_obj"));
		$result= array(
			'title' => $this->getText("Category"),
			'iconcls'=>'page-category-page-16',	
        	 'useScopeColumn'=>true,
			 'forms' =>$pageInfo,
		//	'fields'=> $dataFields,
			'htmlfields'=>$htmlfields
		);
		$this->resetAdminLocale();
		return $result; 				
	}
}
