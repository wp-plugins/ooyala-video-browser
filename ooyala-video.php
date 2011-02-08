<?php

/*
Plugin Name: Ooyala 2010
Plugin URI: http://www.ooyala.com/wordpressplugin/
Description: Easy Embedding of Ooyala Videos based off an Ooyala Account as defined in the preferences. <a href="options-general.php?page=ooyalavideo_options_page">Configure...</a>
Version: 1.3
License: GPL
Author: David Searle ported across from Stefan He&szlig's embedded video plugin;
Original Author URI: http://www.jovelstefan.de

Contact mail: wordpress@ooyala.com
*/


// prevent plugin from being used with wp versions under 2.5; otherwise do nothing!
global $wp_db_version;
if ( $wp_db_version >= 7558 ) {

// prevent file from being accessed directly

if ('ooyala-video.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not access this file directly. Thanks!');

define("EV_VERSION", 41);

// initiate options and variables
function ooyalavideo_initialize() {
	//add_option('ooyala_prefix', "Link To");
	add_option('ooyalavideo_space', "false");
	add_option('ooyalavideo_width', 425);
	add_option('ooyalavideo_small', "false");
	add_option('ooyalavideo_pluginlink', "true");
	add_option('ooyalavideo_version', EV_VERSION);
	//add_option('ooyalavideo_shownolink', "false");
	add_option('ooyalavideo_showinfeed', "true");
    update_option('ooyalavideo_version', EV_VERSION);
}

if ('true' == get_option('ooyalavideo_space')) {
	$ev_space = '&nbsp;';
} else {
	$ev_space = '';
}

//define("LINKTEXT", get_option('ooyala_prefix').$ev_space);
define("GENERAL_WIDTH", get_option('ooyalavideo_width'));

/***********************/

// format definitions
define("OOYALA_HEIGHT", floor(GENERAL_WIDTH*9/16));

// object targets and links
define("OOYALA_TARGET", "<script src=\"http://player.ooyala.com/player.js?width=".GENERAL_WIDTH."&height=".OOYALA_HEIGHT."&embedCode=###VID###\"></script><noscript><object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" id=\"ooyalaPlayer_7n2iz_gewtz7xi\" width=\"".GENERAL_WIDTH."\" height=\"".OOYALA_HEIGHT."\" codebase=\"http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab\"><param name=\"movie\" value=\"http://player.ooyala.com/player.swf?embedCode=###VID###&version=2\" /><param name=\"bgcolor\" value=\"#000000\" /><param name=\"allowScriptAccess\" value=\"always\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"flashvars\" value=\"embedType=noscriptObjectTag&embedCode=###VID###\" /><embed src=\"http://player.ooyala.com/player.swf?embedCode=###VID###&version=2\" bgcolor=\"#000000\" width=\"".GENERAL_WIDTH."\" height=\"".OOYALA_HEIGHT."\" name=\"ooyalaPlayer_7n2iz_gewtz7xi\" align=\"middle\" play=\"true\" loop=\"false\" allowscriptaccess=\"always\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" flashvars=\"&embedCode=###VID###\" pluginspage=\"http://www.adobe.com/go/getflashplayer\"></embed></object></noscript>");
define("OOYALA_LINK", "<a title=\"Ooyala\" href=\"http://www.ooyala.com/watch?v=###VID###\">Video ###TXT######THING###</a>");

// regular expressions
define("REGEXP_1", "/\[(ooyala|google|youtube|myvideo|clipfish|sevenload|revver|metacafe|yahoo|ifilm|myspace|brightcove|aniboom|vimeo|guba|dailymotion|garagetv|gamevideo|vsocial|veoh|gametrailers|local|video) ([[:graph:]]+) (nolink)\]/");
define("REGEXP_2", "/\[(ooyala|google|youtube|myvideo|clipfish|sevenload|revver|metacafe|yahoo|ifilm|myspace|brightcove|aniboom|vimeo|guba|dailymotion|garagetv|gamevideo|vsocial|veoh|gametrailers|local|video) ([[:graph:]]+) ([[:print:]]+)\]/");
define("REGEXP_3", "/\[(ooyala|google|youtube|myvideo|clipfish|sevenload|revver|metacafe|yahoo|ifilm|myspace|brightcove|aniboom|vimeo|guba|dailymotion|garagetv|gamevideo|vsocial|veoh|gametrailers|local|video) ([[:graph:]]+)\]/");

// logic
function ooyalavideo_plugin_callback($match) {
	$output = '';
	// insert plugin link
	//if ((!is_feed())&&('true' == get_option('ooyalavideo_pluginlink'))) $output .= '<small>'.__('embedded by','ooyalavideo').' <a href="http://wordpress.org/extend/plugins/ooyala-video-browser/" title="'.__('plugin page','ooyalavideo').'"><em>Embedded Video</em></a></small><br />';
	if (!is_feed()) {
		switch ($match[1]) {
			case "ooyala": $output .= OOYALA_TARGET; break;
			
			case "local":
				if (preg_match("%([[:print:]]+).(mov|qt|MOV|QT)$%", $match[2])) { $output .= LOCAL_QUICKTIME_TARGET; break; }
				elseif (preg_match("%([[:print:]]+).(wmv|mpg|mpeg|mpe|asf|asx|wax|wmv|wmx|avi|WMV|MPG|MPEG|MPE|ASF|ASX|WAX|WMV|WMX|AVI)$%", $match[2])) { $output .= LOCAL_TARGET; break; }
				elseif (preg_match("%([[:print:]]+).(swf|flv|SWF|FLV)$%", $match[2])) { $output .= LOCAL_FLASHPLAYER_TARGET; break; }
			case "video":
				if (preg_match("%([[:print:]]+).(mov|qt|MOV|QT)$%", $match[2])) { $output .= QUICKTIME_TARGET; break; }
				elseif (preg_match("%([[:print:]]+).(wmv|mpg|mpeg|mpe|asf|asx|wax|wmv|wmx|avi|WMV|MPG|MPEG|MPE|ASF|ASX|WAX|WMV|WMX|AVI)$%", $match[2])) { $output .= VIDEO_TARGET; break; }
				elseif (preg_match("%([[:print:]]+).(swf|flv|SWF|FLV)$%", $match[2])) { $output .= FLASHPLAYER_TARGET; break; }
			default: break;
		}
		if (get_option('ooyalavideo_shownolink')=='false') {
			if ($match[3] != "nolink") {
				$ev_small = get_option('ooyalavideo_small');
				if ('true' == $ev_small) $output .= "<small>";
				switch ($match[1]) {
					case "ooyala": $output .= OOYALA_LINK; break;
			S_LINK; break;
					case "local": $output .= LOCAL_LINK; break;
					case "video": $output .= VIDEO_LINK; break;
					default: break;
				}
				if ('true' == $ev_small) $output .= "</small>";
			}
		}
	} else if (get_option('ooyalavideo_showinfeed')=='true') $output .= __('[There is a video that cannot be displayed in this feed. ', 'ooyalavideo').'<a href="'.get_permalink().'">'.__('Visit the blog entry to see the video.]','ooyalavideo').'</a>';

	// postprocessing
	// first replace linktext
	$output = str_replace("###TXT###", LINKTEXT, $output);
	// special handling of Yahoo! Video IDs
	if ($match[1] == "yahoo") {
		$temp = explode(".", $match[2]);
		$match[2] = $temp[1];
		$output = str_replace("###YAHOO###", $temp[0], $output);
	}
	// replace video IDs and text
	$output = str_replace("###VID###", $match[2], $output);
	$output = str_replace("###THING###", $match[3], $output);
	// add HTML comment
	if (!is_feed()) $output .= "\n<!-- generated by WordPress plugin Ooyala Video -->\n";
	return ($output);
}

// actual plugin function
function ooyalavideo_plugin($content) {
	$output = preg_replace_callback(REGEXP_1, 'ooyalavideo_plugin_callback', $content);
    $output = preg_replace_callback(REGEXP_2, 'ooyalavideo_plugin_callback', $output);
    $output = preg_replace_callback(REGEXP_3, 'ooyalavideo_plugin_callback', $output);
	return ($output);
}

// required filters
add_filter('the_content', 'ooyalavideo_plugin');

//build admin interface
function ooyalavideo_option_page() {

global $wpdb, $table_prefix;

	if ( isset($_POST['ooyala_prefix']) ) {

		$errs = array();

		$temp = stripslashes($_POST['ooyala_prefix']);
		$ev_prefix = wp_kses($temp, array());

		update_option('ooyala_prefix', $ev_prefix);

		if (!empty($_POST['ooyalavideo_space'])) {
			update_option('ooyalavideo_space', "true");
		} else {
			update_option('ooyalavideo_space', "false");
		}

		if (!empty($_POST['ooyalavideo_small'])) {
			update_option('ooyalavideo_small', "true");
		} else {
			update_option('ooyalavideo_small', "false");
		}

		if (!empty($_POST['ooyalavideo_pluginlink'])) {
			update_option('ooyalavideo_pluginlink', "true");
		} else {
			update_option('ooyalavideo_pluginlink', "false");
		}

		if (!empty($_POST['ooyalavideo_shownolink'])) {
			update_option('ooyalavideo_shownolink', "true");
		} else {
			update_option('ooyalavideo_shownolink', "false");
		}

		if (!empty($_POST['ooyalavideo_showinfeed'])) {
			update_option('ooyalavideo_showinfeed', "true");
		} else {
			update_option('ooyalavideo_showinfeed', "false");
		}

		$ev_width = $_POST['ooyalavideo_width'];
		if ($ev_width == "") $errs[] = __('Object width must be set!','ooyalavideo');
		elseif (($ev_width>800)||($ev_width<250)||(!preg_match("/^[0-9]{3}$/", $ev_width))) $errs[] = __('Object width must be a number between 250 and 800!','ooyalavideo');
		else update_option('ooyalavideo_width', $ev_width);

		if ( empty($errs) ) {
			echo '<div id="message" class="updated fade"><p>'.__('Options updated!','ooyalavideo').'</p></div>';
		} else {
			echo '<div id="message" class="error fade"><ul>';
			foreach ( $errs as $name => $msg ) {
				echo '<li>'.wptexturize($msg).'</li>';
			}
			echo '</ul></div>';
		}
	}

	if ('true' == get_option('ooyalavideo_space')) {
		$ev_space = 'checked="true"';
	} else {
		$ev_space = '';
	}

	if ('true' == get_option('ooyalavideo_small')) {
		$ev_small = 'checked="true"';
	} else {
		$ev_small = '';
	}

	if ('true' == get_option('ooyalavideo_pluginlink')) {
		$ev_pluginlink = 'checked="true"';
	} else {
		$ev_pluginlink = '';
	}

	if ('true' == get_option('ooyalavideo_shownolink')) {
		$ev_shownolink = 'checked="true"';
	} else {
		$ev_shownolink = '';
	}

	if ('true' == get_option('ooyalavideo_showinfeed')) {
		$ev_showinfeed = 'checked="true"';
	} else {
		$ev_showinfeed = '';
	}
	?>

	<div style="width:75%;" class="wrap" id="ooyalavideo_options_panel">
	<h2><?php echo _e('Ooyala Video','ooyalavideo'); ?></h2>

	<a href="http://www.ooyala.com/"><img src="../wp-content/plugins/ooyala-video-browser/img/ooyala_72dpi_dark_sm.png" title="<?php echo _e('Ooyala Logo') ?>" alt="<?php echo _e('Ooyala Logo') ?>" align="right" /></a>

	<p><strong><?php echo _e('Edit the prefix of the linktext and the width of the embedded flash object!','ooyalavideo'); ?></strong><br /><?php echo _e('For detailed information see the','ooyalavideo'); ?> <a href="http://wordpress.org/extend/plugins/ooyala-video-browser/" title="<?php echo _e('plugin page','ooyalavideo'); ?>"><?php echo _e('plugin page','ooyalavideo'); ?></a>.</p>

	<div class="wrap">
		<form method="post">
			<div>
				<label for="ooyalavideo_shownolink" style="cursor: pointer;"><input type="checkbox" name="ooyalavideo_shownolink" id="ooyalavideo_shownolink" value="<?php echo get_option('ooyalavideo_shownolink') ?>" <?php echo $ev_shownolink; ?> /> <?php echo _e('Never show the video link (exception: feeds)','ooyalavideo'); ?></label><br />
				<label for="ooyalavideo_showinfeed" style="cursor: pointer;"><input type="checkbox" name="ooyalavideo_showinfeed" id="ooyalavideo_showinfeed" value="<?php echo get_option('ooyalavideo_showinfeed') ?>" <?php echo $ev_showinfeed; ?> /> <?php echo _e('In feed, show link to blog post (video embedding in feed not yet available)','ooyalavideo'); ?></label><br />
				<?php echo _e('Prefix:','ooyalavideo'); ?> <input type="text" value="<?php echo get_option('ooyala_prefix') ?>" name="ooyala_prefix" id="ooyala_prefix" /><br />
				<label for="ooyalavideo_space" style="cursor: pointer;"><input type="checkbox" name="ooyalavideo_space" id="ooyalavideo_space" value="<?php echo get_option('ooyalavideo_space') ?>" <?php echo $ev_space; ?> /> <?php echo _e('Following space character','ooyalavideo'); ?></label><br />
				<label for="ooyalavideo_small" style="cursor: pointer;"><input type="checkbox" name="ooyalavideo_small" id="ooyalavideo_small" value="<?php echo get_option('ooyalavideo_small') ?>" <?php echo $ev_small; ?> /> <?php echo _e('Use smaller font size for link','ooyalavideo'); ?></label><br />
				<?php echo _e('Video object width','ooyalavideo'); ?> (250-800):<input type="text" value="<?php echo get_option('ooyalavideo_width') ?>" name="ooyalavideo_width" id="ooyalavideo_width" size="5" maxlength="3" /><br />
				<br />
				<input type="submit"  id="ooyalavideo_update_options" value="<?php echo _e('Save settings','ooyalavideo'); ?> &raquo;" />
			</div>
		</form>
	</div>

	<h3><?php echo _e('Preview','ooyalavideo'); ?></h3>
	<div class="wrap">
	<p><?php echo _e('Your current settings produce the following output:','ooyalavideo'); ?></p>
	<!--<p><?php if ('true' == get_option('ooyalavideo_pluginlink')) echo '<small>'.__('embedded by','ooyalavideo').' <a href="http://www.ooyala.com/wordpress" title="Ooyala Wordpress Plugin"><em>Ooyala Video</em></a></small><br />'; ?>-->
    
    
    <script src="http://player.ooyala.com/player.js?width=<?php echo get_option('ooyalavideo_width'); ?>&height=<?php echo floor(get_option('ooyalavideo_width')*9/16); ?>&embedCode=80MGxjOsGWfvxNkJ7LbMwJP3iPmhaotj"></script><noscript><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="ooyalaPlayer_7n2iz_gewtz7xi" width="<?php echo get_option('ooyalavideo_width'); ?>" height="<?php echo floor(get_option('ooyalavideo_width')*14/17); ?>" codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab"><param name="movie" value="http://player.ooyala.com/player.swf?embedCode=80MGxjOsGWfvxNkJ7LbMwJP3iPmhaotj&version=2" /><param name="bgcolor" value="#000000" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="embedType=noscriptObjectTag&embedCode=80MGxjOsGWfvxNkJ7LbMwJP3iPmhaotj" /><embed src="http://player.ooyala.com/player.swf?embedCode=80MGxjOsGWfvxNkJ7LbMwJP3iPmhaotj&version=2" bgcolor="#000000" width="<?php echo get_option('ooyalavideo_width'); ?>" height="<?php echo floor(get_option('ooyalavideo_width')*14/17); ?>" name="ooyalaPlayer_7n2iz_gewtz7xi" align="middle" play="true" loop="false" allowscriptaccess="always" allowfullscreen="true" type="application/x-shockwave-flash" flashvars="&embedCode=80MGxjOsGWfvxNkJ7LbMwJP3iPmhaotj" pluginspage="http://www.adobe.com/go/getflashplayer"></embed></object></noscript><br />
	<?php if ('false' == get_option('ooyalavideo_shownolink')) { $ev_issmall = get_option('ooyalavideo_small'); if ('true' == $ev_issmall) echo "<small>"; ?>
	<a title="YouTube" href="http://www.ooyala.com/blog">Video <?php echo get_option('ooyala_prefix'); if ('true' == get_option('ooyalavideo_space')) echo "&nbsp;"; ?>Example Video</a><?php if ('true' == $ev_issmall) echo "</small>"; } ?>
	</p>
	</div>
		
	</p>
	</div>

	<?php
}

function ooyalavideo_add_options_panel() {
	add_options_page('Ooyala Video', 'Ooyala Video', 'manage_options', 'ooyalavideo_options_page', 'ooyalavideo_option_page');
}

function ooyala_video_mcebutton($buttons) {
	array_push($buttons, "|", "ooyala_video");
	return $buttons;
}

function ooyala_video_mceplugin($ext_plu) {
	if (is_array($ext_plu) == false) {
		$ext_plu = array();
	}
	$url = get_option('siteurl')."/wp-content/plugins/ooyala-video-browser/editor_plugin.js";
	$result = array_merge($ext_plu, array("ooyala_video" => $url));
	return $result;
}

function ooyalavideo_mceinit() {
	if (function_exists('load_plugin_textdomain')) load_plugin_textdomain('ooyalavideo','/wp-content/plugins/ooyala-video-browser/langs');
	if ( 'true' == get_user_option('rich_editing') ) {
		add_filter("mce_external_plugins", "ooyala_video_mceplugin", 0);
		add_filter("mce_buttons", "ooyala_video_mcebutton", 0);
	}
}

function ooyalavideo_script() {
	echo "<script type='text/javascript' src='".get_option('siteurl')."/wp-content/plugins/ooyala-video-browser/ooyala-video.js'></script>\n";
}

if ( function_exists('add_action') ) {
	if (( isset($_GET['activate']) && $_GET['activate'] == 'true' ) || ( get_option('ooyalavideo_version') <> EV_VERSION )) {
		add_action('init', 'ooyalavideo_initialize');
	}
	add_action('init', 'ooyalavideo_mceinit');
	add_action('admin_print_scripts', 'ooyalavideo_script');
	add_action('admin_menu', 'ooyalavideo_add_options_panel');
}

} // closing if for version check

?>