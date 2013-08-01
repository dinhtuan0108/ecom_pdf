<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('lt_config')};
CREATE TABLE {$this->getTable('lt_config')} (
  `install_id` int(11) unsigned NOT NULL auto_increment,
  `lturl` varchar(50) default NULL,
  `ltpp` varchar(50) default NULL,
  `f_name` varchar(50) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `cid` int(10) NOT NULL,
  PRIMARY KEY  (`install_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 