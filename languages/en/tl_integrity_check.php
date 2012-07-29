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
$GLOBALS['TL_LANG']['tl_integrity_check']['finished'] = 'Check files for integrity is finished.';

/**
 * Mail to admin
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['subject']   = 'Contao :: Integrity-Check for %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_1'] = 'The integrity check for %s has found corrupt files:';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'] = 'This information can also be found in the system log.';



?>