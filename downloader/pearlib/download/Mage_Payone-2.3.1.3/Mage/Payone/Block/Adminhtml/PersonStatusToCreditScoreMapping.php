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
 * @category   Mage
 * @package    Mage_Payone
 * @copyright  Copyright (c) 2008 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Payone_Block_Adminhtml_PersonStatusToCreditScoreMapping extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_addRowButtonHtml = array();
    protected $_removeRowButtonHtml = array();

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '<div id="person_status_mapping_template" style="display:none">';
        $html .= $this->_getRowTemplateHtml();
        $html .= '</div>';

        $html .= '<ul id="person_status_mapping_container">';
        if ($this->_getValue('status')) {
            foreach ($this->_getValue('status') as $i=>$f) {
                if ($i) {
                    $html .= $this->_getRowTemplateHtml($i);
                }
            }
        }
        $html .= '</ul>';
        $html .= $this->_getAddRowButtonHtml('person_status_mapping_container',
            'person_status_mapping_template', $this->__('Add person status'));

        return $html;
    }

    protected function _getRowTemplateHtml($i=0)
    {
        $html = '<li style="display: block; width: 550px;">';
        $html .= '<div style="float: left;"><select name="'.$this->getElement()->getName().'[status][]" '.$this->_getDisabled().'>';
        $html .= '<option value="">'.$this->__('* Select person status').'</option>';
        foreach ($this->_getStatuses() as $statusCode => $title) {
            $html .= '<option value="'.$statusCode.'" '.$this->_getSelected('status/'.$i, $statusCode).' >'.$title.'</option>';
        }
        $html .= '</select></div>';

        $html .= '<div style="float: right;">';
        $html .= '<select style="width: 100px; margin: 0 10px 0 0;" name="'.$this->getElement()->getName().'[score][]" '.$this->_getDisabled().'>';
        foreach ($this->_getScores() as $scoreCode => $title) {
            $html .= '<option value="'.$scoreCode.'" '.$this->_getSelected('score/'.$i, $scoreCode).' >'.$title.'</option>';
        }
        $html .= '</select>';
        $html .= $this->_getRemoveRowButtonHtml();
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

    protected function _getStatuses()
    {
        $statuses = array();
        $statuses['NONE'] = 'NONE: no verification of personal data carried out';
        $statuses['PPB']  = 'PPB: first name & surname unknown';
        $statuses['PHB']  = 'PHB: surname known';
        $statuses['PAB']  = 'PAB: first name & surname unknown';
        $statuses['PKI']  = 'PKI: ambiguity in name and address';
        $statuses['PNZ']  = 'PNZ: cannot be delivered (any longer)';
        $statuses['PPV']  = 'PPV: person deceased';
        $statuses['PPF']  = 'PPF: postal address details incorrect';
        return $statuses;
    }

    protected function _getScores()
    {
        $scores = array();
        $scores['R']  = 'RED';
        $scores['Y']  = 'YELLOW';
        $scores['G']  = 'GREEN';
        return $scores;
    }

    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/'.$key);
    }

    protected function _getSelected($key, $value)
    {
        return $this->getElement()->getData('value/'.$key)==$value ? 'selected="selected"' : '';
    }

    protected function _getAddRowButtonHtml($container, $template, $title='Add')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('add '.$this->_getDisabled())
                    ->setLabel($this->__($title))
                    ->setOnClick("Element.insert($('".$container."'), {bottom: $('".$template."').innerHTML})")
                    ->setDisabled($this->_getDisabled())
                    ->toHtml();
        }
        return $this->_addRowButtonHtml[$container];
    }

    protected function _getRemoveRowButtonHtml($selector='li', $title='Delete')
    {
        if (!$this->_removeRowButtonHtml) {
            $this->_removeRowButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('delete v-middle '.$this->_getDisabled())
                    ->setLabel($this->__($title))
                    ->setOnClick("Element.remove($(this).up('".$selector."'))")
                    ->setDisabled($this->_getDisabled())
                    ->toHtml();
        }
        return $this->_removeRowButtonHtml;
    }
}
