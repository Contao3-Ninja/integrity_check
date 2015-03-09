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
 * CRON Output (de)
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['ok']           = 'Integritäts-Status für Datei %s ist: OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['corrupt']      = 'Integritäts-Status für Datei %s ist: beschädigt';
$GLOBALS['TL_LANG']['tl_integrity_check']['finished']     = 'Integritäts-Überprüfung der Dateien abgeschlossen.';
$GLOBALS['TL_LANG']['tl_integrity_check']['mail_blocked'] = 'Die Mail für die Datei %s wurde nicht versendet.(blockiert)';
$GLOBALS['TL_LANG']['tl_integrity_check']['log_blocked']  = 'Der System-Log Eintrag für die Datei %s wurde nicht durchgeführt.(blockiert)';
$GLOBALS['TL_LANG']['tl_integrity_check']['md5_blocked']  = 'Integritäts-Überprüfung für die Datei %s wurde nicht durchgeführt.(keine Prüfsummen vorhanden. Update notwendig!)';
$GLOBALS['TL_LANG']['tl_integrity_check']['file_not_found']      = 'Integritäts-Status für Datei %s ist: Datei nicht gefunden.';
$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp_not_found'] = 'Integritäts-Status für Datei %s ist: Zeitstempel nicht vorhanden für diese Datei.';

/**
 * Mail to admin
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['subject']   = 'Contao :: Integritäts-Überprüfung auf %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_1'] = 'Die Integritäts-Überprüfung auf %s hat beschädigte Dateien gefunden:';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'] = 'Diese Information ist auch im System-Log zu finden.';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_3'] = 'Die Integritäts-Überprüfung auf %s hat keine passenden MD5 Prüfsummen. Ein Update ist nötig.';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_4'] = 'Die Integritäts-Überprüfung auf %s hat festgestellt, dass ein neues Contao Update verfügbar ist: Version %s, installierte Version: %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_5'] = 'Die Integritäts-Überprüfung auf %s hat festgestellt, dass das Installtool gesperrt wurde, nachdem dreimal hintereinander ein falsches Passwort eingegeben wurde.';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['new']    = array('Neue Integritäts-Überprüfung', 'Einen neue Integritäts-Überprüfung anlegen');
$GLOBALS['TL_LANG']['tl_integrity_check']['edit']   = array('Überprüfung bearbeiten','Überprüfung bearbeiten');
$GLOBALS['TL_LANG']['tl_integrity_check']['delete'] = array('Überprüfung löschen','Überprüfung löschen');
$GLOBALS['TL_LANG']['tl_integrity_check']['show']   = array('Details der Überprüfung','Details der Überprüfung');
$GLOBALS['TL_LANG']['tl_integrity_check']['toggle'] = array('Überprüfung veröffentlichen/unveröffentlichen', 'Überprüfung veröffentlichen/unveröffentlichen');
$GLOBALS['TL_LANG']['tl_integrity_check']['refresh']= array('Zeitstempel aktualisieren','Zeitstempel der Dateien aktualisieren');
$GLOBALS['TL_LANG']['tl_integrity_check']['init']   = array('Neue Standard Integritäts-Überprüfung','Ein Standard Integritäts-Überprüfung anlegen mit allen 4 Dateien');

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['check_title'] = array('Titel','Titel für die Übersicht');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug'] = array('Debug','Debug Modus aktivieren, zusätzliche Ausgaben in System-Log');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans'] = array('Überprüfungsplan','Planung der Überprüfungen und Aktionen');
$GLOBALS['TL_LANG']['tl_integrity_check']['published']   = array('Integritäts-Überprüfung aktivieren', 'Diese Integritäts-Überprüfung aktivieren.');
$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail'] = array('Alternative Empfänger E-Mail-Adresse','Bitte geben Sie eine gültige alternative E-Mail-Adresse ein für die Aktion E-Mails.');

$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files']        = array('Dateien','Dateien auswählen zur Überprüfung');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval']     = array('Zeitpunkt','Zeitpunkt der Überprüfung');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'] = array('Erkennung','Art der Erkennung');
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action']       = array('Aktion','Art der Reaktion');
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans_expert'] = array('Überprüfungsplan','Planung der Überprüfungen und Aktionen. Bei Neuauswahl bitte Zeitstempel aktualisieren.');
$GLOBALS['TL_LANG']['tl_integrity_check']['expert_legend']  = 'Experten-Tests';
$GLOBALS['TL_LANG']['tl_integrity_check']['publish_legend'] = 'Veröffentlichung';
$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail_legend'] = 'Alternative E-Mail (optional)';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status'] = 'Status';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_0'] = 'ungetestet';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_1'] = 'OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_2'] = 'beschädigt';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_3'] = 'Warnung';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_step_start_now'] = 'Diesen Test jetzt starten';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_start_now_all']  = array('Alle Tests jetzt starten','Alle Tests jetzt starten');

$GLOBALS['TL_LANG']['tl_integrity_check']['update_check'][0] = 'Contao Update Prüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check'][1] = 'Wenn ein neues Contao Update verfügbar ist (Minor/Bugfix), erfolgt eine E-Mail an den Admin.'; 
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_deactivated'] = 'Contao Update Prüfung ist deaktiviert.';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_latest'] = 'Neuste Contao Version';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_installed'] = 'Installierte Contao Version';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_latest_not_detected'] = 'Neuste Contao Version nicht ermittelt.';

$GLOBALS['TL_LANG']['tl_integrity_check']['install_count_check'][0] = 'Prüfung auf Installtool Login-Sperre';
$GLOBALS['TL_LANG']['tl_integrity_check']['install_count_check'][1] = 'Wenn das Installtool gesperrt wurde, nachdem dreimal hintereinander ein falsches Passwort eingegeben wurde, erfolgt eine E-Mail an den Admin.';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_integrity_check']['minutely'] = 'minütlich';
$GLOBALS['TL_LANG']['tl_integrity_check']['hourly']   = 'stündlich';
$GLOBALS['TL_LANG']['tl_integrity_check']['daily']    = 'täglich';
$GLOBALS['TL_LANG']['tl_integrity_check']['weekly']   = 'wöchentlich';
$GLOBALS['TL_LANG']['tl_integrity_check']['monthly']  = 'monatlich';

$GLOBALS['TL_LANG']['tl_integrity_check']['md5']       = 'MD5';
$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'] = 'Zeitstempel';

$GLOBALS['TL_LANG']['tl_integrity_check']['only_logging']     = 'System-Log';
$GLOBALS['TL_LANG']['tl_integrity_check']['admin_email']      = 'Mail an Admin';
$GLOBALS['TL_LANG']['tl_integrity_check']['restore']          = 'Wiederherstellung'; //future feature ?
$GLOBALS['TL_LANG']['tl_integrity_check']['maintenance_mode'] = 'Wartungsseite'; //future feature ?

$GLOBALS['TL_LANG']['tl_integrity_check']['refreshConfirm']          = 'Zeitstempel der Dateien erneut erfassen? Alte Zeitstempel werden überschrieben.';
$GLOBALS['TL_LANG']['tl_integrity_check']['refresh_confirm_message'] = 'Die Zeitstempel wurden aktualisiert.';

$GLOBALS['TL_LANG']['tl_integrity_check']['initConfirm']          = 'Neue Standard Integritäts-Überprüfung anlegen mit allen 4 Dateien?';
$GLOBALS['TL_LANG']['tl_integrity_check']['init_confirm_message'] = 'Es wurde ein Integritäts-Check angelegt. Diesen bitte nun editieren und/oder aktivieren.';
