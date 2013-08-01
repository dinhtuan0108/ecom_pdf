<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Editor_Block_Forms_Cms_Inlineblock extends AuIt_Editor_Block_Form
{
	public function forms()
	{
		$this->setAdminLocale();
		$htmlfields=array('content');
		$pageInfo=array();
		$pageInfo[]=array(
        	 'title'=>$this->getText('General Information'),
			 'tabTip'=>$this->getText('General Information'),
			 'labelAlign'=>'top',
			 'iconCls'=>'tab-general-16',
			'fields'=>array(
    				array(	'xtype'=>'blockeditor',
    						'name'=>'source',
   							'fieldLabel'=> $this->getText('Source')
    						)
			)
		);
		$result = array(
			'title' => $this->getText("Inline Block"),
			'iconcls'=>'page-cms-inline-16',	
			'forms' =>$pageInfo,
			//'fields'=> array('source'),
			'htmlfields'=>$htmlfields
		);
		$this->resetAdminLocale();
		return $result;
	}
}
