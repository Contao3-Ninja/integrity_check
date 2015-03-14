<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2014 Leo Feyer
 *
 * Contao Module "Integrity Check"
 *
 * @copyright  Glen Langer 2012..2014 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check 
 * @license    LGPL 
 * @filesource
 * @see	       https://github.com/BugBuster1701/integrity_check
 */

/**
 * Class IntegrityCheckRunonce
 *
 * Runonce for integrity check
 *
 * @copyright  Glen Langer 2012..2014 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
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
                                      `last_mail_md5_block` int(10) unsigned NOT NULL default '0',
                                      `latest_contao_version` varchar(12) NOT NULL DEFAULT ''
                                      PRIMARY KEY (`id`)
                                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
            //Timestamps füllen
	    	$arrFiles = array
	    	(
	    	        'index.php',
	    	        'system/cron/cron.php',
	    	        'contao/index.php',
	    	        'contao/main.php',
	    	        '.htaccess'
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
	    	$this->Database->prepare("INSERT INTO `tl_integrity_timestamps` ( `id` , `tstamp` , `check_timestamps` )
                                       VALUES (?, ?, ?)")
                            ->execute(1, time(), serialize($arrTimestamps));
		} // if !tableExists('tl_integrity_timestamps')
		
		//From Integrity Check 3.1
		if (is_file(TL_ROOT . '/system/modules/integrity_check/config/database.sql'))
		{
		    $objFile = new File('system/modules/integrity_check/config/database.sql');
		    $objFile->delete();
		    $objFile->close();
		    $objFile=null;
		    unset($objFile);
		}
	}
}

$objIntegrityCheckRunonce = new IntegrityCheckRunonce();
$objIntegrityCheckRunonce->run();
