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
 * @copyright  Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Payone_Model_Cod extends Mage_Payone_Model_Abstract
{
	/**
	* unique internal payment method identifier
	*
	* @var string [a-z0-9_]
	**/
	protected $_code			= 'payone_cod';
	protected $_formBlockType	= 'payone/form';
	protected $_infoBlockType	= 'payone/info';
	protected $_paymentMethod	= 'cod';


	/**
	 * prepare params array to send it to gateway page via POST
	 * @param Mage_Sales_Model_Order $order
     * @return array
     */

    protected function _getAuthorizeParams(Mage_Sales_Model_Order $order)
	{
		$params = parent::_getAuthorizeParams($order);
        if (!$this->canAuthorize()) {
            $params['autosubmit'] = 'yes';
        }
        $params['shippingprovider'] = 'DHL';		
		return $params;
	}

    /**
     * Using internal pages for input payment data
     * Can be used in admin
     *
     * @return bool
     */
    public function canUseInternal()
    {
       return $this->getConfigData('payment_action') == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE;
    }
}