<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    Integrity_Check 
 * @license    LGPL 
 * @filesource
 */

$GLOBALS['BE_MOD']['system']['integrity_check'] = array
(
        'tables'     => array('tl_integrity_check'),
        'icon'       => 'system/modules/integrity_check/assets/integrity_check.png',
        'stylesheet' => 'system/modules/integrity_check/assets/mod_integrity_check_be.css',
);

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('IntegrityCheck\IntegrityCheckHelper', 'checkExtensions');

/**
 * -------------------------------------------------------------------------
 * CRON JOBS
 * -------------------------------------------------------------------------
 *
 * Register methods to be executed at certain intervals.
 */
$GLOBALS['TL_CRON']['monthly'][] = array('IntegrityCheck\Integrity_Check', 'checkFilesMonthly');
$GLOBALS['TL_CRON']['weekly'][]  = array('IntegrityCheck\Integrity_Check', 'checkFilesWeekly');
$GLOBALS['TL_CRON']['daily'][]   = array('IntegrityCheck\Integrity_Check', 'checkFilesDaily');
//from contao 2.11, hourly is possible.
$GLOBALS['TL_CRON']['hourly'][]  = array('IntegrityCheck\Integrity_Check', 'checkFilesHourly');
//from contao 3.0, minutely is possible.
$GLOBALS['TL_CRON']['minutely'][]  = array('IntegrityCheck\Integrity_Check', 'checkFilesMinutely');
