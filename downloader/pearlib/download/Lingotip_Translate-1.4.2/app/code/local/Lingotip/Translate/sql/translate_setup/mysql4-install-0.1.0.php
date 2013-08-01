<?php

$installer = $this;

$installer->startSetup();

$installer->run("


DROP TABLE IF EXISTS {$this->getTable('lt_config')};
CREATE TABLE {$this->getTable('lt_config')} (
  `install_id` int(10) NOT NULL auto_increment,
  `lturl` varchar(50) default NULL,
  `ltpp` varchar(50) default NULL,
  `f_name` varchar(50) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `cid` int(10) unsigned default '0',
  PRIMARY KEY  (`install_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS {$this->getTable('lt_dispute')};
CREATE TABLE {$this->getTable('lt_dispute')} (
  `dispute_id` int(10) NOT NULL auto_increment,
  `txn` int(10) default NULL,
  `entrydate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `mode` varchar(10) default NULL,
  `note` text,
  `stat` varchar(10) default 'pending',
  PRIMARY KEY  (`dispute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS {$this->getTable('lt_languagepair')};
CREATE TABLE {$this->getTable('lt_languagepair')} (
  `id` int(10) NOT NULL auto_increment,
  `source` varchar(255) default NULL,
  `targetlan` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


DROP TABLE IF EXISTS {$this->getTable('lt_notes')};
CREATE TABLE {$this->getTable('lt_notes')} (
  `notes_id` int(10) NOT NULL auto_increment,
  `txn` int(10) default NULL,
  `entrydate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `note` text,
  `stat` varchar(10) default 'new',
  `incoming` varchar(2) default '0',
  `is_dispute` varchar(2) default '0',
  `tid` varchar(10) default NULL,
  PRIMARY KEY  (`notes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('lt_estimate')};
CREATE TABLE {$this->getTable('lt_estimate')} (
  `translate_id` int(10) unsigned NOT NULL auto_increment,
  `price` float(6,2) default NULL,
  `subtotal` varchar(100) default NULL,
  `paid` varchar(20) default NULL,
  `word_count` varchar(20) default NULL,
  `level_id` tinyint(2) default NULL,
  `source` longblob,
  `src_name` varchar(20) default NULL,
  `trg_names` varchar(100) default NULL,
  PRIMARY KEY  (`translate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS {$this->getTable('lt_requests')};
CREATE TABLE {$this->getTable('lt_requests')} (
  `request_id` int(10) NOT NULL auto_increment,
  `rid` int(10) NOT NULL,
  `price` float(6,2) default NULL,
  `paid` varchar(10) default 'no',
  `word_count` varchar(20) default NULL,
  `entrydate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `note` text,
  `label` text,
  `src_name` varchar(20) default NULL,
  `trg_names` varchar(100) default NULL,
  `txns` varchar(100) default NULL,
  `level_id` tinyint(2) default NULL,
  `source` longblob,
  `pending` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('lt_txn')};
CREATE TABLE {$this->getTable('lt_txn')} (
  `txn_id` int(10) NOT NULL auto_increment,
  `txn` int(10) default '0',
  `code` varchar(10) default NULL,
  `status` varchar(20) default 'pending',
  `price` float(6,2) default NULL,
  `paid` varchar(10) default 'no',
  `trg_name` varchar(20) default NULL,
  `target` longblob,
  `dispute` tinyint(2) default '0',
  `feedback` tinyint(2) default '0',
  `request_id` int(10) NOT NULL,
   PRIMARY KEY  (`txn_id`),
  CONSTRAINT `FK_LT_TXN` FOREIGN KEY (`request_id`) REFERENCES {$this->getTable('lt_requests')} (`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





    ");

$installer->endSetup(); 