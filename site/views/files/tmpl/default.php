<?php
/**
 * OxfordSMS - Files Joomla! Native Component
 * @version 1.0.1
 * @author Ivan Komlev <ivankomlev@oxford.edu.pa>
 * @link http://oxfordsms.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app		= JFactory::getApplication();
$params=$app->getParams();

require_once('helper.php');
include('mod_simplefilelisterv1.0.php');