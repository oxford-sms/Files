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
 
// import Joomla view library
jimport('joomla.application.component.view');
 
    class OxfordSMSFilesViewFiles extends JViewLegacy
    {
        function display($tpl = null) 
        {
			$document = JFactory::getDocument();
			$document->addCustomTag('<script src="'.JURI::root(true).'/media/jui/js/jquery.min.js"></script>');

            parent::display($tpl);
        }
    }
