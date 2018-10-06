<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * Modul Integrity Check - Helper 
 * 
 * PHP version 5
 * @copyright  Glen Langer 2012..2016
 * @author     Glen Langer
 * @package    Integrity_Check
 * @license    LGPL
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace BugBuster\IntegrityCheck; 

/**
 * Class IntegrityCheckHelper
 *
 * @copyright  Glen Langer 2012..2016
 * @author     Glen Langer
 * @package    Integrity_Check
 */
class IntegrityCheckHelper extends \System
{
   /**
    * Current object instance
    * @var object
    */
    protected static $instance = null;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
    protected function compile()
    {
        
    }
    /**
     * Return the current object instance (Singleton)
     * @return BotStatisticsHelper
     */
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new IntegrityCheckHelper();
        }
    
        return self::$instance;
    }

    /**
     * Hook: Check the required extensions and files for Integrity_Check
     *
     * @param string $strContent
     * @param string $strTemplate
     * @return string
     */
    public function checkExtensions($strContent, $strTemplate)
    {
        if ($strTemplate == 'be_main')
        {
            if (!is_array($_SESSION["TL_INFO"]))
            {
                $_SESSION["TL_INFO"] = array();
            }
    
            // required extensions
            $arrRequiredExtensions = array(
                'MultiColumnWizard' => 'multicolumnwizard'
            );
            
            // check for required extensions
            foreach ($arrRequiredExtensions as $key => $val)
            {
                if (!in_array($val, \ModuleLoader::getActive()))
                {
                    $_SESSION["TL_INFO"] = array_merge($_SESSION["TL_INFO"], array($val => 'Please install the required extension <strong>' . $key . '</strong>'));
                }
                else
                {
                    if (is_array($_SESSION["TL_INFO"]) && key_exists($val, $_SESSION["TL_INFO"]))
                    {
                        unset($_SESSION["TL_INFO"][$val]);
                    }
                }
            }
        }
    
        return $strContent;
    } // checkExtension
    
    /**
    * Filelist with checksums
    * @return    array    file,checksum_file,checksum_code,contao_version
    */
    public function getFileList() 
	{
	    $file_list = array();
	    $contao_version_live = VERSION . '.' . BUILD;
	    if (file_exists(TL_ROOT . '/system/modules/integrity_check/config/file_list_'.$contao_version_live.'.json')) 
	    {
	        $file_list = json_decode(file_get_contents(TL_ROOT . '/system/modules/integrity_check/config/file_list_'.$contao_version_live.'.json'));
	    }
	    return $file_list;
	}//getFileList
    
} // class
