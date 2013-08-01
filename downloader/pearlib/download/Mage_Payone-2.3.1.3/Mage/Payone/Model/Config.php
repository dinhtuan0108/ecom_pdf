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
class Mage_Payone_Model_Config extends Mage_Payment_Model_Config
{
	/**
	 * Retrieve array of credit card types
	 *
	 * @return array
	*/
	public function getTransactionModes()
	{
		$modes = array();
		foreach (Mage::getConfig()->getNode('global/payment/transaction/modes')->asArray() as $data) {
			$modes[$data['code']] = $data['name'];
		}
		return $modes;
	}

	public function getPersonStatusMapping()
	{
		$statusMapping = array();
		$config = @unserialize(Mage::getStoreConfig('payonecreditrating/addresscheck/person_status_to_credit_score_mapping'));
		if (is_array($config) && isset($config['status'])) {
    		for ($i=0; $i<count($config['status']); $i++) {
                if (!empty($config['status'][$i])) {
                    $statusMapping[$config['status'][$i]] = $config['score'][$i];
                }
    		}
		}
		return $statusMapping;
	}
}