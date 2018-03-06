<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2017 Leo Feyer
 *
 * Contao Module "Integrity Check"
 *
 * @copyright  Glen Langer 2012..2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/integrity_check
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace BugBuster\IntegrityCheck;

/**
 * Class IntegrityCheck
 *
 * Cronjob for integrity check
 *
 * @copyright  Glen Langer 2012..2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @author     Leo Feyer (sourcecode parts from contao check tool)
 * @package    Integrity_Check
 */
class IntegrityCheck extends \Frontend
{
    protected $fileEmailStatus = array();
    protected $fileLogStatus   = array();

    protected $check_debug        = false;
    protected $check_plan_id      = 0;
    protected $check_plans        = array();
    protected $check_plans_expert = array();
    protected $check_title        = '';
    protected $check_alternate_email = false;
    protected $check_update          = false;
    protected $check_install_count   = false;
    protected $last_mail   = array();
    protected $md5_block   = false;

    protected $cron_interval = '';


    const LATEST_VERSION = '3.5.34';
    const MESSAGE_CONTAO_UPDATE = 4;
    const MESSAGE_INSTALL_COUNT = 5;

    /**
     * Filelist with checksums
     * @var array    file,checksum_file,checksum_code
     */
    protected $file_list   = array();

    /**
     *  CRON Minutely Call
     */
    public function checkFilesMinutely()
    {
    	$this->cron_interval = 'minutely';
    	$this->run();
    }

    /**
     *  CRON Hourly Call
     */
    public function checkFilesHourly()
    {
        $this->cron_interval = 'hourly';
        $this->run();
    }
    /**
     *  CRON Daily Call
     */
    public function checkFilesDaily()
    {
        $this->cron_interval = 'daily';
        $this->run();
    }
    /**
     *  CRON Weekly Call
     */
    public function checkFilesWeekly()
    {
        $this->cron_interval = 'weekly';
        $this->run();
    }
    /**
     *  CRON Monthly Call
     */
    public function checkFilesMonthly()
    {
        $this->cron_interval = 'monthly';
        $this->run();
    }

    protected function run()
    {
        $this->loadLanguageFile('tl_integrity_check');
        $this->getCheckPlan();
        $this->checkFiles();

        //Contao Update Check
        $retUpCh = $this->checkContaoUpdate();
        if ($retUpCh && $this->getWarningMailBlock($retUpCh) === false) //not blocked
        {
            $this->sendWarningMail($this::MESSAGE_CONTAO_UPDATE, $retUpCh, VERSION . '.' . BUILD);
            $this->setWarningMailBlock($retUpCh);
        }

        if (true === (bool) $this->check_debug)
        {
            $this->log('installCount: '.print_r($GLOBALS['TL_CONFIG']['installCount'],true), 'IntegrityCheck run()', TL_CRON);
        }
        //Contao Install Count Check
        //1. prüfe ob install_count_check=3 aus warning Tabelle
        if ( $this->getWarningMailBlock('',3) === true )
        {
            // - ja   -> Warnmail bereits erfolgt
            //        -> $GLOBALS['TL_CONFIG']['installCount'] <3 ? dann install_count_check auf Wert setzen, raus
            if ( $GLOBALS['TL_CONFIG']['installCount'] < 3 )
            {
                $this->setWarningMailBlock('',$GLOBALS['TL_CONFIG']['installCount']);
                $this->setCheckStatus('install_count_check', true);
            }
        }
        else
        {
            // - nein -> $GLOBALS['TL_CONFIG']['installCount'] <3 ? dann install_count_check auf Wert setzen, raus
            if ( $GLOBALS['TL_CONFIG']['installCount'] < 3 )
            {
                $this->setWarningMailBlock('',$GLOBALS['TL_CONFIG']['installCount']);
                $this->setCheckStatus('install_count_check', true);
            }
            else
            {
                //-> $GLOBALS['TL_CONFIG']['installCount'] =3 ? dann mail und dann install_count_check auf 3 setzen, raus
                $this->sendWarningMail($this::MESSAGE_INSTALL_COUNT);
                $this->setWarningMailBlock('',$GLOBALS['TL_CONFIG']['installCount']);
                $this->setCheckStatus('install_count_check', 3);
            }
        }


    }

	/**
	 * Check files for integrity
	 */
	protected function checkFiles()
	{
	    $this->file_list = \IntegrityCheck\IntegrityCheckHelper::getInstance()->getFileList();
	    $checkSummary = false; //false=kein check erfolgt, keine Mail, keine completed Meldung
	    $checkSummary_expert = false;

	    //Zeilenweise den Plan durchgehen
	    foreach ($this->check_plans as $check_plan_step)
	    {
	        if ($this->cron_interval == $check_plan_step['cp_interval'])
	        {
	            $resMD5 = false;
	            $resTS  = false;
	            //diese Datei muss jetzt geprüft werden.
	            switch ($check_plan_step['cp_type_of_test'])
	            {
	                case 'md5' :
	                    $resMD5 = $this->checkFileMD5($check_plan_step['cp_files'], $check_plan_step['cp_action']);
	                    break;
	                case 'timestamp' :
	                    $resTS = $this->checkFileTimestamp($check_plan_step['cp_files'], $check_plan_step['cp_action']);
	                    break;
	            }
	            //einmal false immer true
	            $checkSummary = ($resMD5 === false || $resTS === false) ? true : $checkSummary;
	        } //moment
	    } //foreach plan step

	    if ( count( (array)$this->check_plans_expert ) > 0 )
	    {
    	    //Zeilenweise den Expert Plan durchgehen
    	    foreach ($this->check_plans_expert as $check_plan_step)
    	    {
    	        if ($this->cron_interval == $check_plan_step['cp_interval_expert'])
    	        {
    	            $resMD5 = false;
    	            $resTS  = false;
    	            //diese Datei muss jetzt geprüft werden.
    	            switch ($check_plan_step['cp_type_of_test_expert'])
    	            {
    	                case 'md5' :
    	                    $resMD5 = $this->checkFileMD5($check_plan_step['cp_files_expert'], $check_plan_step['cp_action_expert']);
    	                    break;
    	                case 'timestamp' :
    	                    $resTS = $this->checkFileTimestamp($check_plan_step['cp_files_expert'], $check_plan_step['cp_action_expert']);
    	                    break;
    	            }
    	            //einmal false immer true
    	            $checkSummary_expert = ($resMD5 === false || $resTS === false) ? true : $checkSummary_expert;
                } //moment
            } //foreach plan step
        }

	    //Log / Mail wenn notwendig
	    if ($checkSummary || $checkSummary_expert)
	    {
	    	$this->sendCheckLog();
            $this->sendCheckEmail();
            if (true === (bool) $this->check_debug)
            {
                // Add log entry
                $this->log('['.$this->check_title .'] '. $GLOBALS['TL_LANG']['tl_integrity_check']['finished'], 'IntegrityCheck checkFiles()', TL_CRON);
            }
	    }
	}

	/**
	 * Check files via MD5
	 *
	 * @param string $cp_file
	 * @param string $cp_action
	 * @return bool	 true = file is corrupt, false = file is not corrupt or no check
	 */
	protected function checkFileMD5($cp_file, $cp_action)
	{
	    $file_not_found = false;

	    if ($cp_file == '')
	    {
	        return false; // kein check
	    }

	    if ( version_compare(VERSION.'.'.BUILD, $this::LATEST_VERSION, '>') )
	    {
	        if (true === (bool) $this->check_debug)
	        {
	            // Add log entry
	            $this->log('['.$this->check_title .'] '. sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['md5_blocked'], $cp_file), 'IntegrityCheck checkFiles()', TL_ERROR);
	        }
	        // Mail to Admin
	        $this->sendCheckEmailMD5Block();
	        return false; // kein check
	    }

	    $status = true;

	    foreach ($this->file_list as $files)
	    {
	        if (count($files)==2)
	        {
	            //new variant
	            list($file, $md5_file) = $files;
	        }
	        else
	        {
	            //old variant
	            list($file, $md5_file, $md5_code) = $files;
	            unset($md5_code);
	        }
	        if ($file == $cp_file)
	        {
	            break; // gefunden
	        }
	    }

        if (is_file(TL_ROOT . '/' . $cp_file))
        {
            $buffer = str_replace("\r", '', file_get_contents(TL_ROOT . '/' . $cp_file));
            $status = true;
            //Check the content
            if (strncmp(md5($buffer), $md5_file, 10) !== 0)
            {
            	$status = false;
            }
            unset($buffer);
        }
        else
        {
            $file_not_found = true;
        }

        //Ergebniss verarbeiten
        if ($status === false)
        {
            //File corrupt
            switch ($cp_action)
            {
                case 'admin_email' :
                    $this->fileEmailStatus[$cp_file] = true; // true = mail
                    //wenn mail dann auch log
                    $this->fileLogStatus[$cp_file] = 'md5'; // true = log
                    break;
                case 'only_logging':
                	$this->fileLogStatus[$cp_file] = 'md5'; // true = log
                    break;
            }
        }
        elseif (true === (bool) $this->check_debug && $file_not_found === false)
        {
            $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'IntegrityCheck checkFileMD5()', TL_CRON);
        }
        //nur wenn getestet werden konnte
        if ($file_not_found === false)
        {
            $this->setCheckStatus($cp_file, $status);
        }
        else
        {
            $this->setCheckStatus($cp_file, 4); //nicht pruefbar
            $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['file_not_found'], $cp_file), 'IntegrityCheck checkFileMD5()', TL_CRON);
        }

        return $status;
	}

	/**
	 * Check files via timestamp
	 *
	 * @param string $cp_file
	 * @param string $cp_action
	 * @return bool	 true = file is corrupt, false = file is not corrupt or no check
	 */
	protected function checkFileTimestamp($cp_file, $cp_action)
	{
	    $cp_file_ts = 0;

	    if ($cp_file == '')
	    {
	        return false; // kein check
	    }

	    if (is_file(TL_ROOT . '/' . $cp_file))
	    {
	        $objFile = new \File($cp_file);
	        $cp_file_ts = $objFile->mtime;
	        $objFile->close();
	    }
	    else
	    {
	        $this->setCheckStatus($cp_file, 4);// nicht pruefbar
	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['file_not_found'], $cp_file), 'IntegrityCheck checkFileTimestamp()', TL_ERROR);
	        return false; // kein check möglich
	    }

	    $objTimestamps = \Database::getInstance()->prepare("SELECT `check_timestamps` FROM `tl_integrity_timestamps` WHERE `id`=?")
	                                             ->execute(1);
	    if ($objTimestamps->numRows < 1)
	    {
	        $this->setCheckStatus($cp_file, 0);// nicht pruefbar
	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['timestamp_not_found'], $cp_file), 'IntegrityCheck checkFileTimestamp()', TL_ERROR);
	        return false; // kein check möglich
	    }

	    $status = true;
	    $arrTimestamps = deserialize($objTimestamps->check_timestamps);

	    if ( !isset($arrTimestamps[$cp_file]) )
	    {
	        $this->setCheckStatus($cp_file, 0);// nicht pruefbar
	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['timestamp_not_found'], $cp_file), 'IntegrityCheck checkFileTimestamp()', TL_ERROR);
	        return false; // kein check möglich
	    }


	    //Ergebniss verarbeiten, Datei und Zeitstempel vorhanden
	    if ( $cp_file_ts != $arrTimestamps[$cp_file])
	    {
	    	$status = false;
	        //File corrupt
	        switch ($cp_action)
	        {
	            case 'admin_email' :
	                $this->fileEmailStatus[$cp_file] = true; // true = mail
	                //wenn mail dann auch log
	                $this->fileLogStatus[$cp_file] = 'timestamp'; // true = log
	                break;
	            case 'only_logging':
	            	$this->fileLogStatus[$cp_file] = 'timestamp'; // true = log
	                break;
	        }
	    }
        elseif (true === (bool) $this->check_debug)
	    {
	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'IntegrityCheck checkFileTimestamp()', TL_CRON);
	    }

        $this->setCheckStatus($cp_file, $status);
	    return $status;
	}

	/**
	 * Get check plan from DB
	 */
	private function getCheckPlan()
	{
	    $objCheckPlan = \Database::getInstance()
                            ->prepare("SELECT
                                            `id`,
                                            `check_debug`,
                                            `check_plans`,
                                            `check_plans_expert`,
                                            `check_title` ,
                                            `alternate_email`,
                                            `update_check`,
                                            `install_count_check`
                                        FROM
                                            `tl_integrity_check`
                                        WHERE
                                            `published`=?"
                                    )
                            ->execute(1);
	    if ($objCheckPlan->numRows < 1)
	    {
	        return ;
	    }
	    $this->check_plan_id         = $objCheckPlan->id;
	    $this->check_debug           = ($objCheckPlan->check_debug) ? 1 : 0;
	    $this->check_plans           = deserialize($objCheckPlan->check_plans);
	    $this->check_plans_expert    = deserialize($objCheckPlan->check_plans_expert);
	    $this->check_title           = $objCheckPlan->check_title;
	    $this->check_alternate_email = ($objCheckPlan->alternate_email) ? $objCheckPlan->alternate_email : false;
	    $this->check_update          = ($objCheckPlan->update_check) ? 1 : 0;
	    $this->check_install_count   = ($objCheckPlan->install_count_check) ? 1 : 0;
	    return ;
	}

	/**
	 * Send eMail to Admin when files are corrupt
	 */
	private function sendCheckEmail()
	{
	    if (!isset($GLOBALS['TL_CONFIG']['adminEmail']))
	    {
	        return; //admin email not set, needed for sender and recipient
	    }
	    $bolLastMail = false;
	    $arrFiles = array('index.php'=>0,'system/cron/cron.php'=>0,'contao/index.php'=> 0,'contao/main.php'=> 0,'.htaccess'=> 0);
	    $objLastMail = \Database::getInstance()->prepare("SELECT `last_mail_tstamps` FROM `tl_integrity_timestamps` WHERE `id`=?")
	                                           ->executeUncached(2);
	    if ($objLastMail->numRows >0)
	    {
	        $arrFiles = array_merge($arrFiles, deserialize($objLastMail->last_mail_tstamps));
	        $bolLastMail = true;
	    }
	    $time_block = time() - (24 * 60 * 60); // -24h

	    //////////////// MAIL OUT \\\\\\\\\\\\\\\\
	    $sendmail = false;
	    // Notification
	    list($ADMIN_NAME, $ADMIN_EMAIL) = \StringUtil::splitFriendlyEmail($GLOBALS['TL_CONFIG']['adminEmail']); //from index.php
	    $objEmail = new \Email();
	    $objEmail->from     = $ADMIN_EMAIL;
	    $objEmail->fromName = $ADMIN_NAME;

	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host . $this->Environment->path);
	    $objEmail->text     = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['message_1'], $this->Environment->host . $this->Environment->path);

	    foreach ($this->fileEmailStatus as $key => $value) // file => true/false
	    {
	        if ($value === true)
	        {
	            $objEmail->text .= "\n* ".$key;
	            $sendmail_temp = true;
	            //nur wenn die letzte Mail 24h her ist für diese Datei
	            if ($arrFiles[$key] > $time_block)
	            {
	                $sendmail_temp = false;
    	            if (true === (bool) $this->check_debug)
                    {
                        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['mail_blocked'], $key), 'IntegrityCheck sendCheckEmail()', TL_CRON);
                    }
	            }
                //wenn mail dann timestamp erneuern
                if ($sendmail_temp)
                {
                    $arrFiles[$key] = time();
                    $sendmail = true;
                }
	        }
	    }

	    if ($sendmail)
	    {
    	    $objEmail->text .= "\n\n".$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'];
    	    $objEmail->text .= "\n[".date($GLOBALS['TL_CONFIG']['datimFormat'])."]";

    	    //Admin eMail or alternative eMail
    	    if ($this->check_alternate_email !== false)
    	    {
    	        $objEmail->sendTo($this->check_alternate_email);
    	    }
    	    else
    	    {
    	        $objEmail->sendTo($GLOBALS['TL_CONFIG']['adminEmail']);
    	    }

    	    if ($bolLastMail)
    	    {
    	        //update
    	        \Database::getInstance()->prepare("UPDATE tl_integrity_timestamps SET tstamp=?,last_mail_tstamps=? WHERE id=?")
    	                                ->execute(time(), serialize($arrFiles), 2);
    	    }
    	    else
    	    {
    	        //insert
    	        \Database::getInstance()->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `last_mail_tstamps` )
    	                                           VALUES (?, ?, ?)")
    	                                ->execute(2, time(), serialize($arrFiles));
    	    }
	    }
	    unset($objEmail);
	    return ;

	}//sendCheckEmail

	/**
	 * System Log Entry
	 */
	private function sendCheckLog()
	{
	    $bolLastLog = false;
	    $arrFiles = array('index.php'=>0,'system/cron/cron.php'=>0,'contao/index.php'=> 0,'contao/main.php'=> 0,'.htaccess'=> 0);
	    $objLastLog = \Database::getInstance()->prepare("SELECT `last_minutely_log` FROM `tl_integrity_timestamps` WHERE `id`=?")
	    							          ->executeUncached(3);
	    if ($objLastLog->numRows >0)
	    {
	        $arrFiles = array_merge($arrFiles, deserialize($objLastLog->last_minutely_log));
	        $bolLastLog = true;
	    }
	    $time_block = time() - (60 * 60); // -1h
	    $sendlog = false;

	    foreach ($this->fileLogStatus as $key => $value) // file => kind of test
	    {
	        if (true === (bool) $value) // md5 || timestamp
	        {
	        	$sendLog_temp = true;

	        	//nur wenn das letzte Log 1h her ist für diese Datei
	        	if ($arrFiles[$key] > $time_block)
	        	{
	        	    $sendLog_temp = false;
	        	    if (true === (bool) $this->check_debug)
	        	    {
	        	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['log_blocked'], $key), 'IntegrityCheck sendCheckLog()', TL_CRON);
	        	    }
	        	}

	        	//log?
	        	if ($sendLog_temp)
	        	{
	        		if ($value == 'md5')
	        		{
	        			$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $key) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'IntegrityCheck checkFileMD5()', TL_ERROR);
	        		}
	        		else
	        		{
	        			$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $key) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'IntegrityCheck checkFileTimestamp()', TL_ERROR);
	        		}
	        		//wenn log dann timestamp erneuern
	        	    $arrFiles[$key] = time();
	        	    $sendlog = true;
	        	}
	        }
	    }

	    //timestamp eintragen
	    if ($sendlog)
	    {
	        if ($bolLastLog)
	        {
	            //update
	            \Database::getInstance()->prepare("UPDATE tl_integrity_timestamps SET tstamp=?,last_minutely_log=? WHERE id=?")
	            			            ->execute(time(), serialize($arrFiles), 3);
	        }
	        else
	        {
	            //insert
	            \Database::getInstance()->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `last_minutely_log` )
	                   					           VALUES (?, ?, ?)")
	                                    ->execute(3, time(), serialize($arrFiles));
	        }
	    }// if sendlog
	}// sendCheckLog()

	/**
	 * Send eMail to Admin, an update is necessary of integrity check
	 */
	private function sendCheckEmailMD5Block()
	{
	    if (!isset($GLOBALS['TL_CONFIG']['adminEmail']))
	    {
	        return; //admin email not set, needed for sender and recipient
	    }
	    if ($this->md5_block === true)
	    {
	        return; //nicht noch ein MD5 Check
	    }
	    $bolLastMail = false;
	    $objLastMail = \Database::getInstance()->prepare("SELECT `last_mail_md5_block` FROM `tl_integrity_timestamps` WHERE `id`=?")
                                               ->executeUncached(4);
	    if ($objLastMail->numRows >0)
	    {
	        $lastMailTime = $objLastMail->last_mail_md5_block;
	        $bolLastMail = true;
	    }
	    $time_block = time() - (24 * 60 * 60); // -24h
	    if ($time_block < $lastMailTime)
	    {
	        return ; //admin email not send
	    }
	    $this->md5_block = true; //nicht noch ein MD5 Check

	    //////////////// MAIL OUT \\\\\\\\\\\\\\\\
	    // Notification
	    list($ADMIN_NAME, $ADMIN_EMAIL) = \StringUtil::splitFriendlyEmail($GLOBALS['TL_CONFIG']['adminEmail']); //from index.php
	    $objEmail = new \Email();
	    $objEmail->from     = $ADMIN_EMAIL;
	    $objEmail->fromName = $ADMIN_NAME;

	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host . $this->Environment->path);
	    $objEmail->text     = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['message_3'], $this->Environment->host . $this->Environment->path);

	    $objEmail->text .= "\n[".date($GLOBALS['TL_CONFIG']['datimFormat'])."]";

	    //Admin eMail or alternative eMail
	    if ($this->check_alternate_email !== false)
	    {
	        $objEmail->sendTo($this->check_alternate_email);
	    }
	    else
	    {
	        $objEmail->sendTo($GLOBALS['TL_CONFIG']['adminEmail']);
	    }

	    if ($bolLastMail)
	    {
	        //update
	        \Database::getInstance()->prepare("UPDATE tl_integrity_timestamps SET tstamp=?,last_mail_md5_block=? WHERE id=?")
	                                ->execute(time(), time(), 4);
	    }
	    else
	    {
	        //insert
	        \Database::getInstance()->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `last_mail_md5_block` )
    	                                       VALUES (?, ?, ?)")
	        	                    ->execute(4, time(), time());
	    }
	    unset($objEmail);
	    return ;
	}//sendCheckEmailMD5Block

	/**
	 * Set check status for file
	 * @param string $cp_file
	 * @param integer $status
	 */
	private function setCheckStatus($cp_file, $status)
	{
	    //0=not tested, true=ok, false=not ok, 3=warning
	    if ($status === true)
	    {
	        $status = 1;
	    }
	    elseif ($status === false)
	    {
	        $status = 2;
	    }


        $arrSet = array
        (
            'pid'                 => $this->check_plan_id,
            'tstamp'              => time(),
            'check_object'        => $cp_file,
            'check_object_status' => $status
        );
	    // Insert Ignore trick over unique key "pid,check_object"
        $objInsert = \Database::getInstance()
                	        ->prepare("INSERT IGNORE INTO `tl_integrity_check_status` %s")
                	        ->set($arrSet)
                	        ->executeUncached();
        if ($objInsert->insertId == 0)
        {
	        // Update
	        \Database::getInstance()
	                    ->prepare("UPDATE
                                        `tl_integrity_check_status`
                                    SET
                                        `check_object_status`=? ,
                                        `tstamp`=?
                                    WHERE
                                        `pid`=?
                                    AND
                                        `check_object`=?"
	                            )
                        ->executeUncached($status, time(), $this->check_plan_id, $cp_file);
        }

	    return ;
	}//setCheckStatus

	/**
	 *
	 * @return mixed    false(bool)=Update not possible, string=latest Contao version
	 */
	protected function checkContaoUpdate()
	{
	    //http://www.inetrobots.com/liveupdate/version.txt
	    //http://www.inetrobots.com/liveupdate/lts-version.txt
	    if ($this->check_update == 0)
	    {
	        if (true === (bool) $this->check_debug)
	        {
	            $this->log($GLOBALS['TL_LANG']['tl_integrity_check']['update_check_deactivated'], 'IntegrityCheck '.__FUNCTION__, TL_CRON);
	        }
	        $this->setCheckStatus('contao_update_check', 0);
	        return false; //test not necessary
	    }
	    //offline
	    if (isset($GLOBALS['TL_CONFIG']['latestVersion']))
	    {
	        $contao_version_live   = explode('.',VERSION . '.' . BUILD);
	        $contao_version_latest = explode('.',$GLOBALS['TL_CONFIG']['latestVersion']);
	        if (true === (bool) $this->check_debug)
	        {
	            $this->log($GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_installed'] .': '.VERSION . '.' . BUILD, 'IntegrityCheck checkContaoUpdate()', TL_CRON);
	            $this->log($GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_latest'] .': '.$GLOBALS['TL_CONFIG']['latestVersion'], 'IntegrityCheck checkContaoUpdate()', TL_CRON);
	        }
	        if ($contao_version_live[0] < $contao_version_latest[0])
	        {
	            $this->setCheckStatus('contao_update_check', true);
	            return false; //major update possible, but not of interest.
	        }
	        if ($contao_version_live[0] > $contao_version_latest[0])
	        {
                $this->setCheckStatus('contao_update_check', true);
                return false; //can not be, not of interest.
	        }
	        //major ist equal, minor check
	        if ($contao_version_live[1] < $contao_version_latest[1])
	        {
	            $this->setCheckStatus('contao_update_check', 3);
	            return $GLOBALS['TL_CONFIG']['latestVersion'];
	        }
	        if ($contao_version_live[1] > $contao_version_latest[1]) // GitHub #75
	        {
	            $this->setCheckStatus('contao_update_check', true);
	            return false; //can not be, not of interest.
	        }
	        //bugfix check
	        if ($contao_version_live[2] < $contao_version_latest[2])
	        {
	            $this->setCheckStatus('contao_update_check', 3);
	            return $GLOBALS['TL_CONFIG']['latestVersion'];
	        }
	        $this->setCheckStatus('contao_update_check', true);
	        return false; //equal
	    }
	    if (true === (bool) $this->check_debug)
	    {
	        $this->log($GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_latest_not_detected'], 'IntegrityCheck '.__FUNCTION__, TL_CRON);
	    }
	    $this->setCheckStatus('contao_update_check', 0);
	    return false; //test not possible
	}

	protected function sendWarningMail($message_number, $note1='', $note2='')
	{
	    $message = '';
	    $text    = '';
	    if (!isset($GLOBALS['TL_CONFIG']['adminEmail']))
	    {
	        return; //admin email not set, needed for sender and recipient
	    }
	    switch ($message_number)
	    {
	        case $this::MESSAGE_CONTAO_UPDATE:
	            $message = 'message_'.$this::MESSAGE_CONTAO_UPDATE;
	            $text = sprintf($GLOBALS['TL_LANG']['tl_integrity_check'][$message]   , $this->Environment->host . $this->Environment->path, $note1, $note2);
	            break;
	        case $this::MESSAGE_INSTALL_COUNT:
	            $message = 'message_'.$this::MESSAGE_INSTALL_COUNT;
	            $text = sprintf($GLOBALS['TL_LANG']['tl_integrity_check'][$message]   , $this->Environment->host . $this->Environment->path);
	            break;
	        default:
	            return ; //wrong
	            break;
	    }

	    list($ADMIN_NAME, $ADMIN_EMAIL) = \StringUtil::splitFriendlyEmail($GLOBALS['TL_CONFIG']['adminEmail']); //from index.php
	    $objEmail = new \Email();
	    $objEmail->from     = $ADMIN_EMAIL;
	    $objEmail->fromName = $ADMIN_NAME;

	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host . $this->Environment->path);
	    $objEmail->text     = $text;
	    $objEmail->text    .= "\n[".date($GLOBALS['TL_CONFIG']['datimFormat'])."]";

	    //Admin eMail or alternative eMail
	    if ($this->check_alternate_email !== false)
	    {
	        $objEmail->sendTo($this->check_alternate_email);
	    }
	    else
	    {
	        $objEmail->sendTo($GLOBALS['TL_CONFIG']['adminEmail']);
	    }

	    if (true === (bool) $this->check_debug)
	    {
	        $this->log('Send warning e-mail', 'IntegrityCheck sendWarningMail()', TL_CRON);
	    }

	    unset($objEmail);
	    return ;
	}

	/**
	 * Set status for blocking warning mail
	 *
	 * @param string $version     Latest Contao Version
	 */
	protected function setWarningMailBlock($version='',$check='')
	{
	    if ($version !='')
	    {
            \Database::getInstance()->prepare("INSERT IGNORE INTO `tl_integrity_warnings`
                                                    ( `tstamp` , `latest_contao_version` )
                                               VALUES (?, ?)"
                                             )
                                    ->execute(time(), $version);
            return true;
	    }

	    if ($check !=='')
	    {
	        //delete old value before insert
	        \Database::getInstance()->prepare("DELETE FROM
                                                    `tl_integrity_warnings`
                                               WHERE
                                                    `latest_contao_version`=?
                                               AND
                                                    `install_count_check`!=?
                                              ")
                        	        ->execute('', '');
	        if (true === (bool) $this->check_debug)
	        {
	            $this->log('Version Check, Delete', 'IntegrityCheck setWarningMailBlock()', TL_CRON);
	        }

	        \Database::getInstance()->prepare("INSERT INTO `tl_integrity_warnings`
                                                    ( `tstamp` , `install_count_check` )
                                               VALUES (?, ?)"
                                             )
                                    ->execute(time(), $check);
	        if (true === (bool) $this->check_debug)
	        {
	            $this->log('Version Check, Insert', 'IntegrityCheck setWarningMailBlock()', TL_CRON);
	        }
	        return true;
	    }
	    if (true === (bool) $this->check_debug)
	    {
	        $this->log("Parameter Error! 1version:{$version} 2check:{$check}", 'IntegrityCheck setWarningMailBlock()', TL_CRON);
	    }
	    return false;
	}

	/**
	 * Get status of blocking warning mail
	 *
	 * @param string $version     Latest Contao Version
	 * @return boolean            true = Email has been sent.
	 */
	protected function getWarningMailBlock($version='',$check='')
	{
	    if ($version !='')
	    {
            $objCheckBlock = \Database::getInstance()->prepare("SELECT
                                                                    `id`
                                                                FROM
                                                                    `tl_integrity_warnings`
                                                                WHERE
                                                                    `latest_contao_version`=?
                                                               ")
                                                    ->execute($version);
    	    if ($objCheckBlock->numRows < 1)
    	    {
    	        if (true === (bool) $this->check_debug)
    	        {
    	            $this->log('Version Check, Blocking: No', 'IntegrityCheck getWarningMailBlock()', TL_CRON);
    	        }
    	        return false; //Blocking: No
    	    }

    	    if (true === (bool) $this->check_debug)
    	    {
    	        $this->log('Version Check, Blocking: Yes', 'IntegrityCheck getWarningMailBlock()', TL_CRON);
    	    }
    	    return true; //Blocking: Yes
	    }

	    if ($check !='')
	    {
	        $objCheckBlock = \Database::getInstance()->prepare("SELECT
                                                                    `id`
                                                                FROM
                                                                    `tl_integrity_warnings`
                                                                WHERE
                                                                    `install_count_check`=?
                                                               ")
                                                     ->execute($check);
	        if ($objCheckBlock->numRows < 1)
	        {
	            if (true === (bool) $this->check_debug)
	            {
	                $this->log('Install Count Check, Blocking: No', 'IntegrityCheck getWarningMailBlock()', TL_CRON);
	            }
	            return false; //Blocking: No
	        }

	        if (true === (bool) $this->check_debug)
	        {
	            $this->log('Install Count Check, Blocking: Yes', 'IntegrityCheck getWarningMailBlock()', TL_CRON);
	        }
	        return true; //Blocking: Yes
	    }

	    return -1;
	}

}//class
