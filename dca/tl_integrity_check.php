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
            'mode'                    => 0,
            'fields'                  => array('check_title'),
        ),
		'label' => array
		(
			'fields'                  => array('check_title'),
			'format'                  => '%s' ,
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
			),/*
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_article']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_article', 'toggleIcon')
			),*/
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
		  'default'                     => 'check_title,check_debug;check_plans'
	),
    // Subpalettes
    /*
	'subpalettes' => array
	(
		'banner_until'                => 'banner_views_until,banner_clicks_until'
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
			'eval'                    => array('tl_class' => 'w50 m12')
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
                            'contao/index.php'  => 'contao/index.php',
                            'contao/main.php'   => 'contao/main.php',
	                    ),
	                    'eval' 			        => array('style' => 'width:150px', 'includeBlankOption'=>true, 'chosen'=>false, 'submitOnChange'=>true)
		            ),
		            'cp_moment' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_moment'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array
	                    (
	                        'hourly'            => 'hourly',
	                        'daily'             => 'daily',
	                        'weekly'            => 'weekly',
	                        'monthly'           => 'monthly',
	                    ),
	                    'eval' 			        => array('style' => 'width:150px', 'includeBlankOption'=>false, 'submitOnChange'=>true)
		            ),
		            'cp_type_of_test' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array
	                    (
                            'md5'               => 'MD5',
                            'timestamp'         => 'Timestamp',
	                    ),
	                    'eval' 			        => array('style' => 'width:100px', 'includeBlankOption'=>false, 'submitOnChange'=>true)
		            ),
		            'cp_action' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array
	                    (
	                        'only_logging'      => 'System Log',
                            'admin_email'       => 'eMail to Admin',
                            //'restore'           => 'Restore',
                            //'maintenance_mode'  => 'Maintenance Modus'
                            
	                    ),
	                    'eval' 			        => array('style' => 'width:150px', 'includeBlankOption'=>false, 'submitOnChange'=>true)
		            )
                )//columnFields
		    )//eval of check_plans
		)//check_plans
	)//fields
);



?>