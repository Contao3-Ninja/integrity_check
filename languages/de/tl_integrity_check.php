<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * PHP version 5
 * @copyright  Glen Langer 2012..2015 
 * @author     Glen Langer 
 * @package    Integrity_Check 
 * @license    LGPL 
 * @filesource
 */

$GLOBALS['TL_LANG']['tl_integrity_check']['admin_email']                             = 'Mail an Admin';
$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail']['0']                     = 'Alternative Empfänger E-Mail-Adresse';
$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail']['1']                     = 'Bitte geben Sie eine gültige alternative E-Mail-Adresse ein für die Aktion E-Mails.';
$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail_legend']                   = 'Alternative E-Mail (optional)';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug']['0']                        = 'Debug';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug']['1']                        = 'Debug Modus aktivieren, zusätzliche Ausgaben in System-Log';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans']['0']                        = 'Überprüfungsplan';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans']['1']                        = 'Planung der Überprüfungen und Aktionen';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans_expert']['0']                 = 'Überprüfungsplan';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans_expert']['1']                 = 'Planung der Überprüfungen und Aktionen. Bei Neuauswahl bitte Zeitstempel aktualisieren.';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_title']['0']                        = 'Titel';
$GLOBALS['TL_LANG']['tl_integrity_check']['check_title']['1']                        = 'Titel für die Übersicht';
$GLOBALS['TL_LANG']['tl_integrity_check']['corrupt']                                 = 'Integritäts-Status für Datei %s ist: beschädigt';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action']['0']                          = 'Aktion';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action']['1']                          = 'Art der Reaktion';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status']                          = 'Status';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_0']                        = 'ungetestet';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_1']                        = 'OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_2']                        = 'beschädigt';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_3']                        = 'Warnung';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_file_status_4']                        = 'Datei nicht gefunden';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files']['0']                           = 'Dateien';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files']['1']                           = 'Dateien auswählen zur Überprüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval']['0']                        = 'Zeitpunkt';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval']['1']                        = 'Zeitpunkt der Überprüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_start_now_all']['0']                   = 'Alle Tests jetzt starten';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_start_now_all']['1']                   = 'Alle Tests jetzt starten';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_step_start_now']                       = 'Diesen Test jetzt starten';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test']['0']                    = 'Erkennung';
$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test']['1']                    = 'Art der Erkennung';
$GLOBALS['TL_LANG']['tl_integrity_check']['daily']                                   = 'täglich';
$GLOBALS['TL_LANG']['tl_integrity_check']['delete']['0']                             = 'Überprüfung löschen';
$GLOBALS['TL_LANG']['tl_integrity_check']['delete']['1']                             = 'Überprüfung löschen';
$GLOBALS['TL_LANG']['tl_integrity_check']['edit']['0']                               = 'Überprüfung bearbeiten';
$GLOBALS['TL_LANG']['tl_integrity_check']['edit']['1']                               = 'Überprüfung bearbeiten';
$GLOBALS['TL_LANG']['tl_integrity_check']['expert_legend']                           = 'Experten-Tests';
$GLOBALS['TL_LANG']['tl_integrity_check']['file_not_found']                          = 'Integritäts-Status für Datei %s ist: Datei nicht gefunden.';
$GLOBALS['TL_LANG']['tl_integrity_check']['finished']                                = 'Integritäts-Überprüfung der Dateien abgeschlossen.';
$GLOBALS['TL_LANG']['tl_integrity_check']['hourly']                                  = 'stündlich';
$GLOBALS['TL_LANG']['tl_integrity_check']['init']['0']                               = 'Neue Standard Integritäts-Überprüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['init']['1']                               = 'Ein Standard Integritäts-Überprüfung anlegen mit allen 4 Dateien';
$GLOBALS['TL_LANG']['tl_integrity_check']['initConfirm']                             = 'Neue Standard Integritäts-Überprüfung anlegen mit allen 4 Dateien?';
$GLOBALS['TL_LANG']['tl_integrity_check']['init_confirm_message']                    = 'Es wurde ein Integritäts-Check angelegt. Diesen bitte nun editieren und/oder aktivieren.';
$GLOBALS['TL_LANG']['tl_integrity_check']['install_count_check']['0']                = 'Prüfung auf Installtool Login-Sperre';
$GLOBALS['TL_LANG']['tl_integrity_check']['install_count_check']['1']                = 'Wenn das Installtool gesperrt wurde, nachdem dreimal hintereinander ein falsches Passwort eingegeben wurde, erfolgt eine E-Mail an den Admin.';
$GLOBALS['TL_LANG']['tl_integrity_check']['log_blocked']                             = 'Der System-Log Eintrag für die Datei %s wurde nicht durchgeführt.(blockiert)';
$GLOBALS['TL_LANG']['tl_integrity_check']['mail_blocked']                            = 'Die Mail für die Datei %s wurde nicht versendet.(blockiert)';
$GLOBALS['TL_LANG']['tl_integrity_check']['maintenance_mode']                        = 'Wartungsseite';
$GLOBALS['TL_LANG']['tl_integrity_check']['md5']                                     = 'MD5';
$GLOBALS['TL_LANG']['tl_integrity_check']['md5_blocked']                             = 'Integritäts-Überprüfung für die Datei %s wurde nicht durchgeführt.(keine Prüfsummen vorhanden. Update notwendig!)';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_1']                               = 'Die Integritäts-Überprüfung auf %s hat beschädigte Dateien gefunden:';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_2']                               = 'Diese Information ist auch im System-Log zu finden.';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_3']                               = 'Die Integritäts-Überprüfung auf %s hat keine passenden MD5 Prüfsummen. Ein Update ist nötig.';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_4']                               = 'Die Integritäts-Überprüfung auf %s hat festgestellt, dass ein neues Contao Update verfügbar ist: Version %s, installierte Version: %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['message_5']                               = 'Die Integritäts-Überprüfung auf %s hat festgestellt, dass das Installtool gesperrt wurde, nachdem dreimal hintereinander ein falsches Passwort eingegeben wurde.';
$GLOBALS['TL_LANG']['tl_integrity_check']['minutely']                                = 'minütlich';
$GLOBALS['TL_LANG']['tl_integrity_check']['monthly']                                 = 'monatlich';
$GLOBALS['TL_LANG']['tl_integrity_check']['new']['0']                                = 'Neue Integritäts-Überprüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['new']['1']                                = 'Einen neue Integritäts-Überprüfung anlegen';
$GLOBALS['TL_LANG']['tl_integrity_check']['ok']                                      = 'Integritäts-Status für Datei %s ist: OK';
$GLOBALS['TL_LANG']['tl_integrity_check']['only_logging']                            = 'System-Log';
$GLOBALS['TL_LANG']['tl_integrity_check']['publish_legend']                          = 'Veröffentlichung';
$GLOBALS['TL_LANG']['tl_integrity_check']['published']['0']                          = 'Integritäts-Überprüfung aktivieren';
$GLOBALS['TL_LANG']['tl_integrity_check']['published']['1']                          = 'Diese Integritäts-Überprüfung aktivieren.';
$GLOBALS['TL_LANG']['tl_integrity_check']['refresh']['0']                            = 'Zeitstempel aktualisieren';
$GLOBALS['TL_LANG']['tl_integrity_check']['refresh']['1']                            = 'Zeitstempel der Dateien aktualisieren';
$GLOBALS['TL_LANG']['tl_integrity_check']['refreshConfirm']                          = 'Zeitstempel der Dateien erneut erfassen? Alte Zeitstempel werden überschrieben.';
$GLOBALS['TL_LANG']['tl_integrity_check']['refresh_confirm_message']                 = 'Die Zeitstempel wurden aktualisiert.';
$GLOBALS['TL_LANG']['tl_integrity_check']['restore']                                 = 'Wiederherstellung';
$GLOBALS['TL_LANG']['tl_integrity_check']['show']['0']                               = 'Details der Überprüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['show']['1']                               = 'Details der Überprüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['subject']                                 = 'Contao :: Integritäts-Überprüfung auf %s';
$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp']                               = 'Zeitstempel';
$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp_not_found']                     = 'Integritäts-Status für Datei %s ist: Zeitstempel nicht vorhanden für diese Datei.';
$GLOBALS['TL_LANG']['tl_integrity_check']['toggle']['0']                             = 'Überprüfung veröffentlichen/unveröffentlichen';
$GLOBALS['TL_LANG']['tl_integrity_check']['toggle']['1']                             = 'Überprüfung veröffentlichen/unveröffentlichen';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check']['0']                       = 'Contao Update Prüfung';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check']['1']                       = 'Wenn ein neues Contao Update verfügbar ist (Minor/Bugfix), erfolgt eine E-Mail an den Admin.';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_installed']           = 'Installierte Contao Version';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_latest']              = 'Neuste Contao Version';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_latest_not_detected'] = 'Neuste Contao Version nicht ermittelt.';
$GLOBALS['TL_LANG']['tl_integrity_check']['update_check_deactivated']                = 'Contao Update Prüfung ist deaktiviert.';
$GLOBALS['TL_LANG']['tl_integrity_check']['weekly']                                  = 'wöchentlich';

