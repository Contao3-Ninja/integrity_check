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
    
    protected $check_debug = false;
    protected $check_plans = array();
    protected $check_title = '';
    
    protected $cron_moment = '';
    
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
    	$this->cron_moment = 'minutely';
    	$this->checkFiles();
    }
    
    /**
     *  CRON Hourly Call
     */
    public function checkFilesHourly()
    {
        $this->cron_moment = 'hourly';
        $this->checkFiles();
    }
    /**
     *  CRON Daily Call
     */
    public function checkFilesDaily()
    {
        $this->cron_moment = 'daily';
        $this->checkFiles();
    }
    /**
     *  CRON Weekly Call
     */
    public function checkFilesWeekly()
    {
        $this->cron_moment = 'weekly';
        $this->checkFiles();
    }
    /**
     *  CRON Monthly Call
     */
    public function checkFilesMonthly()
    {
        $this->cron_moment = 'monthly';
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
	        if ($this->cron_moment == $check_plan_step['cp_moment']) 
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
	            //einmal true immer true
	            $checkSummary = ($resMD5 == true || $resTS == true) ? true : $checkSummary;
	        } //moment
	    } //foreach plan step
	    if ($checkSummary) 
	    {
            $this->sendCheckEmail();
            // Add log entry
            $this->log('['.$this->check_title .'] '. $GLOBALS['TL_LANG']['tl_integrity_check']['finished'], 'Integrity_Check checkFiles()', TL_CRON);
	    }
	}
	
	
	 
	/**
	 * Check file, use MD5 
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
                    if ($this->check_debug == true) 
                    {
                        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file), 'Integrity_Check checkFileMD5()', TL_ERROR);
                    }
                    break;
                case 'only_logging':
                    $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file), 'Integrity_Check checkFileMD5()', TL_ERROR);
                    break;
            }
        }
        elseif ($this->check_debug == true)
        {
            $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $cp_file), 'Integrity_Check checkFileMD5()', TL_CRON);
        }
        return true;
	}
	
	protected function checkFileTimestamp($cp_file, $cp_action)
	{
	    if ($cp_file == '')
	    {
	        return false; // kein check
	    }
	    $this->log('Timestamp-Check not yet implemented.', 'Integrity_Check checkFileTimestamp()', TL_CRON);
	    return false; // kein check
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
	    $sendmail = false;
	    // Notification
	    list($GLOBALS['TL_ADMIN_NAME'], $GLOBALS['TL_ADMIN_EMAIL']) = $this->splitFriendlyName($GLOBALS['TL_CONFIG']['adminEmail']); //from index.php
	    $objEmail = new \Email();
	    $objEmail->from     = $GLOBALS['TL_ADMIN_EMAIL'];
	    $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
	    
	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host);
	    $objEmail->text     = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['message_1'], $this->Environment->host);
	    
	    foreach ($this->fileEmailStatus as $key => $value)
	    {
	        if ($value === true)
	        {
	            $objEmail->text .= "\n* ".$key;
	            $sendmail = true;
	        }
	    }
	    if ($sendmail) 
	    {
    	    $objEmail->text .= "\n\n".$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'];
    	    $objEmail->sendTo($GLOBALS['TL_CONFIG']['adminEmail']);
	    }
	    else
	    {
	        unset($objEmail);
	    }
	    return ;
	    
	}//sendCheckEmail
	
	
}

