<?php
/**
 * OxfordSMS - Files Joomla! Native Component
 * @version 1.0.1
 * @author Ivan Komlev <ivankomlev@oxford.edu.pa>
 * @link http://oxfordsms.com
 * @GNU General Public License
 **/


// No direct access to this file
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

use Joomla\CMS\Version;

class OxfordSMSFilesViewFiles extends JViewLegacy
{
    function display($tpl = null)
    {
        //JQuery and Bootstrap
        $document = JFactory::getDocument();

        $version_object = new Version;
        $version = (int)$version_object->getShortVersion();

        if ($version < 4) {
            $document->addCustomTag('<script src="' . URI::root(true) . '/media/jui/js/jquery.min.js"></script>');
            $document->addCustomTag('<script src="' . URI::root(true) . '/media/jui/js/bootstrap.min.js"></script>');
        } else {

            HTMLHelper::_('jquery.framework');
            $document->addCustomTag('<link rel="stylesheet" href="' . URI::root(true) . '/media/system/css/fields/switcher.css">');
        }
        parent::display($tpl);
    }
}
