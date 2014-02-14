<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 *
 * @copyright  Glen Langer 2012..2014 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check 
 * @license    LGPL 
 * @filesource
 * @see	       https://github.com/BugBuster1701/integrity_check
 */

$GLOBALS['BE_MOD']['system']['integrity_check'] = array
(
        'tables'     => array('tl_integrity_check'),
        'icon'       => 'system/modules/integrity_check/html/integrity_check.png',
        'stylesheet' => 'system/modules/integrity_check/html/mod_integrity_check_be.css',
);

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('IntegrityCheckHelper', 'checkExtensions');

/**
 * -------------------------------------------------------------------------
 * CRON JOBS
 * -------------------------------------------------------------------------
 *
 * Register methods to be executed at certain intervals.
 */
$GLOBALS['TL_CRON']['monthly'][] = array('Integrity_Check', 'checkFilesMonthly');
$GLOBALS['TL_CRON']['weekly'][]  = array('Integrity_Check', 'checkFilesWeekly');
$GLOBALS['TL_CRON']['daily'][]   = array('Integrity_Check', 'checkFilesDaily');
//from contao 2.11, hourly is possible.
$GLOBALS['TL_CRON']['hourly'][]  = array('Integrity_Check', 'checkFilesHourly');


?>