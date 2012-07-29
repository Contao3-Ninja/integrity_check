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
 * CRON Output (de)
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['ok']       = 'Integritäts-Status für Datei %s ist: OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['corrupt']  = 'Integritäts-Status für Datei %s ist: beschädigt';
$GLOBALS['TL_LANG']['tl_integrity_check']['finished'] = 'Integritäts-Überprüfung der Dateien abgeschlossen.';

/**
 * Mail to admin
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['subject']   = 'Contao :: Integritäts-Überprüfung auf %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_1'] = 'Die Integritäts-Überprüfung auf %s hat beschädigte Dateien gefunden:';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'] = 'Diese Information ist auch im System-Log zu finden.';



?>