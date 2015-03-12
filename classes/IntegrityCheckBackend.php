<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 *
 * Contao Module "Integrity Check"
 *
 * @copyright  Glen Langer 2012..2015 <http://www.contao.glen-langer.de>
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
 * Class IntegrityCheckBackend 
 * 
 * Backend Integrity check 
 *
 * @copyright  Glen Langer 2012..2015 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @author     Leo Feyer (sourcecode parts from contao check tool)
 * @package    Integrity_Check
 */
class IntegrityCheckBackend extends \Backend
{
    /**
     * Static objects
     * @var array
     */
    protected static $check_plan = array();
    
    /**
     * Static objects
     * @var string
     */
    protected static $file_list = '';
    
    protected static function checkPreparation()
    {
        if (0 == count(static::$check_plan))
        {
            static::$check_plan = static::getCheckPlan();
        }
    }
    
    public static function checkAll()
    {
        $ret = static::checkFiles();
        
        $ret = static::checkContaoUpdate();
        
        $ret = static::checkInstallCount();
    }

    public static function checkSingle($singletest)
    {
        $objSingleTest = \Database::getInstance()
                            ->prepare("SELECT
                                        `check_object`
                                        FROM
                                        `tl_integrity_check_status`
                                        WHERE
                                        `id`=?"
                                )
                            ->execute($singletest);
        if ($objSingleTest->numRows < 1)
        {
            return false;
        }
        switch ($objSingleTest->check_object) 
        {
            case 'contao_update_check':
                static::checkContaoUpdate();
                break;
            case 'install_count_check':
                static::checkInstallCount();
                break;
            default:
                static::checkFile($objSingleTest->check_object);
            break;
        }
        
        return ;
    
    }
    
    
    public static function checkFiles()
    {
        static::checkPreparation();
        static::$file_list = static::getFileList();
        
        if (false === static::$check_plan || 
            false === static::$file_list)
        {
        	return false;
        }
        
        foreach (static::$check_plan['plans'] as $check_plan_step)
        {
            switch ($check_plan_step['cp_type_of_test'])
            {
            	case 'md5' :
            	    static::checkFileMD5($check_plan_step['cp_files']);
            	    break;
            	case 'timestamp' :
            	    static::checkFileTimestamp($check_plan_step['cp_files']);
            	    break;
            }
        }

        if ( count( (array)static::$check_plan['plans_expert'] ) > 0 )
        {
            //Zeilenweise den Expert Plan durchgehen
            foreach (static::$check_plan['plans_expert'] as $check_plan_step)
            {
                switch ($check_plan_step['cp_type_of_test_expert'])
                {
                    case 'md5' :
                        static::checkFileMD5($check_plan_step['cp_files_expert']);
                        break;
                    case 'timestamp' :
                        static::checkFileTimestamp($check_plan_step['cp_files_expert']);
                        break;
                }
            }
        }
        
        
        
        return true;
    }
    
    public static function checkFile($file)
    {
        static::checkPreparation();
        static::$file_list = static::getFileList();
        if ('.htaccess' == $file) 
        {
        	static::checkFileTimestamp($file);
        }
        else 
        {
            //Zeilenweise den Plan durchgehen und die Daten für Datei finden
            foreach (static::$check_plan['plans'] as $check_plan_step)
            {
                if ($file == $check_plan_step['cp_files'])
                {
                    switch ($check_plan_step['cp_type_of_test'])
                    {
                    	case 'md5' :
                    	    static::checkFileMD5($check_plan_step['cp_files']);
                    	    break;
                    	case 'timestamp' :
                    	    static::checkFileTimestamp($check_plan_step['cp_files']);
                    	    break;
                    }
                    return true;
                }
            }
        }
        return true;
    }
    
    public static function checkContaoUpdate()
    {
        //http://www.inetrobots.com/liveupdate/version.txt
        //http://www.inetrobots.com/liveupdate/lts-version.txt
        static::checkPreparation();
        
        //offline
        if (isset($GLOBALS['TL_CONFIG']['latestVersion']))
        {
            $contao_version_live   = explode('.',VERSION . '.' . BUILD);
            $contao_version_latest = explode('.',$GLOBALS['TL_CONFIG']['latestVersion']);
            if (static::$check_plan['debug'] == true)
            {
                \System::loadLanguageFile('tl_integrity_check');
                \System::log($GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_installed'] .': '.VERSION . '.' . BUILD, 'IntegrityCheckBackend checkContaoUpdate()', TL_CRON);
                \System::log($GLOBALS['TL_LANG']['tl_integrity_check']['update_check_contao_latest'] .': '.$GLOBALS['TL_CONFIG']['latestVersion'], 'IntegrityCheckBackend checkContaoUpdate()', TL_CRON);
            }
            if ($contao_version_live[0] < $contao_version_latest[0])
            {
                static::setCheckStatus('contao_update_check', true, static::$check_plan['id']);
                return false; //major update possible, but not of interest.
            }
            if ($contao_version_live[0] > $contao_version_latest[0])
            {
                static::setCheckStatus('contao_update_check', true, static::$check_plan['id']);
                return false; //can not be, not of interest.
            }
            //major ist equal, minor check
            if ($contao_version_live[1] < $contao_version_latest[1])
            {
                static::setCheckStatus('contao_update_check', 3, static::$check_plan['id']);
                return true;
            }
            //bugfix check
            if ($contao_version_live[2] < $contao_version_latest[2])
            {
                static::setCheckStatus('contao_update_check', 3, static::$check_plan['id']);
                return true;
            }
            static::setCheckStatus('contao_update_check', true, static::$check_plan['id']);
            return false; //equal
        }
        static::setCheckStatus('contao_update_check', 0, static::$check_plan['id']);
        return false; //test not possible
    }
    
    public static function checkInstallCount()
    {
        static::checkPreparation();
        
        if ( $GLOBALS['TL_CONFIG']['installCount'] < 3 )
        {
            static::setCheckStatus('install_count_check', true, static::$check_plan['id']);
        }
        else
        {
            static::setCheckStatus('install_count_check', 3, static::$check_plan['id']);
        }
        return true;
    }
    
    protected static function getCheckPlan()
    {
        $check_plan = array();
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
            return false;
        }
        $check_plan['id']              = $objCheckPlan->id;
        $check_plan['debug']           = ($objCheckPlan->check_debug) ? 1 : 0;
        $check_plan['plans']           = deserialize($objCheckPlan->check_plans);
        $check_plan['plans_expert']    = deserialize($objCheckPlan->check_plans_expert);
        $check_plan['title']           = $objCheckPlan->check_title;
        $check_plan['alternate_email'] = ($objCheckPlan->alternate_email) ? $objCheckPlan->alternate_email : false;
        $check_plan['update']          = ($objCheckPlan->update_check) ? 1 : 0;
        $check_plan['install_count']   = ($objCheckPlan->install_count_check) ? 1 : 0;

        return $check_plan;
    }
    
    protected static function setCheckStatus($cp_file, $status, $check_plan_id)
	{
	    //if ($cp_file == '.htaccess') { echo "<html><pre>".$cp_file.'-'.(int)$status.'-'.$check_plan_id."</pre><html>";exit;}
	    //0=not tested, true=ok, false=not ok, 3=warning, 4=file not found
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
            'pid'                 => $check_plan_id,
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
                        ->executeUncached($status, time(), $check_plan_id, $cp_file);
        }   
	    return ;
	}//setCheckStatus
	
	/**
	 * Filelist with checksums
	 * @return    array    file,checksum_file,checksum_code,contao_version
	 */
	protected static function getFileList()
	{
	    $contao_version_live = VERSION . '.' . BUILD;
	    if (file_exists(TL_ROOT . '/system/modules/integrity_check/config/file_list_'.$contao_version_live.'.json'))
	    {
	        //require(TL_ROOT . '/system/modules/integrity_check/config/file_list_'.$contao_version_live.'.php');
	        return json_decode(file_get_contents(TL_ROOT . '/system/modules/integrity_check/config/file_list_'.$contao_version_live.'.json'));
	    }
	    return false;
	}//getFileList

	/**
	 * Check files via MD5
	 *
	 * @param string $cp_file
	 * @return bool	 true = file is corrupt, false = file is not corrupt or no check
	 */
	protected function checkFileMD5($cp_file)
	{
	    if ($cp_file == '')
	    {
	        return false; // kein check
	    }
	    //echo "<html><pre>".print_r(static::$file_list,true)."</pre></html>";exit;
	    foreach (static::$file_list as $files)
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
	        }
	        if ($file == $cp_file)
	        {
	            break; // gefunden
	        }
	    }
	    //echo "<html><pre>".print_r($cp_file,true)."</pre></html>";exit;
	    $status = true;
	    if (is_file(TL_ROOT . '/' . $cp_file))
	    {
	        $buffer = str_replace("\r", '', file_get_contents(TL_ROOT . '/' . $cp_file));
	        $status = true;
	        //Check the content
	        if (strncmp(md5($buffer), $md5_file, 10) !== 0)
	        {
	            //echo "<html><pre> ungleich ".print_r($cp_file,true)."</pre></html>";exit;
	            static::setCheckStatus($cp_file, false, static::$check_plan['id']);
	            return true;
	        }
	        unset($buffer);
	    }
	    else
	    {
	        //echo "<html><pre> nicht prüfbar ".print_r($cp_file,true)."</pre></html>";exit;
	        static::setCheckStatus($cp_file, 4, static::$check_plan['id']); //nicht pruefbar
	        return false;
	    }
	    //echo "<html><pre> gleich ".print_r($cp_file,true)."</pre></html>";exit;
	    static::setCheckStatus($cp_file, true, static::$check_plan['id']);
	    return false;
	}
	
	/**
	 * Check files via timestamp
	 *
	 * @param string $cp_file
	 * @return bool	 true = file is corrupt, false = file is not corrupt or no check
	 */
	protected function checkFileTimestamp($cp_file)
	{
	    $cp_file_ts = 0;
	
	    if ($cp_file == '')
	    {
	        return false; // kein check
	    }
	    \System::loadLanguageFile('tl_integrity_check');
	    
	    //Datei vorhanden? Zeitstempel holen
	    if (is_file(TL_ROOT . '/' . $cp_file))
	    {
	        $objFile = new \File($cp_file);
	        $cp_file_ts = $objFile->mtime;
	        $objFile->close();
	    }
	    else
	    {
	        static::setCheckStatus($cp_file, 4, static::$check_plan['id']);// nicht pruefbar
	        \System::log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['file_not_found'], $cp_file), 'IntegrityCheckBackend checkFileTimestamp()', TL_ERROR);
	        return false; // kein check möglich
	    }
	    
	    //Zeitstempel aus DB holen
	    $objTimestamps = \Database::getInstance()
                            ->prepare("SELECT `check_timestamps` FROM `tl_integrity_timestamps` WHERE `id`=?")
                            ->execute(1);
	    if ($objTimestamps->numRows < 1)
	    {
	        static::setCheckStatus($cp_file, 0, static::$check_plan['id']);// nicht pruefbar
	        \System::log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['timestamp_not_found'], $cp_file), 'IntegrityCheckBackend checkFileTimestamp()', TL_ERROR);
	        return false; // kein check möglich
	    }
	    
	    $arrTimestamps = deserialize($objTimestamps->check_timestamps);
	    
	    if ( !isset($arrTimestamps[$cp_file]) )
	    {
	        static::setCheckStatus($cp_file, 0, static::$check_plan['id']);// nicht pruefbar
	        \System::log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['timestamp_not_found'], $cp_file), 'IntegrityCheckBackend checkFileTimestamp()', TL_ERROR);
	        return false; // kein check möglich
	    }
	    
        //Zeitstempel vergleichen
	    if ( $cp_file_ts != $arrTimestamps[$cp_file])
	    {
	        static::setCheckStatus($cp_file, false, static::$check_plan['id']);
	        return true;
	    }
	    
	    static::setCheckStatus($cp_file, true, static::$check_plan['id']);
	    return false;
	}
}
