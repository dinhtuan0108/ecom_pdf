<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Forms_Cms_Page extends AuIt_Editor_Block_Form
{
	public function forms()
	{
		$this->setAdminLocale();
		$storeField = $this->getStoreField();
		
		$fields = array();
    	$fields[]= array(	'xtype'=>'textfield','allowBlank'=> false,'name'=>'title','fieldLabel'=> $this->getText('Page Title'));
    	$fields[]= array(	'xtype'=>'textfield','allowBlank'=> false,'name'=>'identifier','fieldLabel'=> $this->getText('Identifier'));
    	if ($storeField)
    		$fields[]=  $storeField; 
		$fields[]= array(	'xtype'=>'combo','name'=>'is_active',
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
		$htmlfields=array('content');
		$pageInfo=array();
		$pageInfo[]=array(
        	 'title'=>$this->getText('General Information'),
			 'tabTip'=>$this->getText('General Information'),
			 'labelAlign'=>'top',
			 'border'=>false,
			  'frame'=>false,
		//'style'=>'padding:5px',
			 'iconCls'=>'tab-general-16',
             'defaults'=> array(
                 'anchor'=> '100%',
                 'allowBlank'=> false
             ),
             'fields'=>$fields		
		);
		$pageInfo[]=array(
        	 'title'=>$this->getText('Custom Design'),
			 'tabTip'=>$this->getText('Custom Design'),
			 'iconCls'=>'tab-custom-16',
			 'labelAlign'=>'top',
			 'border'=>false,
			  'frame'=>false,
		//'style'=>'padding:5px',
             'defaults'=> array(
                 'anchor'=> '100%'
             ),
             'fields'=>array(
    					array(	'xtype'=>'combo','name'=>'custom_theme',
    							'fieldLabel'=> $this->getText('Custom Theme'),
    							'mode'=> 'local',
    							'typeAhead'=> true,
    							'triggerAction'=> 'all',
    							'lazyRender'=> true,
    							'valueField'=> 'value',
    							'displayField'=> 'text',
    							'groupField'=> 'group',
    							'store'=> array(	
    								'xtype'=>'simplestore',
									//'id'=>2,
    								'fields'=> array('group', 'text','value'),
									'data'=>$this->toGroupV(Mage::getModel('core/design_source_design')->getAllOptions())
					        		
					    		)    					
    					),		
             			array(	'xtype'=>'auit_datefield',
             					'name'=>'custom_theme_from',
    							'fieldLabel'=> $this->getText('Custom Theme From')
    					),
             			array(	'xtype'=>'auit_datefield','name'=>'custom_theme_to',
    							'fieldLabel'=> $this->getText('Custom Theme To')
    					),
    					array(	'xtype'=>'combo','name'=>'root_template',
    							'fieldLabel'=> $this->getText('Layout'),
    							'mode'=> 'local',
    							'typeAhead'=> true,
    							'triggerAction'=> 'all',
    							'lazyRender'=> true,
    							'valueField'=> 'value',
    							'displayField'=> 'text',
    							'groupField'=> 'group',
    							'allowBlank'=> false,
    							'store'=> array(	
    								'xtype'=>'simplestore',
									//'id'=>2,
    								'fields'=> array('group', 'text','value'),
									'data'=>$this->toGroupV( Mage::getSingleton('page/source_layout')->toOptionArray())
					        		
					    		)    					
    					),		
    					array(	'xtype'=>'textarea',
    							'name'=>'layout_update_xml',
    							'fieldLabel'=> $this->getText('Layout Update XML')
    					)
    			)
            );					
		$pageInfo[]=array(
        	 'title'=>$this->getText('Meta Data'),
			 'tabTip'=>$this->getText('Meta Data'),
			 'iconCls'=>'tab-meta-16',
			 'labelAlign'=>'top',
			 'border'=>false,
			  'frame'=>false,
        	 //'style'=>'padding:5px',
             'defaults'=> array(
                 'anchor'=> '100% 47%'
             ),
             'fields'=>array(
    					array(	'xtype'=>'textarea','name'=>'meta_keywords',
    							'fieldLabel'=> $this->getText('Keywords'),
    							'title'=> $this->getText('Meta Keywords')
    					),
    					array(	'xtype'=>'textarea','name'=>'meta_description',
    							'fieldLabel'=> $this->getText('Description'),
    							'title'=> $this->getText('Meta Description')
    					)
    		)
    	);
		$result= array(
			'title' => $this->getText("CMS Page"),
			'iconcls'=>'page-cms-page-16',	
			'forms' =>$pageInfo,
		/**
			'fields'=> array('page_id','title','root_template','meta_keywords',
			 'meta_description','identifier','content',
			 array('name'=> 'creation_time', 'type'=>'date', 'dateFormat'=> 'Y-m-d H:i:s'),
			 array('name'=> 'update_time', 'type'=>'date', 'dateFormat'=> 'Y-m-d H:i:s'),
			 'is_active','sort_order','layout_update_xml','custom_theme',
			 array('name'=> 'custom_theme_from', 'type'=>'date', 'dateFormat'=> 'Y-m-d'),
			 array('name'=> 'custom_theme_to', 'type'=>'date', 'dateFormat'=> 'Y-m-d'),
			 'store_id'),
			 */
			'htmlfields'=>$htmlfields
		); 
    	$this->resetAdminLocale();
    	return $result;				
	}
}
