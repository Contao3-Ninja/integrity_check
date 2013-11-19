<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Contao Module "Integrity Check"
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check 
 * @license    LGPL 
 * @filesource
 * @see	       https://github.com/BugBuster1701/integrity_check
 */



/**
 * Class Integrity_Check 
 * 
 * Cronjob for integrity check 
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @author     Leo Feyer (sourcecode parts from contao check tool)
 * @package    Integrity_Check
 */
class Integrity_Check extends Frontend 
{
    protected $fileEmailStatus = array();
    
    protected $check_debug = false;
    protected $check_plans = array();
    protected $check_title = '';
    protected $last_mail   = array();
    protected $md5_block   = false;
    
    protected $cron_interval = '';
    
    const latest_version = '2.11.13';
    
    /**
     * Filelist with checksums
     * @var array    file,checksum_file,checksum_code,contao_version
     */
    protected $file_list   = array();

    
    /**
     *  CRON Hourly Call
     */
    public function checkFilesHourly()
    {
        $this->cron_interval = 'hourly';
        $this->checkFiles();
    }
    /**
     *  CRON Daily Call
     */
    public function checkFilesDaily()
    {
        $this->cron_interval = 'daily';
        $this->checkFiles();
    }
    /**
     *  CRON Weekly Call
     */
    public function checkFilesWeekly()
    {
        $this->cron_interval = 'weekly';
        $this->checkFiles();
    }
    /**
     *  CRON Monthly Call
     */
    public function checkFilesMonthly()
    {
        $this->cron_interval = 'monthly';
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
	            //einmal true immer true
	            $checkSummary = ($resMD5 == true || $resTS == true) ? true : $checkSummary;
	        } //moment
	    } //foreach plan step
	    if ($checkSummary) 
	    {
            $this->sendCheckEmail();
            if ($this->check_debug == true)
            {
                // Add log entry
                $this->log('['.$this->check_title .'] '. $GLOBALS['TL_LANG']['tl_integrity_check']['finished'], 'Integrity_Check checkFiles()', TL_CRON);
            }
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
	    
	    if ( version_compare(VERSION.'.'.BUILD, $this::latest_version, '>') )
	    {
	        if ($this->check_debug == true)
	        {
	            // Add log entry
	            $this->log('['.$this->check_title .'] '. sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['md5_blocked'], $cp_file), 'Integrity_Check checkFiles()', TL_ERROR);
	        }
	        // Mail to Admin
	        $this->sendCheckEmailMD5Block();
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
            //$this->log('MD5F-MD5C-'.$cp_file.' '.md5($buffer).'-'.md5(preg_replace('@/\*.*\*/@Us', '', $buffer)), 'Integrity_Check checkFileMD5()', REPOSITORY);
            
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
                    $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'Integrity_Check checkFileMD5()', TL_ERROR);
                    break;
                case 'only_logging':
                    $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'Integrity_Check checkFileMD5()', TL_ERROR);
                    break;
            }
        }
        elseif ($this->check_debug == true)
        {
            $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['md5'].']', 'Integrity_Check checkFileMD5()', TL_CRON);
        }
        return true;
	}
	
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
	    
	    $arrTimestamps = deserialize($objTimestamps->check_timestamps);
	    if (is_file(TL_ROOT . '/' . $cp_file))
	    {
	        $objFile = new File($cp_file);
	        $cp_file_ts = $objFile->mtime;
	        $objFile->close();
	    }	    
	    
	    //Ergebniss verarbeiten
	    if ($cp_file_ts != $arrTimestamps[$cp_file])
	    {
	        //File corrupt
	        switch ($cp_action)
	        {
	            case 'admin_email' :
	                $this->fileEmailStatus[$cp_file] = true; // true = mail
	                //wenn mail dann auch log
	                $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'Integrity_Check checkFileTimestamp()', TL_ERROR);
	                break;
	            case 'only_logging':
	                $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'Integrity_Check checkFileTimestamp()', TL_ERROR);
	                break;
	        }
	    }
	    elseif ($this->check_debug == true)
	    {
	        $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $cp_file) . ' ['.$GLOBALS['TL_LANG']['tl_integrity_check']['timestamp'].']', 'Integrity_Check checkFileTimestamp()', TL_CRON);
	    }
	    
	    return true;
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
	    $objEmail = new Email();
	    $objEmail->from     = $GLOBALS['TL_ADMIN_EMAIL'];
	    $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
	    
	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host . TL_PATH);
	    $objEmail->text     = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['message_1'], $this->Environment->host . TL_PATH);
	    
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
	    else
	    {
	        unset($objEmail);
	    }
	    return ;
	    
	}//sendCheckEmail
	
	/**
	 * Send eMail to Admin, an update is necessary
	 */
	private function sendCheckEmailMD5Block()
	{
	    if (!isset($GLOBALS['TL_CONFIG']['adminEmail']))
	    {
	        return; //admin email not set
	    }
	    if ($this->md5_block === true)
	    {
	        return; //nicht noch ein MD5 Check
	    }
	    $bolLastMail = false;
	    $objLastMail = $this->Database->prepare("SELECT `last_mail_md5_block` FROM `tl_integrity_timestamps` WHERE `id`=?")
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
	    $sendmail = false;
	    // Notification
	    list($GLOBALS['TL_ADMIN_NAME'], $GLOBALS['TL_ADMIN_EMAIL']) = $this->splitFriendlyName($GLOBALS['TL_CONFIG']['adminEmail']); //from index.php
	    $objEmail = new Email();
	    $objEmail->from     = $GLOBALS['TL_ADMIN_EMAIL'];
	    $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
	
	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host . TL_PATH);
	    $objEmail->text     = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['message_3'], $this->Environment->host . TL_PATH);
	
	    $objEmail->text .= "\n[".date($GLOBALS['TL_CONFIG']['datimFormat'])."]";
	    $objEmail->sendTo($GLOBALS['TL_CONFIG']['adminEmail']);
	     
	    if ($bolLastMail)
	    {
	        //update
	        $this->Database->prepare("UPDATE tl_integrity_timestamps SET tstamp=?,last_mail_md5_block=? WHERE id=?")
	                       ->execute(time(), time(), 4);
	    }
	    else
	    {
	        //insert
	        $this->Database->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `last_mail_md5_block` )
    	                              VALUES (?, ?, ?)")
	    	               ->execute(4, time(), time());
	    }
	    unset($objEmail);
	    return ;
	}//sendCheckEmailMD5Block
}
?>