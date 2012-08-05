<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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

/**
 * -------------------------------------------------------------------------
 * CRON JOBS
 * -------------------------------------------------------------------------
 *
 * Register methods to be executed at certain intervals.
 * 
 *   weekly = run once a week
 *   daily  = run once a day
 *   hourly = run every hour
 */
//$GLOBALS['TL_CRON']['weekly'][] = array('Integrity_Check', 'checkFiles');
$GLOBALS['TL_CRON']['daily'][]  = array('Integrity_Check', 'checkFiles');

//from contao 2.11, hourly is possible.
//$GLOBALS['TL_CRON']['hourly'][] = array('Integrity_Check', 'checkFiles');


/**
 * DEBUG Modus, default: Off
 */
$GLOBALS['TL_CONFIG']['mod_integrity_check']['debug'] = false;

/**
 * eMail to Admin, default Off
 * use: $GLOBALS['TL_CONFIG']['adminEmail']
 */
$GLOBALS['TL_CONFIG']['mod_integrity_check']['send_email_to_admin'] = false;

?>