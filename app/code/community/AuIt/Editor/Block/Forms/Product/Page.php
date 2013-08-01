<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Forms_Product_Page extends AuIt_Editor_Block_Form
{
	protected $product;
	public function setProduct($product)
	{
		$this->_product=$product;
		return $this;
	}
	public function forms()
	{
		$this->setAdminLocale();
		$product = $this->_product;
		
        
		$htmlfields= Mage::helper('auit_editor')->getHTMLAttributeDescription('product_attributes',$product->getAttributes());
        
		$dataFields=array();
		$exludes = array('tier_price','special_from_date','special_to_date','enable_googlecheckout');
		
        $setId = $product->getAttributeSetId();
		$pageInfo=array();
        if ($setId) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->load();
            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                $groupName = $group->getAttributeGroupName();
                foreach ($attributes as $key => $attribute) {
                    if( !$attribute->getIsVisible() ) {
                        unset($attributes[$key]);
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }
	            $items = $this->_buildFields($attributes,$htmlfields,$dataFields,$exludes);
                if (count($items)==0) {
                    continue;
                }
				$pageInfo[]=array(
				 'sort_order'=>$group->getSortOrder(),
	        	 'title'=>Mage::helper('catalog')->__($group->getAttributeGroupName()),
				 'tabTip'=>Mage::helper('catalog')->__($group->getAttributeGroupName()),
				'useScopeColumn'=>true,
				 'iconCls'=>'tab-general-16',
			 	'form_block_typ'=>'product',
				'fields'=>$items
	             );
            }
            usort ( $pageInfo , array("AuIt_Editor_Block_Form", "cmp_obj"));
        }
       
		$result = array(
			'title' => $this->getText("Product"),
			'iconcls'=>'page-product-page-16',	
			'useScopeColumn'=>true,
			'forms' =>$pageInfo,
			//'fields'=> $dataFields,
			'htmlfields'=>$htmlfields
		); 
        $this->resetAdminLocale();
        return $result;
	}
}
