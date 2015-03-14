<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
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
	// Classes
	'BugBuster\IntegrityCheck\DcaIntegrityCheck'     => 'system/modules/integrity_check/classes/DcaIntegrityCheck.php',
	'BugBuster\IntegrityCheck\IntegrityCheckBackend' => 'system/modules/integrity_check/classes/IntegrityCheckBackend.php',
	'BugBuster\IntegrityCheck\IntegrityCheckHelper'  => 'system/modules/integrity_check/classes/IntegrityCheckHelper.php',

	// Modules
	'BugBuster\IntegrityCheck\Integrity_Check'       => 'system/modules/integrity_check/modules/Integrity_Check.php',
));
