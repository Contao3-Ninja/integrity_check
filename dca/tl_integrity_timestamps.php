<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 *
 * Contao Module "Integrity Check" - Backend DCA tl_integrity_timestamps
 *
 * @copyright  Glen Langer 2012..2016 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/integrity_check
 */


/**
 * Table tl_integrity_check
 */
$GLOBALS['TL_DCA']['tl_integrity_timestamps'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'            => 'Table',
        'sql' => array
        (
            'keys' => array
            (
                'id'    => 'primary'
            )
        ) 
	),
    // Fields
    'fields' => array
    (
            'id' => array
            (
                    'sql'           => "int(10) unsigned NOT NULL auto_increment"
            ),
            'tstamp' => array
            (
                    'sql'           => "int(10) unsigned NOT NULL default '0'"
            ),
            'check_timestamps' => array
            (
                    'sql'           => "varchar(255) NOT NULL default ''"
            ),
            'last_mail_tstamps' => array
            (
                    'sql'           => "varchar(255) NOT NULL default ''"
            ),
            'last_minutely_log'     => array
            (
                    'sql'           => "varchar(255) NOT NULL default ''"
            ),
            'last_mail_md5_block'   => array
            (
                    'sql'           => "int(10) unsigned NOT NULL default '0'"
            ),
            'latest_contao_version' => array
            (
                    'sql'           => "varchar(12) NOT NULL default ''"
            )
    )
);

