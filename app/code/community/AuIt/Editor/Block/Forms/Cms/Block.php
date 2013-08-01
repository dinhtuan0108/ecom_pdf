<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Forms_Cms_Block extends AuIt_Editor_Block_Form
{
	public function forms()
	{
		$this->setAdminLocale();
		$htmlfields=array('content');
		$fileds= array(
    					array(	'xtype'=>'textfield','name'=>'title',
    							'fieldLabel'=> $this->getText('Block Title')
    					),
    					array(	'xtype'=>'textfield','name'=>'identifier',
    							'fieldLabel'=> $this->getText('Identifier')
    					)
    			);
		$storeField = $this->getStoreField();
		if ( $storeField )
			$fileds[]=$storeField;

    	$fileds[]= array(	'xtype'=>'combo','name'=>'is_active',
    							'fieldLabel'=> $this->getText('Status'),
    							'mode'=> 'local',
    							'typeAhead'=> true,
    							'triggerAction'=> 'all',
    							'lazyRender'=> true,
    							'valueField'=> 'value',
    							'displayField'=> 'text',
    							'store'=> array(	
    								'xtype'=>'arraystore',
    								'id'=>0,
					        		'fields'=> array('value', 'text'),
					        		'data'=>array(
							        	array(1, $this->getText('Enabled')),
							        	array(0, $this->getText('Disabled'))
    					        	)
					    		)    					
   						);

		$pageInfo=array();
		$pageInfo[]=array(
        	 'title'=>$this->getText('General Information'),
			 'tabTip'=>$this->getText('General Information'),
			 'iconCls'=>'tab-general-16',
             'fields'=>$fileds	
		);
		$result = array(
			'title' => $this->getText("CMS Block"),
			'iconcls'=>'page-cms-block-16',	
			'forms' =>$pageInfo,
			//'fields'=> array('page_id','title','identifier','content','is_active','store_id'),
			'htmlfields'=>$htmlfields
		); 
    	$this->resetAdminLocale();				
		return $result; 				
	}
}
