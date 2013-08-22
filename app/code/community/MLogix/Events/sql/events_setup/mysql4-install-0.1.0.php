<?php 
/*
 * @ecomwebpro.com
 * 20130820
 */
?><?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('events')};
CREATE TABLE {$this->getTable('events')} (
  `events_id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(11) unsigned NOT NULL default '0',
  `after_id` int(11) unsigned NOT NULL default '0',
  `item_title` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',  
  `alt` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`events_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 