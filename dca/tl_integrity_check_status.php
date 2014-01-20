<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 *
 * Contao Module "Integrity Check" - Backend DCA tl_integrity_timestamps
 *
 * @copyright  Glen Langer 2012..2014 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/integrity_check
 */


/**
 * Table tl_integrity_check_status
 * 
 * check_object_status:    0=not tested, 1=ok, 2=not ok
 */
$GLOBALS['TL_DCA']['tl_integrity_check_status'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'            => 'Table',
        'sql' => array
        (
            'keys' => array
            (
                'id'    => 'primary',
                'pid,check_object'  => 'unique'
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
            'pid' => array
            (
                    'sql'           => "int(10) unsigned NOT NULL default '0'"
            ),
            'tstamp' => array
            (
                    'sql'           => "int(10) unsigned NOT NULL default '0'"
            ),
            'check_object' => array
            (
                    'sql'           => "varchar(255) NOT NULL default ''"
            ),
            'check_object_status' => array
            (
                    'sql'           => "tinyint(3) unsigned NOT NULL default '0'"
            )
    )
);

