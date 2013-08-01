<?php

class Lingotip_Translate_Block_Adminhtml_Translate_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
       // $this->_objectId = 'id';
	    $this->_mode = 'add';
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml_translate';
		
$installModel = Mage::getModel('translate/install');
$registerationData = $installModel->getRegisterationData($installModel); 
$installId = $registerationData->getId(); 

if(!isset($installId) && $installId == "" )
{
	$this->_addButton('registerc', array(
				'label'     => Mage::helper('translate')->__('Registration'),
				'onclick'   => "location.href='".$this->getUrl('*/adminhtml_install/index')."'",
				'class'     => '',
    		));
	
			$this->_removeButton('save', 'label', Mage::helper('translate')->__('save'));
			$this->_removeButton('cancel', 'label', Mage::helper('translate')->__('cancel'));
			$this->_removeButton('reset', 'label', Mage::helper('translate')->__('Reset'));
			$this->_removeButton('back','class','','label', Mage::helper('translate')->__('Back'));
}	
else
{
 
		$this->_addButton('save', array(
            'label'     => Mage::helper('translate')->__('Estimate'),
            'onclick'   => 'addForm.submit();',
            'class'     => '',
        ), 1);
		
		$this->_addButton('cancel', array(
            'label'     => Mage::helper('translate')->__('Cancel'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
        ), 1);
		
		$gridLabel = Mage::helper('translate')->getGridLabel();
        $this->_updateButton('delete', 'label', Mage::helper('translate')->__('Delete Item'));
		$this->_removeButton('reset', 'label', Mage::helper('translate')->__('Reset'));
		$this->_removeButton('back','class','','label', Mage::helper('translate')->__($gridLabel));

		$this->_addButton('back1', array(
            'label'     => Mage::helper('translate')->__($gridLabel),
             'onclick'   => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
			 'class'     => 'back',
        ), -1
		);
}		
        $this->_formScripts[] = "
		
		    


            function toggleEditor() {
                if (tinyMCE.getInstanceById('translate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'translate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'translate_content');
                }
            }
			
			function display_data(slang,path,img,style) { 
 
				//document.getElementById('trg_names1').style.display='';
				//document.getElementById('trg_names').innerHTML='';
				
				xmlhttp=GetXmlHttpObject();
				
				if (xmlhttp==null) {
					alert ('Your browser does not support AJAX!');
					return;
				} 
	 
				var url=path+'targetlanguage.php';
				url=url+'?source='+slang;

 
 if (xmlhttp.readyState == 0 || xmlhttp.readyState == 1 || xmlhttp.readyState == 2 || xmlhttp.readyState == 3)
{
	//document.getElementById('trg_names1').innerHTML = 'Getting Target Languages'; //loading
		document.getElementById('trg_names1').innerHTML = '<img src='+img+ style + ' />'; //loading

}


				xmlhttp.onreadystatechange=function() {
			
			
					if (xmlhttp.readyState==4 || xmlhttp.readyState=='complete') {
						document.getElementById('trg_names1').innerHTML=xmlhttp.responseText;
						//document.getElementById('trg_names1').style.display='none';
						// <img src=''/>
					}
				}
				xmlhttp.open('GET',url,true);
				xmlhttp.send(null);
			}
			
			function GetXmlHttpObject() {
				var xmlhttp=null;
				try {
					// Firefox, Opera 8.0+, Safari
					xmlhttp=new XMLHttpRequest();
			}
			catch (e) {
				// Internet Explorer
				try {
					xmlhttp=new ActiveXObject('Msxml2.XMLHTTP');
				}
				catch (e) {
					xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
				}
			}
			return xmlhttp;
			}
 
        ";
		
    }

    public function getHeaderText()
    {
             return Mage::helper('translate')->__("Request a Translation"); 
    }
 
}