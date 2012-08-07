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
 * CRON Output (en)
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['ok']       = 'Integrity status for file %s is: OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['corrupt']  = 'Integrity status for file %s is: Corrupt';
$GLOBALS['TL_LANG']['tl_integrity_check']['finished'] = 'Checking files for integrity is completed.';

/**
 * Mail to admin
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['subject']   = 'Contao :: Integrity-Check for %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_1'] = 'The integrity check for %s has found corrupt files:';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'] = 'This information can also be found in the system log.';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['new']    = array('New Integrity-Check', 'Add a new Integrity-Check');
$GLOBALS['TL_LANG']['tl_integrity_check']['edit']   = array('Edit Integrity-Check','Edit Integrity-Check');
$GLOBALS['TL_LANG']['tl_integrity_check']['delete'] = array('Delete Integrity-Check','Delete Integrity-Check');
$GLOBALS['TL_LANG']['tl_integrity_check']['show']   = array('Details Integrity-Check','Details Integrity-Check');
$GLOBALS['TL_LANG']['tl_integrity_check']['toggle'] = array('Publish/unpublish Integrity-Check', 'Publish/unpublish Integrity-Check');

$GLOBALS['TL_LANG']['tl_integrity_check']['check_title'] = array('Title','Title for Summary');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug'] = array('Debug','Activate debug mode');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans'] = array('Check-Plan','Planning of the checks and actions');

$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files']  = array('Files','Selecting files to check');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_moment'] = array('Time','Time of the check');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'] = array('Identification','Kind of identification');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action'] = array('Reaction','Kind of reaction');

?>