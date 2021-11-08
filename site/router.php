<?php
/**
 * OxfordSMS - Files Joomla! Native Component
 * @version 1.0.1
 * @author Ivan Komlev <ivankomlev@oxford.edu.pa>
 * @link http://oxfordsms.com
 * @GNU General Public License
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

function OxfordSMSFilesBuildRoute(&$query) {


       $segments = array();
       
       if(isset($query['view']))
       {
	      if (empty($query['Itemid'])) {
		     $segments[] = $query['view'];
	      }
              unset( $query['view'] );
       }
       return $segments;

}

function OxfordSMSFilesParseRoute($segments)
{
       $vars = array();
       return $vars;

}
