<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Contao Module "Integrity Check" - DCA Helper Class DCA_integrity_check
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
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
 * DCA Helper Class DCA_integrity_check
 * 
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check
 *
 */
class DCA_integrity_check extends \Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
        if (strlen(\Input::get('refresh')))
        {
            $this->refreshTimestamps();
        }
        if (strlen(\Input::get('init')))
        {
            $this->importCheckPlan();
        }
    }

    /**
     * Return the "toggle visibility" button
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(\Input::get('tid')))
        {
            $this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_integrity_check::published', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
    }


    /**
     * Disable/enable a check
     * @param integer
     * @param boolean
     */
    public function toggleVisibility($intId, $blnVisible)
    {
        // Check permissions to edit
        \Input::setGet('id', $intId);
        \Input::setGet('act', 'toggle');


        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_integrity_check::published', 'alexf'))
        {
            $this->log('Not enough permissions to publish/unpublish integrity check "'.$intId.'"', 'tl_integrity_check toggleVisibility', TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }

        // Update the database
        \Database::getInstance()->prepare("UPDATE 
                                                tl_integrity_check 
                                            SET 
                                                tstamp=". time() ."
                                                , published='" . ($blnVisible ? 1 : '') . "' 
                                            WHERE 
                                                id=?")
                                ->execute($intId);
        // There can be only one.
        \Database::getInstance()->prepare("UPDATE 
                                                tl_integrity_check 
                                            SET 
                                                tstamp=". time() ."
                                                , published='' 
                                            WHERE 
                                                id!=?")
                                ->execute($intId);

    }

    /**
     * Label Callback
     * @param array $arrRow
     * @return string
     */
    public function listChecks($arrRow)
    {
        $lineCount = 0;
        $check_plans = deserialize($arrRow[check_plans]);
        $title ='
  <table class="tl_listing_checks">
  <tr>
    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files'][0].'</td>
    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval'][0].'</td>
    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'][0].'</td>
    <td class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action'][0].'</td>
  </tr>
  ';
        if (count($check_plans) > 0)
        {
            //Zeilenweise den Plan durchgehen
            foreach ($check_plans as $step)
            {
                $class = (($lineCount % 2) == 0) ? ' even' : ' odd';
                $title .= '<tr class='.$class.'>
    <td class="tl_file_list" style="width: 32%;"><span class="cp_files">'. $step['cp_files'].'</span></td>
    <td class="tl_file_list" style="width: 22%;"><span class="cp_interval">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_interval']].'</span></td>
    <td class="tl_file_list" style="width: 22%;"><span class="cp_type_of_test">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_type_of_test']].'</span></td>
    <td class="tl_file_list" style="width: 24%;"><span class="cp_action">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_action']].'</span></td>
  </tr>
  ';
                $lineCount++;
            }
        }
        //Expert Tests
        $check_plans_expert = deserialize($arrRow[check_plans_expert]);
        if (count($check_plans_expert) > 0)
        {
            //Zeilenweise den Plan durchgehen
            foreach ($check_plans_expert as $step)
            {
                $class = (($lineCount % 2) == 0) ? ' even' : ' odd';
                $title .= '<tr class='.$class.'>
    <td class="tl_file_list" style="width: 32%;"><span class="cp_files">'. $step['cp_files_expert'].'</span></td>
    <td class="tl_file_list" style="width: 22%;"><span class="cp_interval">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_interval_expert']].'</span></td>
    <td class="tl_file_list" style="width: 22%;"><span class="cp_type_of_test">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_type_of_test_expert']].'</span></td>
    <td class="tl_file_list" style="width: 24%;"><span class="cp_action">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_action_expert']].'</span></td>
  </tr>
  ';
                $lineCount++;
            }
        }
        
        $title .= '</table>
';
        return $title;
    }

    /**
     * Set published to '' on other checks
     * @param mixed
     * @param DataContainer
     * @return string
     */
    public function setPublished($varValue, DataContainer $dc)
    {
        if ($varValue)
        {
            // There can be only one.
            \Database::getInstance()->prepare("UPDATE 
                                                    tl_integrity_check 
                                                SET 
                                                    tstamp=". time() ."
                                                    , published='' 
                                                WHERE 
                                                    id!=?")
                                    ->execute($dc->id);
        }
        return $varValue;
    }

    /**
     * Return all possible cron moments
     * @return array
     */
    public function getCronIntervals()
    {
        $arrCronMoments = array('hourly','daily','weekly','monthly');

        if ( isset($GLOBALS['TL_CRON']['minutely']) &&
                count($this->searchCron($GLOBALS['TL_CRON']['minutely'], 0, 'IntegrityCheck\Integrity_Check')) > 0 )
        {
            $arrCronMoments = array('minutely','hourly','daily','weekly','monthly');
        }

        return $arrCronMoments;
    }

    /**
     * Refresh Timestamps in Database
     */
    public function refreshTimestamps($redirect = true)
    {
        $insertId = 0;
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
                $objFile = new \File($arrFile);
                $arrTimestamps[$arrFile] = $objFile->mtime;
                $objFile->close();
            }
        }
        // Insert Ignore
        $objInsert = \Database::getInstance()->prepare("INSERT IGNORE INTO 
                                                            `tl_integrity_timestamps` 
                                                            ( `id` , `tstamp` , `check_timestamps` )
                                                        VALUES 
                                                            (?, ?, ?)")
                                             ->execute(1, time(), serialize($arrTimestamps));
        if ($objInsert->insertId == 0)
        {
            // Update the database
            \Database::getInstance()->prepare("UPDATE 
                                                    tl_integrity_timestamps 
                                                SET 
                                                    tstamp=?
                                                    ,check_timestamps=? 
                                                WHERE 
                                                    id=?")
                                    ->execute(time(),serialize($arrTimestamps),1);
        }
        $this->addConfirmationMessage($GLOBALS['TL_LANG']['tl_integrity_check']['refresh_confirm_message']);
        if ($redirect)
        {
            $this->redirect($this->getReferer());
        }
    }
    
    /**
     * Refresh Timestamp only for .htaccess in Database if necessary
     */
    public function refreshTimestampOnlyHtaccess()
    {
        $insertId = 0;
        $status   = false;
        $arrTimestamps = array();

        // ist htaccess check aktiviert und keine checksumme dafür da?
        // dann Summe bilden und entweder mergen mit den anderen Summen oder nur diese einfügen.
        $objTimestamps = \Database::getInstance()->prepare("SELECT 
                                                                `check_timestamps` 
                                                            FROM 
                                                                `tl_integrity_timestamps` 
                                                            WHERE 
                                                                `id`=?")
                                                 ->execute(1);
        if ($objTimestamps->numRows > 0)
        {
            //vorhandene Summen holen
            $arrTimestamps = deserialize($objTimestamps->check_timestamps);
            //wenn htaccess schon Summe hat dann raus
            if ( isset($arrTimestamps['.htaccess']) ) 
            {
                return ;
            }
        }
        else 
        {
            //keinerlei Summen da, dann gleich alle bilden
            $this->refreshTimestamps(false);
            return ;
        }
        
        //keine Summe für htaccess vorhanden, aber andere       
        $arrFiles = array // es könnten ja noch mehr werden
        (
            '.htaccess' 
        );
        foreach ($arrFiles as $arrFile)
        {
            if (is_file(TL_ROOT . '/' . $arrFile))
            {
                $objFile = new \File($arrFile);
                $arrTimestamps[$arrFile] = $objFile->mtime;
                $objFile->close();
                $status = true;
            }
        }
        
        if ($status === true) 
        {
            // Update the database
            \Database::getInstance()->prepare("UPDATE 
                                                    tl_integrity_timestamps 
                                                SET 
                                                    tstamp=?
                                                    ,check_timestamps=? 
                                                WHERE 
                                                    id=?")
                                    ->execute(time(),serialize($arrTimestamps),1);
        }
        return ;
    }

    protected function importCheckPlan()
    {
        //Initial Füllung
        $cp = array(
                0 => array(
                        'cp_files' => 'index.php',
                        'cp_interval' => 'hourly',
                        'cp_type_of_test' => 'md5',
                        'cp_action' => 'admin_email'
                ),
                1 => array(
                        'cp_files' => 'system/cron/cron.php',
                        'cp_interval' => 'hourly',
                        'cp_type_of_test' => 'md5',
                        'cp_action' => 'admin_email'
                ),
                2 => array(
                        'cp_files' => 'contao/index.php',
                        'cp_interval' => 'daily',
                        'cp_type_of_test' => 'md5',
                        'cp_action' => 'only_logging'
                ),
                3 => array(
                        'cp_files' => 'contao/main.php',
                        'cp_interval' => 'daily',
                        'cp_type_of_test' => 'md5',
                        'cp_action' => 'only_logging'
                                )
        );

        $arrSet = array
        (
                'id'          => 0,
                'tstamp'      => 1234567890,//time(),
                'check_debug' => '',
                'check_plans' => ''.serialize($cp).'',
                'check_title' => 'Integrity Check',
                'published'   => 0
        );

        $objInsert = \Database::getInstance()->prepare("INSERT INTO 
                                                            `tl_integrity_check` 
                                                            %s")
                                             ->set($arrSet)
                                             ->execute();
        $this->addConfirmationMessage($GLOBALS['TL_LANG']['tl_integrity_check']['init_confirm_message']);
        $this->redirect($this->getReferer());
         
    }

    public function changeInitOperations()
    {
        $objCount = \Database::getInstance()->query("SELECT 
                                                        `id` 
                                                     FROM 
                                                        `tl_integrity_check`");
        if ($objCount->numRows != 0 )
        {
            //delete init button definition
            unset($GLOBALS['TL_DCA']['tl_integrity_check']['list']['global_operations']['init']);
            $this->refreshTimestampOnlyHtaccess();
        }

    }

    /**
     * Multidimensional array search function, here for Cron
     * @author sunelbe at gmail dot com
     * @return array
     */
    public function searchCron($array, $key, $value)
    {
        $results = array();

        if (is_array($array))
        {
            if (isset($array[$key]) && $array[$key] == $value)
            {
                $results[] = $array;
            }

            foreach ($array as $subarray)
            {
                $results = array_merge($results, $this->searchCron($subarray, $key, $value));
            }
        }

        return $results;
    }


}
