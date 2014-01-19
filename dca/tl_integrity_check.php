<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Contao Module "Integrity Check" - Backend DCA tl_integrity_check
 *
 * @copyright  Glen Langer 2012..2013 <http://www.contao.glen-langer.de>
 * @author     Glen Langer (BugBuster)
 * @package    Integrity_Check
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/integrity_check
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
		'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id'    => 'primary'
            )
        ),
		'onload_callback' => array
		(
			array('BugBuster\IntegrityCheck\DCA_integrity_check', 'changeInitOperations'),
		) 
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
		    'label_callback'          => array('BugBuster\IntegrityCheck\DCA_integrity_check', 'listChecks')
		),
		'global_operations' => array
		(
    		'init'                    => array
    		(
    		    'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['init'],
    		    'href'                => '&amp;init=true',
    		    'class'               => 'header_new',
    		    'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_integrity_check']['initConfirm'] . '\'))return false;Backend.getScrollOffset()"'
    		),
			'refresh'                 => array
			(
			    'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['refresh'],
			    'href'                => '&amp;refresh=true',
			    'class'               => 'tl_integrity_check_star',
    			'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_integrity_check']['refreshConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			)
			
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
				'button_callback'     => array('BugBuster\IntegrityCheck\DCA_integrity_check', 'toggleIcon')
			)
		)
		
    ),
	// Palettes
	'palettes' => array
	(
	      //'__selector__'                => array(),
		  'default'                     => 'check_title;check_plans;{expert_legend:hide},check_plans_expert;{alternateemail_legend:hide},alternate_email;{publish_legend},published,check_debug'
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
    	'id' => array
    	(
            'sql'          => "int(10) unsigned NOT NULL auto_increment"
    	),
    	'tstamp' => array
    	(
            'sql'          => "int(10) unsigned NOT NULL default '0'"
    	),
    	'check_title' => array
    	(
	        'label'        => &$GLOBALS['TL_LANG']['tl_integrity_check']['check_title'],
	        'exclude'      => true,
	        'inputType'    => 'text',
	        'eval'         => array('tl_class' => 'w50', 'mandatory'=>true, 'maxlength'=>255),
	        'sql'          => "varchar(255) NOT NULL default ''"
    	),
		'check_plans' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'sql'                     => "blob NULL",
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
                            'index.php'            => 'index.php',
                            'system/cron/cron.php' => 'system/cron/cron.php',
                            'contao/index.php'     => 'contao/index.php',
                            'contao/main.php'      => 'contao/main.php',
	                    ),
	                    'eval' 			        => array('style' => 'width:180px', 'includeBlankOption'=>true, 'chosen'=>true)
		            ),
		            'cp_interval' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
		                'options_callback'      => array('BugBuster\IntegrityCheck\DCA_integrity_check', 'getCronIntervals'),
		                'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:120px', 'includeBlankOption'=>false, 'chosen'=>true)
		            ),
		            'cp_type_of_test' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array('md5','timestamp'),
	                    'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:110px', 'includeBlankOption'=>false, 'chosen'=>true)
		            ),
		            'cp_action' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array('only_logging','admin_email'), //'restore','maintenance_mode'
		                'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:140px', 'includeBlankOption'=>false, 'chosen'=>true)
		            )
                )//columnFields
		    )//eval of check_plans
		),//check_plans
		'check_plans_expert' => array
	    (
	        'label'                   => &$GLOBALS['TL_LANG']['tl_integrity_check']['check_plans_expert'],
	        'exclude'                 => true,
	        'inputType'               => 'multiColumnWizard',
	        'sql'                     => "blob NULL",
	        'eval'                    => array
	        (
                'columnFields' => array
                (
                    'cp_files_expert' => array
                    (
                            'label'             => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_files'],
                            'exclude'           => true,
                            'inputType'         => 'select',
                            'options'         	=> array
                            (
                                '.htaccess'     => '.htaccess',
                            ),
                            'eval' 	            => array('style' => 'width:180px', 'includeBlankOption'=>true, 'chosen'=>true)
                    ),
            		'cp_interval_expert' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_interval'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
		                'options_callback'      => array('BugBuster\IntegrityCheck\DCA_integrity_check', 'getCronIntervals'),
		                'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:120px', 'includeBlankOption'=>false, 'chosen'=>true)
		            ),
		            'cp_type_of_test_expert' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_type_of_test'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array('timestamp'),
	                    'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:110px', 'includeBlankOption'=>false, 'chosen'=>true)
		            ),
		            'cp_action_expert' => array
		            (
	                    'label'                 => &$GLOBALS['TL_LANG']['tl_integrity_check']['cp_action'],
	                    'exclude'               => true,
	                    'inputType'             => 'select',
	                    'options'            	=> array('only_logging','admin_email'), //'restore','maintenance_mode'
		                'reference'             => &$GLOBALS['TL_LANG']['tl_integrity_check'],
	                    'eval' 			        => array('style' => 'width:140px', 'includeBlankOption'=>false, 'chosen'=>true)
		            )
            	)//columnFields
            )//eval of check_plans_expert
        ),//check_plans_expert
        'alternate_email' => array
        (
            'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['alternateemail'],
            'exclude'             => true,
			'inputType'           => 'text',
			'eval'                => array('mandatory'=>false, 'rgxp'=>'email', 'maxlength'=>255, 'unique'=>false, 'decodeEntities'=>true, 'tl_class'=>'w50'),
			'sql'                 => "varchar(255) NOT NULL default ''"
        ),
		'published' => array
		(
	        'exclude'             => true,
	        'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['published'],
	        'inputType'           => 'checkbox',
	        'eval'                => array('doNotCopy'=>true, 'tl_class' => 'w50'),
	        'sql'                 => "char(1) NOT NULL default ''",
	        'save_callback' => array
	        (
	                array('BugBuster\IntegrityCheck\DCA_integrity_check', 'setPublished')
	        )
		),
		'check_debug' => array
		(
			'label'               => &$GLOBALS['TL_LANG']['tl_integrity_check']['check_debug'],
			'exclude'             => true,
			'inputType'           => 'checkbox',
			'eval'                => array('tl_class' => 'w50'),
			'sql'                 => "char(1) NOT NULL default ''"
		)
	)//fields
);
