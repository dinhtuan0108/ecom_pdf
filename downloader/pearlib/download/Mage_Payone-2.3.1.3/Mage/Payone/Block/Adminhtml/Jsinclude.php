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
 * @category   Phoenix
 * @package    Mage_Payone
 * @copyright  Copyright (c) 2008 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)*
 */
class Mage_Payone_Block_Adminhtml_Jsinclude extends Mage_Adminhtml_Block_Template
{
    /**
     * Get value of module config field
     * @param <type> $field
     * @return <type> 
     */
    public function getModuleConfig($field)
    {
        $model = Mage::getModel('payone/cc');
        return $model->getConfigData($field);
    }
    /**
     * Check if Cc API is enabled
     * @return <type>
     */
    public function isCcApiEnabled()
    {
        return $this->getModuleConfig('active') && $this->getModuleConfig('payment_action') == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
    }
}
