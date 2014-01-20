<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * PHP version 5
 * @copyright  Glen Langer 2012..2014 
 * @author     Glen Langer 
 * @package    Integrity_Check 
 * @license    LGPL 
 * @filesource
 */

/**
 * CRON Output (en)
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['ok']           = 'Integrity status for file %s is: OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['corrupt']      = 'Integrity status for file %s is: Corrupt';
$GLOBALS['TL_LANG']['tl_integrity_check']['finished']     = 'Checking files for integrity is completed.';
$GLOBALS['TL_LANG']['tl_integrity_check']['mail_blocked'] = 'The mail for the file %s was not sent. (blocked)';
$GLOBALS['TL_LANG']['tl_integrity_check']['log_blocked']  = 'The system log entry for the file %s was not performed.(blocked)';
$GLOBALS['TL_LANG']['tl_integrity_check']['md5_blocked']  = 'Checking file %s for integrity was not performed.(No checksums available. Update is necessary!)';
$GLOBALS['TL_LANG']['tl_integrity_check']['file_not_found']      = 'Integrity status for file %s is: File not found.';
$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp_not_found'] = 'Integrity status for file %s is: Timestamp not found for this file.';

/**
 * Mail to admin
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['subject']   = 'Contao :: Integrity Check for %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_1'] = 'The integrity check for %s has found corrupt files:';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'] = 'This information can also be found in the system log.';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_3'] = 'The integrity check for %s has found no matching MD5 checksums. An update is necessary.';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['new']    = array('New integrity check', 'Add a new integrity check');
$GLOBALS['TL_LANG']['tl_integrity_check']['edit']   = array('Edit integrity check','Edit integrity check');
$GLOBALS['TL_LANG']['tl_integrity_check']['delete'] = array('Delete integrity check','Delete integrity check');
$GLOBALS['TL_LANG']['tl_integrity_check']['show']   = array('Details integrity check','Details integrity check');
$GLOBALS['TL_LANG']['tl_integrity_check']['toggle'] = array('Activate/Deactivate integrity check', 'Activate/Deactivate integrity check');
$GLOBALS['TL_LANG']['tl_integrity_check']['refresh']= array('Update timestamps','Update the timestamp of the files.');
$GLOBALS['TL_LANG']['tl_integrity_check']['init']   = array('New default integrity check','Add a new default integrity check with all 4 files.');

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['check_title'] = array('Title','Title for Summary');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug'] = array('Debug','Activate debug mode, extended logging to system log');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans'] = array('Check plan','Planning of the checks and actions');
$GLOBALS['TL_LANG']['tl_integrity_check']['published']   = array('Activate integrity check', 'Activate the integrity check.');
$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail'] = array('Alternative recipient e-mail address','Please enter a valid e-mail address for the action e-mails.');

$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files']        = array('Files','Selecting files to check');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval']     = array('Time','Time of the check');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'] = array('Identification','Kind of identification');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action']       = array('Action','Kind of action');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans_expert'] = array('Check plan','Planning of the checks and actions. When reselecting please update the timestamps.');
$GLOBALS['TL_LANG']['tl_integrity_check']['expert_legend']  = 'Expert checks';
$GLOBALS['TL_LANG']['tl_integrity_check']['publish_legend'] = 'Publish settings';
$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail_legend'] = 'Alternative e-mail (optional)';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status'] = 'Status';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_0'] = 'unchecked';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_1'] = 'OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_2'] = 'corrupt';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['minutely'] = 'minutely';
$GLOBALS['TL_LANG']['tl_integrity_check']['hourly']   = 'hourly';
$GLOBALS['TL_LANG']['tl_integrity_check']['daily']    = 'daily';
$GLOBALS['TL_LANG']['tl_integrity_check']['weekly']   = 'weekly';
$GLOBALS['TL_LANG']['tl_integrity_check']['monthly']  = 'monthly';

$GLOBALS['TL_LANG']['tl_integrity_check']['md5']       = 'MD5';
$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'] = 'Timestamp';

$GLOBALS['TL_LANG']['tl_integrity_check']['only_logging']     = 'System log';
$GLOBALS['TL_LANG']['tl_integrity_check']['admin_email']      = 'Mail to Admin';
$GLOBALS['TL_LANG']['tl_integrity_check']['restore']          = 'Restore'; //future feature ?
$GLOBALS['TL_LANG']['tl_integrity_check']['maintenance_mode'] = 'Maintenance Page'; //future feature ?

$GLOBALS['TL_LANG']['tl_integrity_check']['refreshConfirm']          = 'Update the timestamp of the files? Old timestamps will be overwritten.';
$GLOBALS['TL_LANG']['tl_integrity_check']['refresh_confirm_message'] = 'The timestamps have been updated.';

$GLOBALS['TL_LANG']['tl_integrity_check']['initConfirm']          = 'Add a new default integrity check with all 4 files?';
$GLOBALS['TL_LANG']['tl_integrity_check']['init_confirm_message'] = 'It has been created an integrity check. Please modify and/or activate.';
