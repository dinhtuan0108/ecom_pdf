<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Forms_Blog_Page extends AuIt_Editor_Block_Form
{
	public function forms()
	{
		$this->setAdminLocale();
    	
		
		$fields = array();
    	$fields[]= array(	'xtype'=>'textfield','allowBlank'=> false,'name'=>'title','fieldLabel'=> $this->getText('Title','blog'));
    	$fields[]= array(	'xtype'=>'textfield','allowBlank'=> false,'name'=>'identifier','fieldLabel'=> $this->getText('Identifier','blog'));

    	$storeField = $this->getStoreField();
    	if ($storeField)
   			$fields[]=  $storeField; 
   			
   			
		$categories = array();
	  	$collection = Mage::getModel('blog/cat')->getCollection()->setOrder('sort_order', 'asc');
		foreach ($collection as $cat) {
			$categories[] = array($cat->getCatId(), (string)$cat->getTitle());
		}
   			
		$fields[]= array(	'xtype'=>'GroupingComboBox','name'=>'cat_id','multiselect'=>true,
   							'fieldLabel'=> $this->getText('Category','blog'),
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
				        		'data'=>$categories
				    		)    					
    					);
   			
		$fields[]= array(	'xtype'=>'combo','name'=>'status',
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
						        	array(1, $this->getText('Enabled','blog')),
						        	array(2, $this->getText('Disabled','blog')),
						        	array(3, $this->getText('Hidden','blog'))
    				        	)
				    		)    					
    					);
		$fields[]= array(	'xtype'=>'combo','name'=>'comments',
   							'fieldLabel'=> $this->getText('Enable Comments','blog'),
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
						        	array(0, $this->getText('Enabled','blog')),
						        	array(1, $this->getText('Disabled','blog'))
    				        	)
				    		)    					
    					);
    	$fields[]= array(	'xtype'=>'textfield','allowBlank'=> false,'name'=>'tags','fieldLabel'=> $this->getText('Tags','blog'));
    					
		$htmlfields=array('post_content','short_content');
		$pageInfo=array();
		$pageInfo[]=array(
        	 'title'=>$this->getText('Post information','blog'),
			 'tabTip'=>$this->getText('Post information','blog'),
			 'labelAlign'=>'top',
			 'border'=>false,
			  'frame'=>false,
			 'iconCls'=>'tab-general-16',
             'defaults'=> array(
                 'anchor'=> '100%',
                 'allowBlank'=> false
             ),
             'fields'=>$fields		
		);
		
		$pageInfo[]=array(
        	 'title'=>$this->getText('Meta Data','blog'),
			 'tabTip'=>$this->getText('Meta Data','blog'),
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
    							'fieldLabel'=> $this->getText('Keywords','blog'),
    							'title'=> $this->getText('Meta Keywords','blog')
    					),
    					array(	'xtype'=>'textarea','name'=>'meta_description',
    							'fieldLabel'=> $this->getText('Description','blog'),
    							'title'=> $this->getText('Meta Description','blog')
    					)
    		)
    	);
		
		/*
		$pageInfo[]=array(
        	 'title'=>$this->getText('Advanced Post Options','blog'),
			 'tabTip'=>$this->getText('Advanced Post Options','blog'),
			 'iconCls'=>'tab-custom-16',
			 'labelAlign'=>'top',
			 'border'=>false,
			  'frame'=>false,
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
    					array(	'xtype'=>'textfield',
    							'name'=>'user',
    							'fieldLabel'=> $this->getText('Poster','blog')
    					),
    					array(	'xtype'=>'created_time',
    							'name'=>'user',
    							'fieldLabel'=> $this->getText('Post Date','blog')
    					)
				)
            );			
          */  		
		$result= array(
			'title' => $this->getText("Blog Page"),
			'iconcls'=>'page-cms-page-16',	
			'forms' =>$pageInfo,
			'htmlfields'=>$htmlfields
		); 
    	$this->resetAdminLocale();
    	return $result;				
	}
}
