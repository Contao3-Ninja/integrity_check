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

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['new']    = array('Neue Integritäts-Überprüfung', 'Einen neue Integritäts-Überprüfung anlegen');
$GLOBALS['TL_LANG']['tl_integrity_check']['edit']   = array('Überprüfung bearbeiten','Überprüfung bearbeiten');
$GLOBALS['TL_LANG']['tl_integrity_check']['delete'] = array('Überprüfung löschen','Überprüfung löschen');
$GLOBALS['TL_LANG']['tl_integrity_check']['show']   = array('Details der Überprüfung','Details der Überprüfung');
$GLOBALS['TL_LANG']['tl_integrity_check']['toggle'] = array('Überprüfung veröffentlichen/unveröffentlichen', 'Überprüfung veröffentlichen/unveröffentlichen');

$GLOBALS['TL_LANG']['tl_integrity_check']['check_title'] = array('Titel','Titel für die Übersicht');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug'] = array('Debug','Debug Modus aktivieren');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans'] = array('Überprüfungsplan','Planung der Überprüfungen und Aktionen');

$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files']  = array('Dateien','Dateien auswählen zur Überprüfung');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_moment'] = array('Zeitpunkt','Zeitpunkt der Überprüfung');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'] = array('Erkennung','Art der Erkennung');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action'] = array('Action','Art der Reaktion');
 
?>