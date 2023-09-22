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

$document = JFactory::getDocument();
$document->addCustomTag('<link href="/components/com_oxfordsmsfiles/css/mod_simplefilelister.css" type="text/css" rel="stylesheet" />');

$loaderImagePath = 'components/com_oxfordsmsfiles/images/ajax-loader.gif';
?>

    <script>

        var curPageURL = window.location.href;
        if (curPageURL.indexOf(".php?") > 0) {
            curPageURL += "&";
        } else {
            curPageURL += "?";
        }
        var curBrowseDir = "<?php echo $sfl_dirlocation; ?>";

        (function (jQuery) {
// wait till the DOM is loaded

            jQuery(document).ready(function () {

                jQuery('#div_sflcontent').on('click', '#sfl_ARefresh', function () {

                    var params = '&sflDir=' + curBrowseDir;

                    jQuery('#div_sflcontent').css('text-align', 'center');
                    jQuery('#div_sflcontent').html('')
                        .append('<img style="position: relative; top: 50px;" src="<?php echo $loaderImagePath; ?>" />')
                        .fadeIn(700, function () {
                            //$('#div_sflcontent').append("DONE!");
                        });

                    jQuery.ajax({
                        type: 'GET',
                        url: curPageURL,
                        data: 'sflaction=dir' + params,
                        cache: false,
                        success: function (data) {
                            jQuery('#div_sflcontent').css('text-align', 'left');
                            jQuery('#div_sflcontent').html('').append(data);
                        }
                    });

                    return false;

                });
//sfl_btnBrowseDir
//sfl_item
                jQuery('#div_sflcontent').on('click', '.sfl_btnBrowseDir', function () {

                    var dir = this.rel;

                    var params = '&sflDir=' + dir;
                    curBrowseDir = dir;

                    jQuery('#div_sflcontent').css('text-align', 'center');
                    jQuery('#div_sflcontent').html('')
                        .append('<img style="position: relative; top: 50px;" src="<?php echo $loaderImagePath; ?>" />')
                        .fadeIn(700, function () {
                            //$('#div_sflcontent').append("DONE!");
                        });

                    jQuery.ajax({
                        type: 'GET',
                        url: curPageURL,
                        data: 'sflaction=dir' + params,
                        cache: false,
                        success: function (data) {
                            jQuery('#div_sflcontent').css('text-align', 'left');
                            jQuery('#div_sflcontent').html('').append(data);
                        }
                    });

                    return false;

                });

                <?php if ($sfl_allowdelete === "1") { ?>

                jQuery('.sfl_btnDelete').live('click', function () {

                    var del = this.rel;
                    var params = '&sflDelete=' + del;

                    var msg = del.split('**');

                    if (confirm('<?php echo JText::_('DELETE_MSG'); ?>\n(' + msg[1] + ')')) {

                        jQuery('#div_sflcontent').css('text-align', 'center');
                        jQuery('#div_sflcontent').html('')
                            .append('<img style="position: relative; top: 50px;" src="<?php echo $loaderImagePath; ?>" />')
                            .fadeIn(700, function () {
                                //$('#div_sflcontent').append("DONE!");
                            });

                        jQuery.ajax({
                            type: 'GET',
                            url: curPageURL,
                            data: 'sflaction=delete' + params,
                            cache: false,
                            success: function (data) {
                                alert(data);
                                //$('#div_sflcontent').html('').append(data);

                                params = '&sflDir=' + curBrowseDir;
                                jQuery.ajax({
                                    type: 'GET',
                                    url: curPageURL,
                                    data: 'sflaction=dir' + params,
                                    cache: false,
                                    success: function (data) {
                                        jQuery('#div_sflcontent').css('text-align', 'left');
                                        jQuery('#div_sflcontent').html('').append(data);
                                    }
                                });
                            }
                        });

                    }
                    return false;

                });
                <?php } ?>

//FIX IT!
                jQuery('#sfl_ASortDesc').on('click', '#sfl_ASortDesc', function () {
                    var params = '&sflSort=desc&sflDir=' + curBrowseDir;

                    if (document.getElementById("sflSortDesc").className == "") return false;

                    document.getElementById("sflSortAsc").className = "sfl_shadow";
                    document.getElementById("sflSortDesc").className = "";

                    jQuery('#div_sflcontent').css('text-align', 'center');

                    jQuery('#div_sflcontent').html('')
                        .append('<img style="position: relative; top: 50px;" src="<?php echo $loaderImagePath; ?>" />')
                        .fadeIn(700, function () {
                            //$('#div_sflcontent').append("DONE!");
                        });

                    jQuery.ajax({
                        type: 'GET',
                        url: curPageURL,
                        data: 'sflaction=sort' + params,
                        cache: false,
                        success: function (data) {
                            jQuery('#div_sflcontent').css('text-align', 'left');
                            jQuery('#div_sflcontent').html('').append(data);
                        }
                    });
                    return false;

                });

//FIX IT
                jQuery('#sfl_ASortAsc').on('click', '#sfl_ASortAsc', function () {
                    var params = '&sflSort=asc&sflDir=' + curBrowseDir;

                    if (document.getElementById("sflSortAsc").className == "") return false;

                    document.getElementById("sflSortAsc").className = "";
                    document.getElementById("sflSortDesc").className = "sfl_shadow";

                    jQuery('#div_sflcontent').css('text-align', 'center');

                    jQuery('#div_sflcontent').html('')
                        .append('<img style="position: relative; top: 50px;" src="<?php echo $loaderImagePath; ?>" />')
                        .fadeIn(700, function () {
                            //$('#div_sflcontent').append("DONE!");
                        });

                    jQuery.ajax({
                        type: 'GET',
                        url: curPageURL,
                        data: 'sflaction=sort' + params,
                        cache: false,
                        success: function (data) {
                            jQuery('#div_sflcontent').css('text-align', 'left');
                            jQuery('#div_sflcontent').html('').append(data);
                        }
                    });
                    return false;

                });

//FIX IT
                jQuery('#sfl_btnNext').on('click', '#sfl_btnNext', function () {

                    var nextVal = document.getElementById('sflNextVal').value;
                    var params = '&sflNext=' + nextVal + '&sflDir=' + curBrowseDir;

                    jQuery('#div_sflcontent').css('text-align', 'center');

                    jQuery('#div_sflcontent').html('')
                        .append('<img style="position: relative; top: 50px;" src="<?php echo $loaderImagePath; ?>" />')
                        .fadeIn(700, function () {
                            //$('#div_sflcontent').append("DONE!");
                        });

                    jQuery.ajax({
                        type: 'GET',
                        url: curPageURL,
                        data: 'sflaction=next' + params,
                        cache: false,
                        success: function (data) {
                            jQuery('#div_sflcontent').css('text-align', 'left');
                            jQuery('#div_sflcontent').html('').append(data);
                        }
                    });
                    return false;

                });

//Fix it
                jQuery('#sfl_btnPrev').on('click', '#sfl_btnPrev', function () {

                    var params = '';

                    var prevVal = document.getElementById('sflPrevVal').value;
                    if (prevVal + 0 > -1) params = '&sflPrevious=' + prevVal + '&sflDir=' + curBrowseDir;

                    jQuery('#div_sflcontent').css('text-align', 'center');
                    jQuery('#div_sflcontent').html('')
                        .append('<img style="position: relative; top: 50px;" src="<?php echo $loaderImagePath; ?>" />')
                        .fadeIn(700, function () {
                            //$('#div_sflcontent').append("DONE!");
                        });

                    jQuery.ajax({
                        type: 'GET',
                        url: curPageURL,
                        data: 'sflaction=prev' + params,
                        cache: false,
                        success: function (data) {
                            jQuery('#div_sflcontent').html('').append(data);
                        }
                    });
                    return false;

                });

            });
        })(jQuery);

    </script>

    <style>
        .sfldel:hover {
            border: solid 1px #CCC;
            -moz-box-shadow: 1px 1px 5px #999;
            -webkit-box-shadow: 1px 1px 5px #999;
            box-shadow: 1px 1px 5px #999;
        }

        .sfldel {
            height: 12px;
            position: relative;
            top: 2px;
        }
    </style>
<?php

if ($sfl_maxheight > 0) {
    // We're gonna have a fixed height DIV
    ?>

    <div id="div_sflwrapper"
         style="position: relative; height: <?php echo $sfl_maxheight; ?>px; overflow: auto; background: <?php echo $sfl_bgcolor ?>;">

        <?php
        }
        ?>

        <div id="div_sflcontent" class="sfl_content"
             style="background: <?php echo $sfl_bgcolor ?>; left: <?php echo $sfl_boxleft ?>px;">
            <span style="display: none"><a id="sfl_ARefresh" class="sfl_ARefresh" href="javascript:void(0);">Refresh</a></span>
            <?php echo $results; ?>
        </div>

<?php

if ($sfl_maxheight > 0) echo "</div>";
?>