<?php
/**
 * Simple File Lister Module Entry Point
 *
 * @package    Joomla
 * @subpackage Modules
 * @author Anders Was�n
 * @link http://wasen.net/
 * @license        GNU/GPL, see LICENSE.php
 * mod_simplefileupload is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 *
 * Modified by Ivan Komlev Jan 4, 2017
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class ModSimpleFileListerHelperv10
{
    static function getFileList(
        $params,
        $sfl_dirlocation,
        $sfl_basepath,
        $sfl_maxfiles,
        $sfl_userlocation
    )
    {

        $results = "";

        $session = JFactory::getSession();

        // Reset session var. we had a reload if we enter this way!
        $session->set('sfl_startdir', '');
        $session->set('sfl_userdir', '');

        $results = "<div style=\"text-align: left\">";

        if (strlen($sfl_dirlocation) == 0 && strlen($sfl_userlocation) == 0) {
            $results .= JText::_('NO_DIR_GIVEN');
        } else {
            $results .= ModSimpleFileListerHelperv10::getDirContents($params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation);
        }

        $results .= "</div>";

        return $results;
    }

    static function getDirContents($params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation)
    {

        $session = JFactory::getSession();
        $results = "";
        $sfl_goupdir = "";
        $sfl_currentdir = "";
        $browsedir = "";
        $filelist = "";
        $sfl_dirlocationdefault = $params->get('sfl_dirlocation', '.' . DIRECTORY_SEPARATOR . 'images');
        $sfl_next = $params->get('sfl_next', '0');
        $sfl_showfilesize = $params->get('sfl_showfilesize', '0');
        $sfl_onlyimg = $params->get('sfl_onlyimg', '0');
        $sfl_imgthumbs = $params->get('sfl_imgthumbs', '0');
        $sfl_thumbheight = $params->get('sfl_thumbheight', '30');
        $sfl_thumbwidth = $params->get('sfl_thumbwidth', '30');
        $sfl_thumbkeepaspect = $params->get('sfl_thumbkeepaspect', '0');
        $sfl_listdir = $params->get('sfl_listdir', '0');
        $sfl_browsedir = $params->get('sfl_browsedir', '0');
        $sfl_showdir = $params->get('sfl_showdir', '1');
        $sfl_showicon = $params->get('sfl_showicon', '1');
        $sfl_sortorder = $params->get('sfl_sortorder', 'asc');
        $sfl_showsort = $params->get('sfl_showsort', '0');
        $sfl_setbasepath = $params->get('sfl_basepath', '');
        $sfl_basepathusr = $params->get('sfl_basepathusr', '');
        $sfl_listleft = $params->get('sfl_listleft', '-10');

        $sfl_allowdelete = $params->get('sfl_allowdelete', '0');
        $sfl_allowdeleteall = $params->get('sfl_allowdeleteall', '0');
        $sfl_allowdeletereg = $params->get('sfl_allowdeletereg', '0');
        $sfl_allowdeleteedt = $params->get('sfl_allowdeleteedt', '0');
        $sfl_movedeleted = $params->get('sfl_movedeleted', '0');
        $sfl_movedeletedpath = $params->get('sfl_movedeletedpath', '');
        $sfl_disablegdthreshold = $params->get('sfl_disablegdthreshold', '0');
        $sfl_allowupdir = $params->get('sfl_allowupdir', '0');

        $subdirlocation = "";

        $tmpSort = $session->get('sfl_sort', '');
        if (strlen($tmpSort) > 0)
            $sfl_sortorder = $tmpSort;

        // Get current logged in user
        $user = JFactory::getUser();
        $usr_name = $user->get('username');
        if (stripos($usr_name, "/") !== false) {
            $usr_name = "";
        }
        if (stripos($usr_name, "\\") !== false) {
            $usr_name = "";
        }
        if (stripos($usr_name, "..") !== false) {
            $usr_name = "";
        }

        $show_delete = "0";

        if ($sfl_allowdelete === "1") {
            if ($sfl_allowdeleteall === "1")
                $show_delete = "1";
            if ($sfl_allowdeletereg === "1" && !$user->guest)
                $show_delete = "1";
            if ($sfl_allowdeleteedt === "1" && $user->authorise('core.edit', 'com_content'))
                $show_delete = "1";
        }
//echo "sfl_dirlocation=$sfl_dirlocation<br/>";
//echo "sfl_dirlocationdefault=$sfl_dirlocationdefault<br/>";

        // Don't allow moving upwards in dirs through AJAX
        if ($sfl_allowupdir == 0 && strlen(strstr($sfl_dirlocation, "../")) > 0) $sfl_dirlocation = $sfl_dirlocationdefault;

        if (strlen(strstr($sfl_dirlocation, $sfl_dirlocationdefault)) <= 0) $sfl_dirlocation = $sfl_dirlocationdefault;

//echo "sfl_dirlocation=$sfl_dirlocation<br/>";

        // If only sfl_userlocation is set!
        if (strlen($sfl_dirlocation) == 0 || (strlen($sfl_dirlocation) > 0 && strlen($sfl_userlocation) > 0)) {
            $sfl_dirlocation = $sfl_userlocation;
            if (substr($sfl_dirlocation, strlen($sfl_dirlocation) - 1) !== DIRECTORY_SEPARATOR)
                $sfl_dirlocation .= DIRECTORY_SEPARATOR;

            if (strlen($sfl_basepathusr) > 0 && $usr_name !== "") {
                if (substr($sfl_basepathusr, strlen($sfl_basepathusr) - 1) !== DIRECTORY_SEPARATOR)
                    $sfl_basepathusr .= "/" . $usr_name . "/";
                else
                    $sfl_basepathusr .= $usr_name . "/";

                $sfl_setbasepath = $sfl_basepathusr;
                $session->set('sfl_userdir', $sfl_setbasepath);
            }
        } else {
            // check if we are in "user" mode
            $sfl_basepathusr = $session->get('sfl_userdir', '');
            if (strlen($sfl_basepathusr) > 0) $sfl_setbasepath = $sfl_basepathusr;
        }
//echo "sfl_dirlocation=$sfl_dirlocation<br/>";
//echo "sfl_setbasepath=$sfl_setbasepath<br/>";

        $baseurl = ModSimpleFileListerHelperv10::getBaseURL($sfl_dirlocation, $sfl_setbasepath);

        //echo '<br>$baseurl='.$baseurl.'<br/>';

        // Remove final slash to get dir.
        if (substr($sfl_dirlocation, strlen($sfl_dirlocation) - 1) === DIRECTORY_SEPARATOR)
            $sfl_dirlocation = substr($sfl_dirlocation, 0, strlen($sfl_dirlocation) - 1);

        $startdir = $session->get('sfl_startdir', '');
        if ($startdir === '')
            $session->set('sfl_startdir', $sfl_dirlocation);

        if (strlen($startdir) > 0 && str_replace(DIRECTORY_SEPARATOR, "", $startdir) !== str_replace(DIRECTORY_SEPARATOR, "", $sfl_dirlocation)) {
            // We have browsed!

            $browsedir = substr($sfl_dirlocation, strlen($startdir));
            // Remove any leading slash
            $browsedir = str_replace(DIRECTORY_SEPARATOR, "/", substr($browsedir, 1));
            // Remove any trialing slash
            if (substr($browsedir, strlen($browsedir) - 1) === DIRECTORY_SEPARATOR)
                $browsedir = substr($browsedir, 0, strlen($browsedir) - 1);
            // Make sure we are working with front-slash only
            $browsedir = str_replace(DIRECTORY_SEPARATOR, "/", $browsedir);

            $sfl_breadcrumb = "<a class=\"sfl_btnBrowseDir\" rel=\"" . $startdir . "\" href=\"javascript: void(0);\">" . $startdir . "</a>/";

            $dirvals = "/";
            $icntdir = 0;
            $pathcol = explode("/", $browsedir);
            foreach ($pathcol as $dirval) {
                $dirvals .= $dirval . "/";
                $icntdir++;
                if ($icntdir < count($pathcol)) {
                    $sfl_breadcrumb .= "<a class=\"sfl_btnBrowseDir\" rel=\"" . $startdir . $dirvals . "\" href=\"javascript: void(0);\">" . $dirval . "</a>/";
                    // Get parent dir for "go up"
                    $sfl_goupdir = "<a class=\"sfl_btnBrowseDir\" rel=\"" . $startdir . $dirvals . "\" href=\"javascript: void(0);\">" . JText::_('UP_DIR') . "</a>";
                } else {
                    $sfl_breadcrumb .= $dirval;
                    $sfl_currentdir = " " . $dirval;
                }
            }

// Fix AW 2001-05-20, if web server path is set subdir is omitted without below
            $subdirlocation = $dirvals;
// Remove initial slash if exist
            if (substr($subdirlocation, 0, 1) === "/") $subdirlocation = substr($subdirlocation, 1);

// Add trainling slash
            /*
            if (substr($subdirlocation, strlen($subdirlocation) - 1) !== "/") $subdirlocation .= "/";
            $baseurl .= $subdirlocation;
            */

//$results .= "[$subdirlocation]";
            /*
$results .= "$baseurl .= $browsedir";
            // Set new browsedir and add one slash at the end if missing
            $baseurl .= $browsedir;
            if ( substr( $baseurl , strlen($baseurl) - 1) !== "/" )
                $baseurl .= "/";
*/
        } else {
            if ($startdir === "") $startdir = $sfl_dirlocation;
            $sfl_breadcrumb = $startdir;
        }

        // Open directory
        if ($bib = @opendir($sfl_dirlocation)) {

            $idx = 0;
            $dir_list = null;
            $file_list = null;

            $idx_startat = $session->get('sfl_nextindex', 0);
            $idx_endat = $session->get('sfl_stopindex', $sfl_maxfiles);
//$results .= "idx_startat=$idx_startat | idx_endat=$idx_endat";

            if ($sfl_next === '1' && $idx_startat > 0)
                $fil_list[] = "<input type=\"hidden\" id=\"sflPrevVal\" value=\"" . $idx_startat . "\" /><a id=\"sfl_btnPrev\" href=\"javascript:void(0)\">" . JText::_('PREV_BTN') . "</a>";
            else
                $fil_list[] = "<input type=\"hidden\" id=\"sflPrevVal\" value=\"-1\" />";
            // Intially set previous to -1 to make sure it exists

            //$fil_list[] = "<form id=\"frm_sflprevious\" value=\"prev\" enctype=\"multipart/form-data\" action=\"\" method=\"POST\"><input type=\"hidden\" name=\"sflPrevious\" value=\"".$idx_startat."\" /><a href=\"javascript:void(0)\" onclick=\"javascript: sfl_MovePrevious(); sflSubmitForm('frm_sflprevious');\">".JText::_('PREV BTN')."</a></form>";


            while (false !== ($lfile = readdir($bib))) {

                if (is_dir($sfl_dirlocation . DIRECTORY_SEPARATOR . $lfile)) {
                    // Safe it, dir or filenames can't contain a *
                    if ($lfile !== "." && $lfile !== ".." && $sfl_listdir == 1)
                        $dir_list[] = array("sort" => strtolower($lfile), "name" => "*dir*" . $lfile);

                } else {

                    $file_list[] = array("sort" => strtolower($lfile), "name" => $lfile);

                }
            }

            //Sort Directories
            if (is_array($dir_list)) {
                //Asc
                if ($sfl_sortorder === "asc")
                    asort($dir_list);

                //Desc
                if ($sfl_sortorder === "desc")
                    rsort($dir_list);
            }

            //Sort Files
            if (is_array($file_list)) {
                //Asc
                if ($sfl_sortorder === "asc")
                    asort($file_list);

                //Desc
                if ($sfl_sortorder === "desc")
                    rsort($file_list);
            }
//print_r($file_list);

            if ($sfl_listdir == 1 && is_array($dir_list) && is_array($file_list))
                $full_list = array_merge($dir_list, $file_list);
            elseif (is_array($file_list))
                $full_list = $file_list;
            elseif ($sfl_listdir == 1 && is_array($dir_list))
                $full_list = $dir_list;
            else
                $full_list = null;
            if (is_array($full_list)) {
                foreach ($full_list as $lfile) {

                    //echo $lfile['name'];

                    if ($lfile['name'] != '*dir*.cache') {
                        //while (false !== ($lfile = readdir($bib))) {
                        //$sfl_listdir
                        //$sfl_browsedir
                        $fdir = (substr($lfile['name'], 0, 5) === "*dir*");

                        if ($lfile['name'] != "." && $lfile['name'] != ".." && !preg_match("/^\..+/", $lfile['name']) && $lfile['name'] != "index.html") {

                            // Capture a list of files to be put in session var. This to protect delete
                            $filelist .= $lfile['name'] . '*';

                            if ($idx >= $idx_endat) {
                                $session->set('sfl_nextindex', $idx);
                                $session->set('sfl_stopindex', $idx + $sfl_maxfiles);
                                break;
                            }

                            $idx += 1;

                            if ($idx > $idx_startat && $idx <= $idx_endat) {
                                $tmpfile = "<nobr>";
                                $tmpthumb = "";
                                $is_img = false;

                                if (($sfl_imgthumbs === '1' || $sfl_imgthumbs === '2') && !$fdir) {
                                    //Check image

                                    if ((filesize($sfl_dirlocation . DIRECTORY_SEPARATOR . $lfile['name']) <= $sfl_disablegdthreshold) || ($sfl_disablegdthreshold == 0)) {

                                        if ($img = @getimagesize($sfl_dirlocation . DIRECTORY_SEPARATOR . $lfile['name'])) {
                                            // Show thumbnail
                                            //if($img = @getimagesize($baseurl.str_replace(" ", "%20", $lfile['name']))) {
                                            //list($width, $height, $type, $attr) = getimagesize($baseurl.str_replace(" ", "%20", $lfile['name']));
                                            list($width, $height, $type, $attr) = getimagesize($sfl_dirlocation . DIRECTORY_SEPARATOR . $lfile['name']);
                                            if (($height > $sfl_thumbheight) && ($sfl_thumbkeepaspect === '1'))
                                                $tmpthumb = "<img border=\"0\" style='height:" . $sfl_thumbheight . "px !important;' height='$sfl_thumbheight' style='width:" . $sfl_thumbheight . "px !important;' width='$sfl_thumbheight' src=\"" . $baseurl . str_replace(" ", "%20", $lfile['name']) . "\"  title='" . $lfile['name'] . "' alt='" . $lfile['name'] . "'  />";
                                            else
                                                $tmpthumb = "<img border=\"0\" height=$sfl_thumbheight width=$sfl_thumbheight src=\"" . $baseurl . str_replace(" ", "%20", $lfile['name']) . "\" title='" . $lfile['name'] . "' alt='" . $lfile['name'] . "' />";

                                            $tmpthumb .= ' ' . $lfile['name'];

                                            $is_img = true;
                                        } else {
                                            // no thumbnail and show icon
                                            if ($sfl_showicon == 1) {
                                                $imagePath = self::getFileFormatImagesLink($lfile['name']);
                                                $tmpfile .= '<img height="16" src="' . $imagePath.'" />';
                                            }
                                        }
                                    } else {
                                        $imagePath = self::getFileFormatImagesLink($lfile['name']);
                                        $tmpfile .= '<img height="16" src="' . $imagePath.'" />';
                                    }

                                } elseif ($fdir) {
                                    $lfile['name'] = substr($lfile['name'], 5);
                                    $imagePath = JURI::root() . 'components/com_oxfordsmsfiles/images/directory.png';
                                    $tmpfile .= '<img height="16" src="' . $imagePath . '" title = "' . $lfile['name'] . '" alt = "' . $lfile['name'] . '" />';

                                } elseif ($sfl_showicon == 1) {

                                    $imagePath = self::getFileFormatImagesLink($lfile['name']);
                                    $tmpfile .= '<img height="16" src="' . $imagePath . '" title = "' . $lfile['name'] . '" alt = "' . $lfile['name'] . '" />';
                                }


                                if ($fdir) {
                                    if ($sfl_browsedir == 1)
                                        $tmpfile .= "<a class=\"sfl_btnBrowseDir\" rel=\"" . $sfl_dirlocation . DIRECTORY_SEPARATOR . $lfile['name'] . "\" href=\"javascript: void(0);\">" . $lfile['name'] . "</a>";
                                    else
                                        $tmpfile .= $lfile['name'];
                                } else {
                                    // Add thumbnail as clickable, empty string if no thumb option
                                    $linktext = $tmpthumb;
                                    if ($sfl_imgthumbs !== '2' || !$is_img) {
                                        // Add the filename if it is not an image and/or thumb is not created
                                        $linktext .= $lfile['name'];
                                    }
                                    $tmpfile .= "<a href=\"" . $baseurl . str_replace(" ", "%20", $lfile['name']) . "\" target=\"blank\">" . $linktext . "</a>";
                                }

                                // Show size but not for directories
                                if ($sfl_showfilesize === '1' && !$fdir)
                                    $tmpfile .= " (" . ModSimpleFileListerHelperv10::getFileSizePP(filesize($sfl_dirlocation . DIRECTORY_SEPARATOR . $lfile['name'])) . ")";

                                // Allow delete?
                                if ($show_delete === '1' && !$fdir)
                                    $tmpfile .= " <a class=\"sfl_btnDelete\" rel=\"" . $sfl_dirlocation . DIRECTORY_SEPARATOR . $lfile['name'] . "**" . $lfile['name'] . "\" href=\"javascript:void(0)\"><img class=\"sfldel\" src=\"" . JURI::root() . $sfl_basepath . "images/delete.png\" /></a>";

                                if (($sfl_onlyimg === '1' && $is_img) || ($sfl_onlyimg !== '1'))
                                    $fil_list[] = $tmpfile . "</nobr>";
                            }
                        }


                    }
                }
                $session->set('sfl_filelist', $filelist);
            }

            closedir($bib);

            if ($sfl_next === '1' && $idx >= $idx_endat)
                $fil_list[] = "<input type=\"hidden\" id=\"sflNextVal\" value=\"" . $idx_endat . "\" /><a id=\"sfl_btnNext\" href=\"javascript:void(0)\">" . JText::_('NEXT_BTN') . "</a>";
            //$fil_list[] = "<form id=\"frm_sflnext\" value=\"next\" enctype=\"multipart/form-data\" action=\"\" method=\"POST\"><input type=\"hidden\" name=\"sflNext\" value=\"".$idx_endat."\" /><a id=\"sfl_btnNext\" href=\"javascript:void(0)\" onclick=\"javascript: sfl_MoveNext(); sflSubmitForm('frm_sflnext');\">".JText::_('NEXT BTN')."</a></form>";

            if (is_array($fil_list)) {
                $liste = "<div class=\"sfl_item\">" . join("</div><div class=\"sfl_item\">", $fil_list) . "</div>";
            } else {
                $liste = "<div class=\"sfl_item\">" . JText::_('NO_FILES_FOUND') . " " . $sfl_dirlocation . "</div>";
            }

            $sortascclass = "";
            $sortdescclass = "";
            if ($sfl_sortorder === "desc")
                $sortascclass = "class=\"sfl_shadow\" ";
            elseif ($sfl_sortorder === "asc")
                $sortdescclass = "class=\"sfl_shadow\" ";

            $sort_arrows = "";
            if ($sfl_showsort == 1)
                $sort_arrows = "<div style=\"width: 90%; height: 12px; text-align: right;\"><a id=\"sfl_ASortAsc\" class=\"sfl_ASortAsc\" href=\"javascript:void(0)\"><img id=\"sflSortAsc\" " . $sortascclass . "alt=\"Sort ascending\" style=\"cursor: n-resize ;\" src=\"" . JURI::root() . $sfl_basepath . "images/sort_up.png\" /></a>&nbsp;<a id=\"sfl_ASortDesc\" class=\"sfl_ASortDesc\" href=\"javascript:void(0)\"><img id=\"sflSortDesc\" " . $sortdescclass . "alt=\"Sort descending\" style=\"cursor: n-resize ;\" src=\"" . JURI::root() . $sfl_basepath . "images/sort_down.png\" /></a></div>";

            if ($sfl_showdir == 1) {
                $results .= "<b>" . JText::_('FILES_IN_DIR') . " (" . $sfl_breadcrumb . "):</b>";
            } elseif ($sfl_showdir == 0) {
                $results .= "<b>" . JText::_('FILES_IN_DIR') . $sfl_currentdir . ":</b><br />";
                // If no breadcrumb we must have "home" and "up" buttons
                if (strlen($browsedir) > 0)
                    $results .= "<a class=\"sfl_btnBrowseDir\" rel=\"" . $startdir . "\" href=\"javascript: void(0);\">" . JText::_('GO_HOME') . "</a>&nbsp;&nbsp;" . $sfl_goupdir . "<br />";
            }
            $results .= $sort_arrows . "<div>" . $liste . "</div>";

        } else {
            $results .= "<b>" . JText::_('ERROR_READ') . " (Dir: " . $sfl_dirlocation . ")</b>";
        }

        return $results;


    }

    static function getBaseURL($sfl_dirlocation, $sfl_basepath)
    {
        $baseurl = "";
        $serverurl = "";
        $protocol = "";
        $protocol = "http://";

        $dirlocation = $sfl_dirlocation;

        if (strlen($sfl_basepath) == 0) {

            $tmp_dirlocation = str_replace("\\", "/", $dirlocation);

            if (substr(JURI::base(), 0, 5) === "https") $protocol = "https://";
            $folder = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/"));
            if ($folder === "//") $folder = "";
//print_r($dirlocation."<br/>");
            //Do we have .. in the path?
            if (strpos($dirlocation, "../") >= 0) {
                $dirlocation = realpath($dirlocation);
                $server_root = $_SERVER["DOCUMENT_ROOT"];
                //SCRIPT_FILENAME
                $dirlocation = str_replace($server_root, "/", $dirlocation);
                // Get rid of http:// or https://
                $server_basedir = str_replace("http://", "https://", $_SERVER["HTTP_HOST"]);
                $server_basedir = str_replace("https://", "", $server_basedir);
                $server_basedir = explode(".", $server_basedir);
//print_r($server_basedir[0]."<br/>");
//print_r($dirlocation."<br/>");
                if ($server_basedir[0] === substr($dirlocation, 2, strlen($server_basedir[0])))
                    $dirlocation = "." . substr($dirlocation, strlen($server_basedir[0]) + 2);
                if (substr($dirlocation, 0, 2) === "//")
                    $dirlocation = str_replace("//", "./", $dirlocation);
//print_r($dirlocation);
                //$dirlocation = "[".$server_basedir[0]."][".substr($dirlocation, 2, strlen($server_basedir[0]))."]";
            }
            //TEST 2011-03-24 TEST WITH WINDOWS FULL PATH TO RELATIVE PATH IF UNDER WEB ROOT
            if (strpos($tmp_dirlocation, ":/") >= 0 || substr($tmp_dirlocation, 0, 1) === "/") {
                //We have a root path, check and see if it is under Server root, e.g. make http:// url
                $sfl_realdirlocation = realpath($dirlocation);
                $sfl_realdirlocation = str_replace("\\", "/", $sfl_realdirlocation);
                $server_root = realpath($_SERVER["DOCUMENT_ROOT"]);
                $server_path = str_replace($server_root, "", str_replace("index.php", "", realpath($_SERVER["SCRIPT_FILENAME"])));
                //$dirlocation = "[".$server_root."][".$sfl_realdirlocation."][".$server_path."]";

                $server_root = str_replace("\\", "/", $server_root);
                $server_path = str_replace("\\", "/", $server_path);

                if (strlen(str_replace($server_root, "", $sfl_realdirlocation)) < strlen($sfl_realdirlocation)) {
                    //Path is in server root
                    $dirlocation = str_replace($server_root, ".", $sfl_realdirlocation);
                    //print_r($dirlocation);
                    //print_r($server_path);
                    if (strpos($dirlocation, $server_path) >= 0) {
                        $dirlocation = str_replace($server_path, "/", $dirlocation);
                    }
                }

            }


            // Check if relative path
            if (substr($dirlocation, 0, 1) === ".") {
                // Don't replace all dots... Could be dots in directory name!!!
                //$serverurl .= str_replace(".", $protocol.$_SERVER["HTTP_HOST"].$folder, $dirlocation);
                $serverurl .= $protocol . $_SERVER["HTTP_HOST"] . $folder . substr($dirlocation, 1);
                // Fix Windows path...
                $baseurl .= str_replace("\\", "", $serverurl);
            } else {
                if ((substr($dirlocation, 1, 2) === ":\\") || (substr($dirlocation, 0, 1) === "/")) {
                    // Server root path
                    $baseurl = "file://" . str_replace("\\", "/", $dirlocation);
                } else {

                    $serverurl = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);

                    $baseurl = str_replace("\\", "/", $dirlocation);

                    $baseurl = str_replace($serverurl, "", $baseurl);
                    //$baseurl = dirname($_SERVER["HTTP_REFERER"])."/".$baseurl;
                    $baseurl = $protocol . $_SERVER["HTTP_HOST"] . $folder . "/" . $baseurl;
                }
            }
        } else {
            $baseurl = $sfl_basepath;
        }

        //Replace space with %20 for URL
        $baseurl = str_replace(" ", "%20", $baseurl);

        // Make sure it ends with front slash
        if (substr($baseurl, strlen($baseurl) - 1) !== "/") {
            $baseurl .= "/";
        }
        return $baseurl;
    }

    static function getFileFormatImagesLink(?string $fileName)
    {
        if ($fileName === false)
            return JURI::root() . 'components/com_oxfordsmsfiles/images/formats/_blank.png';

        $fileNameParts = explode('.', $fileName);
        $extension = end($fileNameParts);

        $filePath = JPATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'formats' . DIRECTORY_SEPARATOR . $extension . '.png';
        if (file_exists($filePath))
            return JURI::root() . 'components/com_oxfordsmsfiles/images/formats/' . $extension . '.png';
        else
            return JURI::root() . 'components/com_oxfordsmsfiles/images/formats/_blank.png';
    }

    static function getFileSizePP($filesize)
    {
        if (is_numeric($filesize)) {
            $decr = 1024;
            $step = 0;
            $prefix = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB');

            while (($filesize / $decr) > 0.9) {
                $filesize = $filesize / $decr;
                $step++;
            }
            return round($filesize, 2) . ' ' . $prefix[$step];
        } else {
            return 'NaN';
        }

    }


}

class SFLAjaxServlet
{

    static function getContent($action, $params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation, $sfl_file)
    {
        $retVal = "false";

        // We should alsways get directory through Ajax call, userlocation only at initial call
        $sfl_userlocation = "";

        switch ($action) {
            case "delete":
                //$retVal = "<div style=\"text-align: left\">";
                // Just send the information text back!
                $retVal = ModSimpleFileListerHelperv10::deleteFile($params, $sfl_file);
                //$retVal .= "</div>";
                break;

            case "next" || "prev" || "dir" || "sort":

                $retVal = "<div style=\"text-align: left\">";
                $retVal .= ModSimpleFileListerHelperv10::getDirContents($params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation);
                $retVal .= "</div>";
                break;

            default:
                $retVal = "Action missing";
                break;
        }

        $app = JFactory::getApplication();
        echo $retVal;
        $app->close();
    }

}

?>