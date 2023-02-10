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

use Joomla\CMS\Factory;
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
$controller = JControllerLegacy::getInstance('OxfordSMSFiles');

// Perform the Request task
$jinput = Factory::getApplication()->input;
$task = $jinput->getCmd('task');

$controller->execute($task);
 
// Redirect if set by the controller
$controller->redirect();
