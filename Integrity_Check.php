<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * Class Integrity_Check 
 * 
 * Cronjob for integrity check 
 *
 * @copyright  Glen Langer 2012 
 * @author     BugBuster 
 * @author     Leo Feyer (sourcecode parts from contao check tool)
 * @package    Integrity_Check
 */
class Integrity_Check extends Frontend 
{
    protected $fileStatus = array();
    
	/**
	 * Check files for integrity
	 */
	public function checkFiles()
	{
	    $contao_version_live = VERSION . '.' . BUILD;
	    $this->loadLanguageFile('tl_integrity_check');
	    $status = true;
	    
	    foreach ($this->getFileList() as $file) 
	    {
	        list($file, $md5_file, $md5_code, $contao_version) = $file;
	        if ($contao_version_live != $contao_version) 
	        {
	            continue;
	        }
	    
	        if (is_file(TL_ROOT . '/' . $file)) 
	        {
	            $buffer  = str_replace("\r", '', file_get_contents(TL_ROOT . '/' . $file));
	            // Check the content
	            if (md5($buffer) != $md5_file) 
	            {
	                // Check the content without comments
	                if (md5(preg_replace('@/\*.*\*/@Us', '', $buffer)) != $md5_code) 
	                {
	                    $this->fileStatus[] = array($file,false);
	                    
	                }
	                else 
	                {
	                    $this->fileStatus[] = array($file,true);
	                }
	            }
	            else
	            {
	                $this->fileStatus[] = array($file,true);
	            }
	            unset($buffer);
	        }
	    }
	    foreach ($this->fileStatus as $key => $value)
	    {
	        if ($value[1] === false) 
	        {
	            $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['corrupt'], $value[0]), 'Integrity_Check checkFiles()', TL_ERROR);
	            $status = false;
	        }
	        elseif ($GLOBALS['TL_CONFIG']['mod_integrity_check']['debug'] === true) 
	        { 
	            $this->log(sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['ok'], $value[0]), 'Integrity_Check checkFiles()', TL_CRON);
	        }
	    }
	    
	    //Mail to Admin
	    if ($status === false) 
	    {
	        $this->sendCheckEmail();
	    }

	    // Add log entry
    	$this->log($GLOBALS['TL_LANG']['tl_integrity_check']['finished'], 'Integrity_Check checkFiles()', TL_CRON);
		
	}
	
	/**
	 * Filelist with checksums
	 * @return    array    file,checksum_file,checksum_code,contao_version
	 */
	private function getFileList() 
	{
	    return array (
	            /*2.11.5.1*/
	                    array('index.php', '1b223120dfb3083a36c6e36f1eb276f6', 'f5a8ccfabeb8b26d62032cfacb3e005a','2.11.5'),
                        array('contao/index.php', 'ccf20e724fd76a58676b85cd549bf228', '9d74ad1a5a129df17268b2a45494cb1f','2.11.5'),
                        array('contao/main.php', '3fd192985d0454f61a43271b5c1c643e', '14ae4550377d73ce1251a4a9a0a8d7a7','2.11.5'),
	            /*2.11.4.0*/
	                    array('index.php', '1b223120dfb3083a36c6e36f1eb276f6', 'f5a8ccfabeb8b26d62032cfacb3e005a','2.11.4'),
	                    array('contao/index.php', 'ccf20e724fd76a58676b85cd549bf228', '9d74ad1a5a129df17268b2a45494cb1f','2.11.4'),
	                    array('contao/main.php', '3fd192985d0454f61a43271b5c1c643e', '14ae4550377d73ce1251a4a9a0a8d7a7','2.11.4'),
	            /*2.11.3.0*/	            
        	            array('index.php', '1b223120dfb3083a36c6e36f1eb276f6', 'f5a8ccfabeb8b26d62032cfacb3e005a','2.11.3'),
        	            array('contao/index.php', 'ccf20e724fd76a58676b85cd549bf228', '9d74ad1a5a129df17268b2a45494cb1f','2.11.3'),
        	            array('contao/main.php', '3fd192985d0454f61a43271b5c1c643e', '14ae4550377d73ce1251a4a9a0a8d7a7','2.11.3'),
	            /*2.11.2.1*/
	                    array('index.php', '1b223120dfb3083a36c6e36f1eb276f6', 'f5a8ccfabeb8b26d62032cfacb3e005a','2.11.2'),
	                    array('contao/index.php', 'ccf20e724fd76a58676b85cd549bf228', '9d74ad1a5a129df17268b2a45494cb1f','2.11.2'),
	                    array('contao/main.php', '3fd192985d0454f61a43271b5c1c643e', '14ae4550377d73ce1251a4a9a0a8d7a7','2.11.2'),
	            /*2.11.1.1*/
	                    array('index.php', '1b223120dfb3083a36c6e36f1eb276f6', 'f5a8ccfabeb8b26d62032cfacb3e005a','2.11.1'),
	                    array('contao/index.php', 'ccf20e724fd76a58676b85cd549bf228', '9d74ad1a5a129df17268b2a45494cb1f','2.11.1'),
	                    array('contao/main.php', '3fd192985d0454f61a43271b5c1c643e', '14ae4550377d73ce1251a4a9a0a8d7a7','2.11.1'),
	            /*2.11.0.0*/
	                    array('index.php', '1b223120dfb3083a36c6e36f1eb276f6', 'f5a8ccfabeb8b26d62032cfacb3e005a','2.11.0'),
	                    array('contao/index.php', 'ccf20e724fd76a58676b85cd549bf228', '9d74ad1a5a129df17268b2a45494cb1f','2.11.0'),
	                    array('contao/main.php', '3fd192985d0454f61a43271b5c1c643e', '14ae4550377d73ce1251a4a9a0a8d7a7','2.11.0'),
	            /*2.10.4.0*/
	                    array('index.php', 'e1239574cdcbe65d7d27bb6a8212dca3', '946faca1c0462bc33f3601966a597ecc','2.10.4'),
	                    array('contao/index.php', '27cb36a9489e3c937388b2ad1b523b8c', 'c074c5a659abcce83662304546282f15','2.10.4'),
	                    array('contao/main.php', '1b09cf86e2660d5bdc43738374dd7d36', '277816b2a1df8f925dc7e21708dbe484','2.10.4'),
	            /*2.10.3.0*/
	                    array('index.php', 'e1239574cdcbe65d7d27bb6a8212dca3', '946faca1c0462bc33f3601966a597ecc','2.10.3'),
	                    array('contao/index.php', '27cb36a9489e3c937388b2ad1b523b8c', 'c074c5a659abcce83662304546282f15','2.10.3'),
	                    array('contao/main.php', '1b09cf86e2660d5bdc43738374dd7d36', '277816b2a1df8f925dc7e21708dbe484','2.10.3'),
	            /*2.10.2.0*/
	                    array('index.php', 'e1239574cdcbe65d7d27bb6a8212dca3', '946faca1c0462bc33f3601966a597ecc','2.10.2'),
	                    array('contao/index.php', '27cb36a9489e3c937388b2ad1b523b8c', 'c074c5a659abcce83662304546282f15','2.10.2'),
	                    array('contao/main.php', '1b09cf86e2660d5bdc43738374dd7d36', '277816b2a1df8f925dc7e21708dbe484','2.10.2'),
	            /*2.10.1.0*/
	                    array('index.php', 'e1239574cdcbe65d7d27bb6a8212dca3', '946faca1c0462bc33f3601966a597ecc','2.10.1'),
	                    array('contao/index.php', '27cb36a9489e3c937388b2ad1b523b8c', 'c074c5a659abcce83662304546282f15','2.10.1'),
	                    array('contao/main.php', '1b09cf86e2660d5bdc43738374dd7d36', '277816b2a1df8f925dc7e21708dbe484','2.10.1'),
	            /*2.10.0.0*/
	                    array('index.php', '7307a3432fd0c012eb79c01f09c17001', '9fbd48db6c18ac52ddbcca71ebcd4de9','2.10.0'),
	                    array('contao/index.php', '27cb36a9489e3c937388b2ad1b523b8c', 'c074c5a659abcce83662304546282f15','2.10.0'),
	                    array('contao/main.php', '1b09cf86e2660d5bdc43738374dd7d36', '277816b2a1df8f925dc7e21708dbe484','2.10.0'),
	                 );
	
	}//getFileList
	
	/**
	 * Send eMail to Admin
	 */
	private function sendCheckEmail()
	{
	    if ($GLOBALS['TL_CONFIG']['mod_integrity_check']['send_email_to_admin'] == false) 
	    {
	        return; //email to admin is off
	    }
	    if (!isset($GLOBALS['TL_CONFIG']['adminEmail'])) 
	    {
	        return; //admin email not set
	    }
	    // Notification
	    list($GLOBALS['TL_ADMIN_NAME'], $GLOBALS['TL_ADMIN_EMAIL']) = $this->splitFriendlyName($GLOBALS['TL_CONFIG']['adminEmail']); //from index.php
	    $objEmail = new Email();
	    $objEmail->from     = $GLOBALS['TL_ADMIN_EMAIL'];
	    $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
	    
	    $objEmail->subject  = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['subject']  , $this->Environment->host);
	    $objEmail->text     = sprintf($GLOBALS['TL_LANG']['tl_integrity_check']['message_1'], $this->Environment->host);
	    
	    foreach ($this->fileStatus as $key => $value)
	    {
	        if ($value[1] === false)
	        {
	            $objEmail->text .= "\n".$value[0];
	        }
	    }
	    $objEmail->text .= "\n\n".$GLOBALS['TL_LANG']['tl_integrity_check']['message_2'];
	    $objEmail->sendTo($GLOBALS['TL_CONFIG']['adminEmail']);
	    return ;
	    
	}//sendCheckEmail
	
	
}
?>