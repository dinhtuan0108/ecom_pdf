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
class Mage_Payone_Block_Checkout_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
    /**
     * Overrides
     * Performs additional checking for Mage_Payone_Model_Abstract payment methods
     *
     * @return bool
     */
    protected function _canUseMethod($method) {
        $allowed = parent::_canUseMethod($method);
        if ($allowed) {
            $allowed = $this->_canUseForCreditRating($method, $this->getQuote());
        }
        return $allowed;
    }

    /**
     *  Credit Rating
     *
     *  @return bool
     */
    protected function _canUseForCreditRating($method, $quote)
    {
        $methodCode = $method->getCode();
        $creditRating = Mage::getSingleton('payone/creditRating');
        if (!$creditRating->isActiveForMethod($methodCode, $quote)) {
            // Allow Method, if  Credit Raitng is disabled
            // or if not shold be performed for this method and quote
            Mage::helper('payone')->debug('canUseForCreditRating() - ' . 'Order doesn\'t match criteria to get credit rating. Method: ' . $methodCode . ' Allowed by default.', Mage_Payone_Helper_Data::DEBUG_CONFIG_CREDIT_RATING, Mage_Payone_Helper_Data::DEBUG_LEVEL_LIGHT);
            return true;
        }
        // Default value for score is YELLOW
        $score = $creditRating->getCreditRatingScore($methodCode, $quote);
        $allowedScore = $creditRating->getAllowedScoreForMethod($methodCode);
        $allowed = $this->_compareCreditRatingScore($score['score'], $allowedScore);
        $debugMessage = 'canUseForCreditRating() - ' . 'Method: ' . $methodCode .
                  ', Score:' . $score['score'] . ', allowedScore: ' . $allowedScore .
                  ', Allowed: ' . ($allowed ? 'Yes' : 'No') . ', Source: ' . $score['source'];
        Mage::helper('payone')->debug($debugMessage, Mage_Payone_Helper_Data::DEBUG_CONFIG_CREDIT_RATING, Mage_Payone_Helper_Data::DEBUG_LEVEL_LIGHT);
        return $allowed;
    }

    /**
     * Returns true if $score1 >= $score2
     *
     * @return bool
     */
    protected function _compareCreditRatingScore ($score1, $score2)
    {
        switch ($score1) {
            case Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_GREEN:
                return true;
                break;
            case Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_YELLOW:
                return $score2 == Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_YELLOW ||
                       $score2 == Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_RED;
                break;
            case Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_RED:
                return $score2 == Mage_Payone_Model_CreditRating::CREDIT_RATING_SCORE_RED;
                break;
            default:
                return false;
        }
    }
}
