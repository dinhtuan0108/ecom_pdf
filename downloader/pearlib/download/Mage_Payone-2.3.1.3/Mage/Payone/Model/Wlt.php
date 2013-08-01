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
class Mage_Payone_Model_Wlt extends Mage_Payone_Model_Abstract
{
	/**
	* unique internal payment method identifier
	*
	* @var string [a-z0-9_]
	**/
	protected $_code			= 'payone_wlt';
	protected $_formBlockType	= 'payone/form';
	protected $_infoBlockType	= 'payone/info';
	protected $_paymentMethod	= 'wlt';

	/**
 	 * Prepare VOR Auth request params
	 * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function _getAuthorizeParams(Mage_Sales_Model_Order $order)
	{
		$params = parent::_getAuthorizeParams($order);
        if (!$this->canAuthorize()){
            $params['autosubmit'] = 'yes';
        }
		$params['wallettype'] = 'PPE';
		return $params;
	}
}