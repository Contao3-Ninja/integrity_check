<?php

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
 * Run in a custom namespace, so the class can be replaced
 */
namespace BugBuster\IntegrityCheck; 

/**
 * Class Integrity_Check 
 * 
 * Cronjob for integrity check 
 *
 * @copyright  Glen Langer 2012 
 * @author     BugBuster 
 * @author     Leo Feyer (sourcecode parts from contao check tool)
 * @package    Integrity_Check
 */
class Integrity_Check extends \Frontend 
{
    protected $fileEmailStatus = array();
    protected $fileLogStatus = array();
    
    protected $check_debug = false;
    protected $check_plans = array();
    protected $check_title = '';
    protected $last_mail   = array();
    
    protected $cron_interval = '';
    
    /**
     * Filelist with checksums
     * @var array    file,checksum_file,checksum_code,contao_version
     */
    protected $file_list   = array();
	
    /**
     *  CRON Minutely Call
     */
    public function checkFilesMinutely()
    {
    	$this->cron_interval = 'minutely';
    	//$this->log('Start: '.$this->cron_interval, 'Integrity_Check checkFilesMinutely()', TL_CRON);
    	$this->checkFiles();
    }
    
    /**
     *  CRON Hourly Call
     */
    public function checkFilesHourly()
    {
        $this->cron_interval = 'hourly';
        //$this->log('Start: '.$this->cron_interval, 'Integrity_Check checkFilesHourly()', TL_CRON);
        $this->checkFiles();
    }
    /**
     *  CRON Daily Call
     */
    public function checkFilesDaily()
    {
        $this->cron_interval = 'daily';
        //$this->log('Start: '.$this->cron_interval, 'Integrity_Check checkFilesDaily()', TL_CRON);
        $this->checkFiles();
    }
    /**
     *  CRON Weekly Call
     */
    public function checkFilesWeekly()
    {
        $this->cron_interval = 'weekly';
        //$this->log('Start: '.$this->cron_interval, 'Integrity_Check checkFilesWeekly()', TL_CRON);
        $this->checkFiles();
    }
    /**
     *  CRON Monthly Call
     */
    public function checkFilesMonthly()
    {
        $this->cron_interval = 'monthly';
        //$this->log('Start: '.$this->cron_interval, 'Integrity_Check checkFilesMonthly()', TL_CRON);
        $this->checkFiles();
    }
    
    
	/**
	 * Check files for integrity
	 */
	protected function checkFiles()
	{
	    $this->loadLanguageFile('tl_integrity_check');
	    $this->getCheckPlan();
	    $this->getFileList();
	    $checkSummary = false; //false=kein check erfolgt, keine Mail, keine completed Meldung

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
	            $checkSummary = ($resMD5 == false || $resTS == false) ? true : $checkSummary;
	        } //moment
	    } //foreach plan step
	    
	    //Log / Mail wenn notwendig
	    if ($checkSummary) 
	    {
	    	$this->sendCheckLog();
            $this->sendCheckEmail();
            if ($this->check_debug == true)
            {
                // Add log entry
                $this->log('['.$this->check_title .'] '. $GLOBALS['TL_LANG']['tl_integrity_check']['finished'], 'Integrity_Check checkFiles()', TL_CRON);
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
	    if ($cp_file == '') 
	    {
	        return false; // kein check
	    }
	    $status = true;

	    foreach ($this->file_list as $files)
	    {
	        list($file, $md5_file, $md5_code, $contao_version) = $files;
	        if ($file == $cp_file) 
	        {
	            break; // gefunden
	        }
	    }
	    
        if (is_file(TL_ROOT . '/' . $cp_file)) 
        {
            $buffer  = str_replace("\r", '', file_get_contents(TL_ROOT . '/' . $cp_file));
            // Check the content
            if (md5($buffer) != $md5_file) 
            {
                // Check the content without comments
                if (md5(preg_replace('@/\*.*\*/@Us', '', $buffer)) != $md5_code) 
                {
                    $status = false;
                }
                else 
                {
                    $status = true;
                }
            }
            //DEV
            //$this->log('Summen '.$cp_file.':'.md5($buffer).'-'.md5(preg_replace('@/\*.*\*/@Us', '', $buffer)), 'Integrity_Check MD5()', TL_ERROR);
            unset($buffer);
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
                    //$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'Integrity_Check checkFileMD5()', TL_ERROR);
                    break;
                case 'only_logging':
                	$this->fileLogStatus[$cp_file] = 'md5'; // true = log
                    //$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'Integrity_Check checkFileMD5()', TL_ERROR);
                    break;
            }
        }
        elseif ($this->check_debug == true)
        {
            $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'Integrity_Check checkFileMD5()', TL_CRON);
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
	    $objTimestamps = $this->Database->prepare("SELECT `check_timestamps` FROM `tl_integrity_timestamps` WHERE `id`=?")
	                                    ->execute(1);
	    if ($objTimestamps->numRows < 1)
	    {
	        return false; // kein check möglich
	    }
	    $status = true;
	    $arrTimestamps = deserialize($objTimestamps->check_timestamps);
	    if (is_file(TL_ROOT . '/' . $cp_file))
	    {
	        $objFile = new \File($cp_file);
	        $cp_file_ts = $objFile->mtime;
	        $objFile->close();
	    }	    
	    
	    //Ergebniss verarbeiten
	    if ($cp_file_ts != $arrTimestamps[$cp_file])
	    {
	    	$status = false;
	        //File corrupt
	        switch ($cp_action)
	        {
	            case 'admin_email' :
	                $this->fileEmailStatus[$cp_file] = true; // true = mail
	                //wenn mail dann auch log
	                $this->fileLogStatus[$cp_file] = 'timestamp'; // true = log
	                //$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'Integrity_Check checkFileTimestamp()', TL_ERROR);
	                break;
	            case 'only_logging':
	            	$this->fileLogStatus[$cp_file] = 'timestamp'; // true = log
	                //$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'Integrity_Check checkFileTimestamp()', TL_ERROR);
	                break;
	        }
	    }
	    elseif ($this->check_debug == true)
	    {
	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'Integrity_Check checkFileTimestamp()', TL_CRON);
	    }
	    
	    return $status;
	}
	
	private function getCheckPlan()
	{
	    $objCheckPlan = $this->Database->prepare("SELECT `check_debug`, `check_plans`, `check_title` 
                                                  FROM `tl_integrity_check` WHERE published=?")
	                                   ->execute(1);
	    if ($objCheckPlan->numRows < 1) 
	    {
	        return ;
	    }
	    $this->check_debug = ($objCheckPlan->check_debug) ? 1 : 0;
	    $this->check_plans = deserialize($objCheckPlan->check_plans);
	    $this->check_title = $objCheckPlan->check_title;
	    return ;
	}
	
	/**
	 * Filelist with checksums
	 * @return    array    file,checksum_file,checksum_code,contao_version
	 */
	private function getFileList() 
	{
	    $contao_version_live = VERSION . '.' . BUILD;
	    $files2check = ''; // overwrite in file_list_...php
	    if (file_exists(TL_ROOT . '/system/modules/integrity_check/config/file_list_'.$contao_version_live.'.php')) 
	    {
	        require(TL_ROOT . '/system/modules/integrity_check/config/file_list_'.$contao_version_live.'.php');
	        $this->file_list = $files2check;
	    }
	    return;
	}//getFileList
	
	/**
	 * Send eMail to Admin
	 */
	private function sendCheckEmail()
	{
	    if (!isset($GLOBALS['TL_CONFIG']['adminEmail'])) 
	    {
	        return; //admin email not set
	    }
	    $bolLastMail = false;	    
	    $arrFiles = array('index.php'=>0,'cron.php'=>0,'contao/index.php'=> 0,'contao/main.php'=> 0);
	    $objLastMail = $this->Database->prepare("SELECT `last_mail_tstamps` FROM `tl_integrity_timestamps` WHERE `id`=?")
	                                  ->execute(2);
	    if ($objLastMail->numRows >0) 
	    {
	        $arrFiles = array_merge($arrFiles, deserialize($objLastMail->last_mail_tstamps));
	        $bolLastMail = true;
	    }
	    $time_block = time() - (24 * 60 * 60); // -24h
	    
	    //////////////// MAIL OUT \\\\\\\\\\\\\\\\
	    $sendmail = false;
	    // Notification
	    list($GLOBALS['TL_ADMIN_NAME'], $GLOBALS['TL_ADMIN_EMAIL']) = $this->splitFriendlyName($GLOBALS['TL_CONFIG']['adminEmail']); //from index.php
	    $objEmail = new \Email();
	    $objEmail->from     = $GLOBALS['TL_ADMIN_EMAIL'];
	    $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
	    
	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host);
	    $objEmail->text     = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['message_1'], $this->Environment->host);
	    
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
    	            if ($this->check_debug == true)
                    {
                        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['mail_blocked'], $key), 'Integrity_Check sendCheckEmail()', TL_CRON);
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
    	    $objEmail->sendTo($GLOBALS['TL_CONFIG']['adminEmail']);

    	    if ($bolLastMail) 
    	    {
    	        //update
    	        $this->Database->prepare("UPDATE tl_integrity_timestamps SET tstamp=?,last_mail_tstamps=? WHERE id=?")
    	                       ->execute(time(),serialize($arrFiles),2);
    	    }
    	    else 
    	    {
    	        //insert
    	        $this->Database->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `last_mail_tstamps` )
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
	    $arrFiles = array('index.php'=>0,'cron.php'=>0,'contao/index.php'=> 0,'contao/main.php'=> 0);
	    $objLastLog = $this->Database->prepare("SELECT `last_minutely_log` FROM `tl_integrity_timestamps` WHERE `id`=?")
	    							 ->execute(3);
	    if ($objLastLog->numRows >0)
	    {
	        $arrFiles = array_merge($arrFiles, deserialize($objLastLog->last_minutely_log));
	        $bolLastLog = true;
	    }
	    $time_block = time() - (60 * 60); // -1h
	    $sendlog = false;
	    
	    foreach ($this->fileLogStatus as $key => $value) // file => kind of test
	    {
	        if ($value == true) // md5 || timestamp
	        {
	        	$sendLog_temp = true;
	        	
	        	//nur wenn das letzte Log 1h her ist für diese Datei
	        	if ($arrFiles[$key] > $time_block)
	        	{
	        	    $sendLog_temp = false;
	        	    if ($this->check_debug == true)
	        	    {
	        	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['log_blocked'], $key), 'Integrity_Check sendCheckLog()', TL_CRON);
	        	    }
	        	}
	        	
	        	//log?
	        	if ($sendLog_temp)
	        	{
	        		if ($value == 'md5') 
	        		{
	        			$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $key) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'Integrity_Check checkFileMD5()', TL_ERROR);
	        		}
	        		else 
	        		{
	        			$this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $key) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'Integrity_Check checkFileTimestamp()', TL_ERROR);
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
	            $this->Database->prepare("UPDATE tl_integrity_timestamps SET tstamp=?,last_minutely_log=? WHERE id=?")
	            			   ->execute(time(),serialize($arrFiles),3);
	        }
	        else
	        {
	            //insert
	            $this->Database->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `last_minutely_log` )
	                   					  VALUES (?, ?, ?)")
	                           ->execute(3, time(), serialize($arrFiles));
	        }
	    }// if sendlog
	}// sendCheckLog()

}

