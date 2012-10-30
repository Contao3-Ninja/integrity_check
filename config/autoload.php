<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Integrity_check
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'BugBuster',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'BugBuster\IntegrityCheck\Integrity_Check'          => 'system/modules/integrity_check/modules/Integrity_Check.php',

	// Classes
	'BugBuster\IntegrityCheck\IntegrityCheckHelper'     => 'system/modules/integrity_check/classes/IntegrityCheckHelper.php',
));
