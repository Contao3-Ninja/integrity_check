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

class Generate_Checksum_Files
{
	/**
	 * Generate Checksum File
	 * @param string $file '3.0.0.json'
	 */
	public function Generate_Checksum_File($file)
	{
		$arrCheckHashes = array();
		$file_to = '../config/file_list_' . $file;
		$arrHashes = json_decode(file_get_contents(__DIR__ .'/'. $file));
		foreach ($arrHashes as $hash) 
		{
		    if (count($hash)==2) 
		    {
		        list($path, $md5_file) = $hash;
		    }
		    else 
		    {
		        list($path, $md5_file, $md5_code) = $hash;
		    }
		    if ($path == 'index.php' 
		     || $path == 'system/cron/cron.php'
		     || $path == 'contao/index.php'
		     || $path == 'contao/main.php') 
		    {
		    	//echo $path .' - '. $md5_file .' - '. $md5_code ."\n";
		    	$arrCheckHashes[] = array($path, $md5_file, $md5_code);
		    }
		}
		file_put_contents($file_to, json_encode($arrCheckHashes));
	}

}

$objGenerate = new Generate_Checksum_Files();
$objGenerate->Generate_Checksum_File('3.0.3.json');
