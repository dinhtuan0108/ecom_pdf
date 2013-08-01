<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Editor
 * @author     M Augsten
 * @copyright  Copyright (c) 2010 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
abstract class AuIt_Editor_Block_Form extends Mage_Core_Block_Abstract
{
	abstract public function forms();
	static function cmp_obj($a, $b)
    {
        $al = $a['sort_order'];
        $bl = $b['sort_order'];
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
    protected function setAdminLocale()
    {
    	$adminLocale = Mage::helper('auit_editor')->getAdminLocaleCode();
    	if ( !$adminLocale )
    		$adminLocale=Mage::app()->getTranslator()->getLocale();
    	$this->oldLocalCode = Mage::app()->getTranslator()->getLocale();
    	if ( $adminLocale != $this->oldLocalCode )
    	{
			Mage::app()->getTranslator()->setLocale($adminLocale);
			Mage::app()->getTranslator()->init('adminhtml',true);
    	}
    	/**
    	$this->oldArea = Mage::getDesign()->getArea();
		Mage::getDesign()->setArea('adminhtml');
		
    	$this->oldLocalCode = Mage::app()->getTranslator()->getLocale();
    	$adminLocale = Mage::getStoreConfig('general/locale/code', 0);
    	Mage::app()->getLocale()->setLocale($adminLocale);
		Mage::app()->getTranslator()->setLocale($adminLocale);
		Mage::app()->getTranslator()->init('adminhtml',true);
    	*/
    }
    protected function resetAdminLocale()
    {
    	/*
    	Mage::app()->getTranslator()->setLocale($this->oldLocalCode);
    	Mage::app()->getLocale()->setLocale($this->oldLocalCode);
    	
    	Mage::getDesign()->setArea($this->oldArea);
		Mage::app()->getTranslator()->init('frontend',true);
		*/
    }
    protected function getStoreField()
    {
    	return Mage::helper('auit_editor')->getStoreField();
    }
    protected function getText($t,$modul='cms')
    {
    	return Mage::helper($modul)->__($t);
    }
    protected function trimLabel($label)
    {
		return str_replace(array('&nbsp;'),'',$label);
    }
    protected function getWbData(array $opts)
    {
    	$wb = array();
    	foreach ( $opts as $opt )
    		$wb[]=array($opt['value'],$opt['label']);
		return $wb;
    }
    protected function toGroupV(array $opts,$group=false)
    {
    	return Mage::helper('auit_editor')->toGroupV($opts,$group);
    }
    protected function _buildFields($attributes,array &$htmlfields,array &$dataFields,$exclude=array())
    {
    	$items=array();
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || !$attribute->getIsVisible()) {
                continue;
            }
            if ( ($inputType = $attribute->getFrontend()->getInputType())
                 && !in_array($attribute->getAttributeCode(), $exclude)
                 && ('media_image' != $inputType)
                 ) {
                 	
                 	if ( ($field = $this->getField($attribute)) )
                 	{
				
                 		
                 		if ( isset($htmlfields[$field['name']]) )
                 		{
                 			if ( !isset($field['xtype']) || $field['xtype'] != 'textarea' )
                 			{
                 				unset($htmlfields[$field['name']]);
                 			}else {
                 				$htmlfields[$field['name']] = array_merge($htmlfields[$field['name']],$field);
                 				continue;
                 			}
                 		}
		            	if ( isset($field['datatyp']) ) 
		            	{ 
		            		$dataFields[]=$field['datatyp'];
		            		unset($field['datatyp']);
		            	}
		            	$items[]=$field;
                 	}
            }
        }
        return $items;
    }    
    public function getScopeLabel(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $html = '';
        if (!$attribute || Mage::app()->isSingleStoreMode() || $attribute->getFrontendInput()=='gallery') {
            return $html;
        }
        if ($attribute->isScopeGlobal()) {
            $html.= '[GLOBAL]';
        }
        elseif ($attribute->isScopeWebsite()) {
            $html.= '[WEBSITE]';
        }
        elseif ($attribute->isScopeStore()) {
            $html.= '[STORE VIEW]';
        }

        return $html;
    } 
    public function getToolTip($attribute)
    {
    	$tooltip = '<b>'.__($attribute->getFrontend()->getLabel()).'</b> - ';
    	return $tooltip.$this->getScopeLabel($attribute).'<hr/><br/>'.$attribute->getNote();
    }
    
    public function canDisplayUseDefault($attribute)
    {
        if ($attribute ) {
            if (!$attribute->isScopeGlobal() )
            /**
                && $this->getDataObject()
                && $this->getDataObject()->getId()
                && $this->getDataObject()->getStoreId())
                */
            {
                return true;
            }
        }
        return false;
    }
    
    /*   
    <?php if ($this->canDisplayUseDefault()): ?>
    <td class="value use-default">
        <input <?php if($_element->getReadonly()):?> disabled="disabled"<?php endif; ?> 
        type="checkbox" 
        name="use_default[]" 
        id="<?php echo $_element->getHtmlId() ?>_default"<?php if ($this->usedDefault()): ?> 
        checked="checked"<?php endif; ?> 
        onclick="toggleValueElements(this, this.parentNode.parentNode)" 
        value="<?php echo $this->getAttributeCode() ?>"/>
        <label for="<?php echo $_element->getHtmlId() ?>_default" class="normal"><?php echo $this->__('Use Default Value') ?></label>
    </td>
    <?php endif; ?>
    <td<?php if ($_element->getNote()): ?> class="note"<?php endif ?>><small><?php echo $_element->getNote()?$_element->getNote():'&nbsp;' ?></small></td>
    */
    
    function getField(Mage_Eav_Model_Entity_Attribute $attribute)
    {
    	$fieldType = $attribute->getFrontend()->getInputType();
        $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
		if (!empty($rendererClass)) {
        	$fieldType  = $fieldType . '_' . $attribute->getAttributeCode();
            //$fieldset->addType($fieldType, $rendererClass);
		}
        $item=null;
        switch ( $fieldType )
		{
			case 'text':
                $item=array(	'xtype'=>'textfield',
              					'name'=>$attribute->getAttributeCode(),
    							'fieldLabel'=> __($attribute->getFrontend()->getLabel()),
                				'tooltip'=>$this->getToolTip($attribute),
                				'allowBlank'=> $attribute->getIsRequired()?false:true,
                				'datatyp'=>$attribute->getAttributeCode()
                				,'isScopeStore'=>$attribute->isScopeStore()
                				//,'is_html_allowed_on_front'=>$attribute->getData('is_html_allowed_on_front')
                // 'class'     => $attribute->getFrontend()->getClass(),
                
    					);
				break;
			case 'date':
             	$item=array(	'xtype'=>'auit_datefield',
             					'name'=>$attribute->getAttributeCode(),
    							'fieldLabel'=> __($attribute->getFrontend()->getLabel()),
                				'tooltip'=>$this->getToolTip($attribute),
                				'allowBlank'=> $attribute->getIsRequired()?false:true,
             					'isScopeStore'=>$attribute->isScopeStore(),
                				'datatyp'=>array('name'=> $attribute->getAttributeCode(), 'type'=>'date', 'dateFormat'=> 'Y-m-d H:i:s')
    					);
				break;
			case 'textarea':
    			$item=array(	'xtype'=>'textarea',
             					'name'=>$attribute->getAttributeCode(),
    							'fieldLabel'=> __($attribute->getFrontend()->getLabel()),
    							'tooltip'=>$this->getToolTip($attribute),
    							'isScopeStore'=>$attribute->isScopeStore(),
    							'allowBlank'=> $attribute->getIsRequired()?false:true,
                				'datatyp'=>$attribute->getAttributeCode(),
    							'is_html_allowed_on_front'=>$attribute->getData('is_html_allowed_on_front')
    						);
				break;
			case 'select':
    			$item=array(	'xtype'=>'combo',
    							'name'=>$attribute->getAttributeCode(),
    							'isScopeStore'=>$attribute->isScopeStore(),
    							'fieldLabel'=> __($attribute->getFrontend()->getLabel()),
    							'tooltip'=>$this->getToolTip($attribute),
    							'allowBlank'=> $attribute->getIsRequired()?false:true,
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
					        		'data'=>$this->getWbData($attribute->getSource()->getAllOptions(true, true))
					    		),
                				'datatyp'=>$attribute->getAttributeCode()    					
    						);
				break;
			case 'image':
    			$item=array(	'xtype'=>'auit-catalog-image',
    							'isScopeStore'=>$attribute->isScopeStore(),
             					'name'=>$attribute->getAttributeCode(),
    							'fieldLabel'=> __($attribute->getFrontend()->getLabel()),
    							'tooltip'=>$this->getToolTip($attribute),
    			   				'allowBlank'=> $attribute->getIsRequired()?false:true,
                				'datatyp'=>$attribute->getAttributeCode()
    						);
				break;
			default:
				break; 
		} 
        return $item;
    }
}
