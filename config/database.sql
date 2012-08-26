-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- --------------------------------------------------------

-- --------------------------------------------------------
 
-- 
-- Table `tl_integrity_check`
-- 
 
CREATE TABLE `tl_integrity_check` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `check_debug` char(1) NOT NULL default '',
  `check_plans` blob NULL,
  `check_title` varchar(255) NOT NULL default '',
  `published` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
-- --------------------------------------------------------
 
-- 
-- Table `tl_integrity_timestamps`
-- 
 
CREATE TABLE `tl_integrity_timestamps` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `check_timestamps` varchar(255) NOT NULL default '',
  `last_mail_tstamps` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
    