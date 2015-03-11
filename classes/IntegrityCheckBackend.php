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
    
    protected static function checkPreparation()
    {
        \System::log('Start '.__FUNCTION__, __FUNCTION__, TL_CRON);
        if (0 == count(static::$check_plan))
        {
            static::$check_plan = static::getCheckPlan();
        }
    }
    public static function checkAll()
    {
        \System::log('Start '.__FUNCTION__, __FUNCTION__, TL_CRON);
        
        $ret = static::checkFiles();
        \System::log('checkFiles Status '.(int)$ret, __FUNCTION__, TL_CRON);
        
        $ret = static::checkContaoUpdate();
        \System::log('checkContaoUpdate Status '.(int)$ret, __FUNCTION__, TL_CRON);
        
        $ret = static::checkInstallCount();
        \System::log('checkInstallCount Status '.(int)$ret, __FUNCTION__, TL_CRON);
    }

    public static function checkFiles()
    {
        \System::log('Start '.__FUNCTION__, __FUNCTION__, TL_CRON);
        static::checkPreparation();

        return true;
    }
    
    public static function checkFile($file)
    {
        \System::log('Start '.__FUNCTION__, __FUNCTION__, TL_CRON);
        static::checkPreparation();
        
        return true;
    }
    
    public static function checkContaoUpdate($id = 0)
    {
        //http://www.inetrobots.com/liveupdate/version.txt
        //http://www.inetrobots.com/liveupdate/lts-version.txt
        \System::log('Start '.__FUNCTION__, __FUNCTION__, TL_CRON);
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
        \System::log('Start '.__FUNCTION__, __FUNCTION__, TL_CRON);
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
        \System::log('Start '.__FUNCTION__, __FUNCTION__, TL_CRON);
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
	    \System::log('Start '.__FUNCTION__.'-'.$cp_file.'-'.(int)$status, __FUNCTION__, TL_CRON);
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

}
