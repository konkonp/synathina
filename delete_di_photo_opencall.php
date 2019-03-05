<?php
/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Core');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

//connect to db
$db = JFactory::getDBO();

$lang = JFactory::getLanguage();

$user = JFactory::getUser();

if($user->id>0){
	//remove photo
	@unlink('images/di/'.@$_REQUEST['opencall_id'].'_'.@$_REQUEST['di_id'].'_'.@$_REQUEST['di_filename']);
	@unlink('images/di/'.@$_REQUEST['opencall_id'].'_'.@$_REQUEST['di_id'].'_zoomed_'.@$_REQUEST['di_filename']);
	@unlink('images/di/'.@$_REQUEST['opencall_id'].'_'.@$_REQUEST['di_id'].'_regular_'.@$_REQUEST['di_filename']);
	@unlink('images/di/'.@$_REQUEST['opencall_id'].'_'.@$_REQUEST['di_id'].'_thumb_'.@$_REQUEST['di_filename']);
	$query="DELETE FROM #__di_images WHERE object_image_id='".@$_REQUEST['di_id']."' AND filename='".@$_REQUEST['di_filename']."' AND object_id='".@$_REQUEST['opencall_id']."' LIMIT 1";
	$db->setQuery($query);
	$db->execute();
	echo 1;
}else{
	//header('Location:index.php');
	//exit;
}
?>