<?php   
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * Modul Integrity Check 
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    Integrity_Check 
 * @license    LGPL 
 */

/**
 * Class IntegrityCheckRunonce
 *
 * Runonce for integrity check
 *
 * @copyright  Glen Langer 2012
 * @author     BugBuster
 * @package    Integrity_Check
 */
class IntegrityCheckRunonce extends Controller
{
	public function __construct()
	{
	    parent::__construct();
	    $this->import('Database');
	}
	public function run()
	{
		if (!$this->Database->tableExists('tl_integrity_timestamps'))
		{
		    
	    	//Tabelle anlegen, da vor DB Update bei Installation
	    	$this->Database->execute("CREATE TABLE IF NOT EXISTS `tl_integrity_timestamps` (
                                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                      `tstamp` int(10) unsigned NOT NULL DEFAULT '0',
                                      `check_timestamps` varchar(255) NOT NULL DEFAULT '',
                                      `last_mail_tstamps` varchar(255) NOT NULL DEFAULT '',
                                      `last_minutely_log` varchar(255) NOT NULL DEFAULT '',
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
            //Timestamps füllen
	    	$arrFiles = array
	    	(
	    	        'index.php',
	    	        'system/cron/cron.php',
	    	        'contao/index.php',
	    	        'contao/main.php'
	    	);
	    	$arrTimestamps = array();
	    	foreach ($arrFiles as $arrFile)
	    	{
	    	    if (is_file(TL_ROOT . '/' . $arrFile))
	    	    {
	    	        $objFile = new File($arrFile);
	    	        $arrTimestamps[$arrFile] = $objFile->mtime;
	    	        $objFile->close();
	    	    }
	    	}
	    	// Insert
	    	$objInsert = $this->Database->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `check_timestamps` )
	    	                                       VALUES (?, ?, ?)")
	    	                            ->execute(1, time(), serialize($arrTimestamps));
		} // if !tableExists('tl_integrity_timestamps')
	}
}

$objIntegrityCheckRunonce = new IntegrityCheckRunonce();
$objIntegrityCheckRunonce->run();

?>