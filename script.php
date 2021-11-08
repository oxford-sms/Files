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

class com_OxfordSMSFilesInstallerScript
{
    function postflight($route, $adapter)
    {
		$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customtables' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'customtables' . DIRECTORY_SEPARATOR;
		$loader_file = $path.'loader.php';
			
		if(!file_exists($loader_file))
		{
			JFactory::getApplication()->enqueueMessage('Please install CustomTables extension.','error');
			return false;
		}
		
		require_once($loader_file);
		CTLoader(true);
		
		$smsfile=JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_oxfordsms'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'components.php';

		if(!file_exists($smsfile))
		{
			echo '<p style="color:white;background-color:red;">Please install Oxford SMS extension.</p>';
			return false;
		}

		require_once($smsfile);

		if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'sharedfiles'))
			mkdir(JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'sharedfiles');

		$image='images\/icons\/toolbar\/files-32.png';

		$params_string='{"sfl_dirlocation":".\/images\/sharedfiles","sfl_basepath":"\/images\/sharedfiles\/","sfl_maxfiles":"1024","sfl_next":"1","sfl_showfilesize":"1","sfl_maxheight":"0","sfl_bgcolor":"#ffffff","sfl_onlyimg":"0","sfl_imgthumbs":"2","sfl_thumbheight":"30","sfl_thumbkeepaspect":"1","sfl_listdir":"1","sfl_browsedir":"1","sfl_useusernameddir":"0","sfl_usernameddirdefault":"0","sfl_userlocation":".\/users\/","sfl_basepathusr":"","sfl_allowupdir":"0","sfl_allowdelete":"0","sfl_allowdeleteall":"0","sfl_allowdeletereg":"0","sfl_allowdeleteedt":"0","sfl_movedeleted":"0","sfl_movedeletedpath":".\/sfl_deletedfiles","sfl_disablegdthreshold":"0","cache":"1","cache_time":"900","cachemode":"itemid","sfl_showdir":"1","sfl_showicon":"1","sfl_sortorder":"asc","sfl_showsort":"0","sfl_boxleft":"0","sfl_listleft":"0","moduleclass_sfx":"","menu-anchor_title":"","menu-anchor_css":"","menu_image":"'.$image.'","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}';

		$file=JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_oxfordsmsfiles'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'files-32.png';
		$newfile=JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'icons'.DIRECTORY_SEPARATOR.'toolbar'.DIRECTORY_SEPARATOR.'files-32.png';

		if (!copy($file, $newfile))
		{
			echo "failed to copy $file...\n";
		}

		OxfordSMSComponents::addMenu('Shared Files','oxfordsms-shared-files','index.php?option=com_oxfordsmsfiles&view=files'
									 ,'oxfordsms','com_oxfordsmsfiles','13',$params_string);

		return true;
	}
}
