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
 * @copyright  Glen Langer 2012..2014
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
 * @copyright  Glen Langer 2012..2014
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
                if (!in_array($val, $this->Config->getActiveModules()))
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
    
} // class
