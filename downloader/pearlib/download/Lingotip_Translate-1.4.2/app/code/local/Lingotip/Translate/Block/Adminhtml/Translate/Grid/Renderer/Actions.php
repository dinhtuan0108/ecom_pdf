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
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Lingotip_Translate_Block_Adminhtml_Translate_Grid_Renderer_Actions
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
 		$paidYesOrNo = Mage::getResourceModel('translate/request')->getPaymentStatusById($row->getId());
		$form_key = Mage::getSingleton('core/session')->getFormKey(); 
	  if($paidYesOrNo == "no")
	  {
            //return sprintf('<a href="%s">%s</a>  <a href="%s" onClick="deleteConfirm(\'%s\',this.href); return false;">%s</a>  <a href="%s">%s</a>  <a href="%s">%s</a>',
				 
				 return sprintf('<a href="%s" onClick="deleteConfirm(\'%s\',this.href); return false;">%s</a> <a href="%s">%s</a>    <a href="%s">%s</a>  <a href="%s">%s</a>',
				$this->getUrl('*/*/delete', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('translate')->__('Are you sure?'),
				'<img class="logo1" title="Delete" alt="Delete" src="'.$this->getSkinUrl().'images/lingotip/cancel_icon.gif">',
				$this->getUrl('*/*/edit/isedit/yes/', array('_current'=>true, 'rid' => $row->getId())),
                '<img class="logo1" alt="Edit" title="Edit" src="'.$this->getSkinUrl().'images/lingotip/fam_page_white_edit.gif">',					 				$this->getUrl('*/*/paypal/directPaypal/yes/form_key/'.$form_key, array('_current'=>true, 'id' => $row->getId())),
                '<img class="logo1" alt="PayPal payment was not made yet" title="PayPal payment was not made yet" src="'.$this->getSkinUrl().'images/lingotip/fam_money.gif">',	
			    $this->getUrl('*/*/view/client/comments', array('_current'=>true, 'id' => $row->getId())),
                '<img class="logo1" alt="View Translation Details" title="View Translation Details" src="'.$this->getSkinUrl().'images/lingotip/btn_show-hide_icon.gif">'
            );
        }
		else
		{
			  // return sprintf('<a href="%s">%s</a>  <a href="%s" onClick="alert(\'%s\');return false;">%s</a>  <a href="%s" onClick="alert(\'%s\'); return false;">%s</a>  <a href="%s" onClick="alert(\'%s\'); return false;">%s</a>',
			   
			   return sprintf('<a href="%s" onClick="alert(\'%s\'); return false;">%s</a> <a href="%s" onClick="alert(\'%s\'); return false;">%s</a> <a href="%s" onClick="alert(\'%s\');return false;">%s</a>  <a href="%s">%s</a>',
			    $this->getUrl('*/*/delete', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('translate')->__('You cannot delete an active translation.'),
				'<img class="logo1" title="Delete" alt="Delete" src="'.$this->getSkinUrl().'images/lingotip/cancel_icongray.gif">',
				$this->getUrl('*/*/edit/isedit/yes/', array('_current'=>true, 'id' => $row->getId())),
				Mage::helper('translate')->__('You cannot edit an active translation.'),
                '<img class="logo1" alt="Edit" title="Edit" src="'.$this->getSkinUrl().'images/lingotip/edit_gray.gif">',
                $this->getUrl('*/*/paypal', array('_current'=>true, 'id' => $row->getId())),
				Mage::helper('translate')->__('Payment has already been made for this translation.'),
                '<img class="logo1" alt="PayPal payment is completed" title="PayPal payment is completed" src="'.$this->getSkinUrl().'images/lingotip/paypal_gray.gif">',
 			    $this->getUrl('*/*/view/client/comments', array('_current'=>true, 'id' => $row->getId())),
                '<img class="logo1" alt="View Translation Details" title="View Translation Details" src="'.$this->getSkinUrl().'images/lingotip/btn_show-hide_icon.gif">'
     
				

            );
		}
	}	
      
}
