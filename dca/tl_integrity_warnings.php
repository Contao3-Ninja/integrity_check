<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 *
 * Contao Module "Integrity Check" - Backend DCA tl_integrity_warnings
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
$GLOBALS['TL_DCA']['tl_integrity_warnings'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'            => 'Table',
        'sql' => array
        (
            'keys' => array
            (
                'id'                    => 'primary',
                'latest_contao_version' => 'unique'
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
            'latest_contao_version' => array
            (
                    'sql'           => "varchar(12) NOT NULL default ''"
            ),
            'install_count_check' => array
            (
                    'sql'           => "char(1) NOT NULL default ''"
            )
    )
);

