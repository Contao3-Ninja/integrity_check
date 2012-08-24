<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');
/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * Modul Integrity Check - Backend DCA tl_integrity_check
 * 
 * This is the data container array for table tl_integrity_check.
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    Integrity_Check 
 * @license    LGPL 
 * @filesource
 */


/**
 * Table tl_integrity_check
 */
$GLOBALS['TL_DCA']['tl_integrity_check'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true
	),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
            'fields'                  => array('check_title'),
        ),
		'label' => array
		(
			'fields'                  => array('check_title'),
			'format'                  => '%s' ,
		    'label_callback'          => array('tl_integrity_check', 'listChecks')
		),
		'operations' => array
		(
			'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['toggle'],
				'icon'                => 'visible.gif',
				//'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_integrity_check', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
		
    ),
	// Palettes
	'palettes' => array
	(
	      //'__selector__'                => array(),
		  'default'                     => 'check_title;check_plans;published,check_debug'
	),
    // Subpalettes
    /*
	'subpalettes' => array
	(
		'sub_fields'                => 'field1,field2'
	),
	*/


	// Fields
	'fields' => array
	(
    	'check_title' => array
    	(
	        'label'                   => &$GLOBALS['TL_LANG']['tl_integrity_check']['check_title'],
	        'exclude'                 => true,
	        'inputType'               => 'text',
	        'eval'                    => array('tl_class' => 'w50', 'mandatory'=>true, 'maxlength'=>255)
    	),
		'check_debug' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class' => 'w50')
		),
		'check_plans' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
		    (
		        'columnFields' => array
	            (
		            'cp_files' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array
	                    (
                            'index.php'         => 'index.php',
                            'cron.php'          => 'cron.php',
                            'contao/index.php'  => 'contao/index.php',
                            'contao/main.php'   => 'contao/main.php',
	                    ),
	                    'eval' 			        => array('style' => 'width:150px', 'includeBlankOption'=>true, 'chosen'=>true)
		            ),
		            'cp_interval' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
		                //'options'               => array('hourly','daily','weekly','monthly'),
		                'options_callback'      => array('tl_integrity_check', 'getCronIntervals'),
		                'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:150px', 'includeBlankOption'=>false, 'chosen'=>true)
		            ),
		            'cp_type_of_test' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array('md5'), // 'timestamp'
	                    'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:100px', 'includeBlankOption'=>false, 'chosen'=>true)
		            ),
		            'cp_action' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array('only_logging','admin_email'), //'restore','maintenance_mode'
		                'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:150px', 'includeBlankOption'=>false, 'chosen'=>true)
		            )
                )//columnFields
		    )//eval of check_plans
		),//check_plans
		'published' => array
		(
	        'exclude'             => true,
	        'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['published'],
	        'inputType'           => 'checkbox',
	        'eval'                => array('doNotCopy'=>true, 'tl_class' => 'w50'),
	        'save_callback' => array
	        (
	                array('tl_integrity_check', 'setPublished')
	        )
		)
	)//fields
);

class tl_integrity_check extends Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
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
        if (strlen($this->Input->get('tid')))
        {
            $this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
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
        $this->Input->setGet('id', $intId);
        $this->Input->setGet('act', 'toggle');
        
    
        // Check permissions to publish
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_integrity_check::published', 'alexf'))
        {
            $this->log('Not enough permissions to publish/unpublish integrity check "'.$intId.'"', 'tl_integrity_check toggleVisibility', TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }
    
        // Update the database
        $this->Database->prepare("UPDATE tl_integrity_check SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
                       ->execute($intId);
        // There can be only one.
        $this->Database->prepare("UPDATE tl_integrity_check SET tstamp=". time() .", published='' WHERE id!=?")
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
    <td class="tl_file_list" style="width: 30%;"><span class="cp_files">'. $step['cp_files'].'</span></td>
    <td class="tl_file_list" style="width: 24%;"><span class="cp_interval">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_interval']].'</span></td>
    <td class="tl_file_list" style="width: 22%;"><span class="cp_type_of_test">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_type_of_test']].'</span></td>
    <td class="tl_file_list" style="width: 24%;"><span class="cp_action">'. $GLOBALS['TL_LANG']['tl_integrity_check'][$step['cp_action']].'</span></td>
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
            $this->Database->prepare("UPDATE tl_integrity_check SET tstamp=". time() .", published='' WHERE id!=?")
                           ->execute($dc->id);
        }
        return $varValue; 
    }
    
    /**
     * Return all possible cron moments
     * @param DataContainer
     * @return array
     */
    public function getCronIntervals()
    {
        $arrCronIntervals = array('hourly','daily','weekly','monthly');
        //hourly not in Contao 2.10
        if (version_compare(VERSION, '2.11', '<'))
        {
            $arrCronIntervals = array('daily','weekly','monthly');
        }
        return $arrCronIntervals;
    }

}
?>