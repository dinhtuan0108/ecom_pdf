<?php
/**
 * AuIt 
 *
 * @category   AuIt
 * @package    AuIt_Basic
 * @author     M Augsten
 * @copyright  Copyright (c) 2008 Ingenieurbüro (IT) Dipl.-Ing. Augsten (http://www.au-it.de)
 */
class AuIt_Basic_Model_Observer  extends Mage_AdminNotification_Model_Feed
{
    const XML_USE_HTTPS_PATH    = 'auit_basic/feed/use_https';
    const XML_FEED_URL_PATH     = 'auit_basic/feed/url';
    
    const XML_FREQUENCY_PATH    = 'system/adminnotification/frequency';
    const XML_LAST_UPDATE_PATH  = 'system/adminnotification/last_update';
    
    
	public function checkFeed(Varien_Event_Observer $observer)
    {
		if(!Mage::getStoreConfig('auit_basic/feed/enabled')){		
			return;
		}
    	$this->checkUpdate();
    }
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
                . Mage::getStoreConfig(self::XML_FEED_URL_PATH);
        }
        return $this->_feedUrl;
    }
    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
    }
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('auit_basic_feed_lastcheck');
    }
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'auit_basic_feed_lastcheck');
        return $this;
    }
    public function checkUpdate()
    {
        if ( ($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }
        $feedData = array();
        $feedXml = $this->getFeedData();
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $feedData[] = array(
                    'severity'      => (int)$item->severity,
                    'date_added'    => $this->getDate((string)$item->pubDate),
                    'title'         => (string)$item->title,
                    'description'   => (string)$item->description,
                    'url'           => (string)$item->link,
                );
            }
            if ($feedData) {
                Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
            }

        }
        $this->setLastUpdate();
        return $this;
    }
}


