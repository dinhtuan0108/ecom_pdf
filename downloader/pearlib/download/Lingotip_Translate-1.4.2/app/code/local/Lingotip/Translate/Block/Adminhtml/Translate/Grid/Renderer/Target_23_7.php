<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml  
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Lingotip_Translate_Block_Adminhtml_Translate_Grid_Renderer_Target
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$color = '';$textBoxId = '';
 		$data = Mage::getModel('translate/request')->load($row->getId());
  		$Target = $data->getTrgNames();
		$targetArray = explode(",",$Target);$toolTip = '';
		$isForSelectTT = $this->getRequest()->getParam('textboxid');
	
if(isset($isForSelectTT) && $isForSelectTT != "")
{
	$result[] = '<div>';
}	
		foreach($targetArray as $target){
			$status = Mage::getResourceModel('translate/txn')->getStatusOnGrid($row->getId(),$target);
			$border = 'border:0px; '; 
			$float="float:left;width:50%;";
			if(ucfirst($status) == ucfirst("pending")) {$color = "color:#000;";$toolTip = "Pending";}
			elseif(ucfirst($status) == ucfirst("active")) {$color = "color:#0000FF";$toolTip = "Note sent from translator";}
			elseif(ucfirst($status) == ucfirst("accept")) {$color = "color:#FF8040";$toolTip = "Accepted by translator";}
			elseif(ucfirst($status) == ucfirst("done")) {$color = "color:#3EA99F";$toolTip = "Translation is done";}

$translatedText  = Mage::getResourceModel('translate/txn')->getTranslatedTextOnGrid($row->getId(),$target);			
$textBoxId = $this->getRequest()->getParam('textboxid');
if(isset($isForSelectTT) && $isForSelectTT != "")
{
	$result[] .=  '<div><div style="'. $float. $color .'" alt="'.$toolTip.'" title="'.  ucfirst($toolTip) .'" >'.$target.'</div>';
	$result[] .= '<div style="'. $border.'"><button style="margin-bottom:5px;" onclick=translateTextOnGrid("transdata'.$row->getId().$target.'","'.$textBoxId.'","'.$status.'") alt="'.$toolTip.'" title="'.  ucfirst($toolTip) .'" >'."Select".'</button><br>';
	$result[] .= '</div></div>';
	$result[] .= '<div style="display:none;" id="transdata'.$row->getId().$target.'">'.htmlentities(stripcslashes($translatedText)).'</div>';
	//return $res = implode(",",$result);
}
else{
		$result[] = '<span onclick=translateTextOnGrid("transdata'.$row->getId().$target.'","'.$textBoxId.'","'.$status.'") alt="'.$toolTip.'" title="'.  ucfirst($toolTip) .'" style="'.  $color .'">'.$target.'</span>'. '<div style="display:none;" id="transdata'.$row->getId().$target.'">'.htmlentities($translatedText).'</div>';
		//$result[] ' ';
	}		
		
		}
	if(isset($isForSelectTT) && $isForSelectTT != "")
	{
		$result[] = '</div>';return $res = implode("",$result);
	}		
		return $res = implode(",",$result);
	}	
      
}
