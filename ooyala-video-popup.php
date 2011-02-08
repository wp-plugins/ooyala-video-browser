<?php
//if (!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__).'/../../../');
//require_once('../../../wp-admin/admin.php');
include_once('./config.php');

if (!defined('ABSPATH')) include_once('./../../../wp-blog-header.php');
require_once(ABSPATH . '/wp-admin/admin.php');

if (isset($_POST['action'])) {

$mimes = is_array($mimes) ? $mimes : apply_filters('upload_mimes', array (
		'avi' => 'video/avi',
		'mov|qt' => 'video/quicktime',
		'mpeg|mpg|mpe' => 'video/mpeg',
		'asf|asx|wax|wmv|wmx' => 'video/asf',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv'
	));

$overrides = array('action'=>'save','mimes'=>$mimes);

$file = wp_handle_upload($_FILES['video'], $overrides);

if ( !isset($file['error']) ) {

	$url = $file['url'];
	$type = $file['type'];
	$file = $file['file'];
	$filename = basename($file);

	// Construct the attachment array
	$attachment = array(
		'post_title' => $_POST['videotitle'] ? $_POST['videotitle'] : $filename,
		'post_content' => $_POST['descr'],
		'post_status' => 'attachment',
		'post_parent' => $_GET['post'],
		'post_mime_type' => $type,
		'guid' => $url
		);

	// Save the data
	$id = wp_insert_attachment($attachment, $file, $post);

	if ( preg_match('!^image/!', $attachment['post_mime_type']) ) {
		// Generate the attachment's postmeta.
		$imagesize = getimagesize($file);
		$imagedata['width'] = $imagesize['0'];
		$imagedata['height'] = $imagesize['1'];
		list($uwidth, $uheight) = get_udims($imagedata['width'], $imagedata['height']);
		$imagedata['hwstring_small'] = "height='$uheight' width='$uwidth'";
		$imagedata['file'] = $file;

		add_post_meta($id, '_wp_attachment_metadata', $imagedata);

		if ( $imagedata['width'] * $imagedata['height'] < 3 * 1024 * 1024 ) {
			if ( $imagedata['width'] > 128 && $imagedata['width'] >= $imagedata['height'] * 4 / 3 )
				$thumb = wp_create_thumbnail($file, 128);
			elseif ( $imagedata['height'] > 96 )
				$thumb = wp_create_thumbnail($file, 96);

			if ( @file_exists($thumb) ) {
				$newdata = $imagedata;
				$newdata['thumb'] = basename($thumb);
				update_post_meta($id, '_wp_attachment_metadata', $newdata, $imagedata);
			} else {
				$error = $thumb;
			}
		}
	} else {
		add_post_meta($id, '_wp_attachment_metadata', array());
	}

	$_GET['tab'] = 'select';
  }

}

if (! current_user_can('edit_others_posts') ) {
	$and_user = "AND post_author = " . $user_ID;} else {$and_user="";}
$and_type = "AND (post_mime_type = 'video/avi' OR post_mime_type = 'video/quicktime' OR post_mime_type = 'video/mpeg' OR post_mime_type = 'video/asf' OR post_mime_type = 'video/x-flv' OR post_mime_type = 'application/x-shockwave-flash')";
if ( 3664 <= $wp_db_version )
  $attachments = $wpdb->get_results("SELECT post_title, guid FROM $wpdb->posts WHERE post_type = 'attachment' $and_type $and_user ORDER BY post_date_gmt DESC LIMIT 0, 10", ARRAY_A);
else
  $attachments = $wpdb->get_results("SELECT post_title, guid FROM $wpdb->posts WHERE post_status = 'attachment' $and_type $and_user ORDER BY post_date_gmt DESC LIMIT 0, 10", ARRAY_A);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="ooyala-video.js"></script>
	<base target="_self" />
	<style type="text/css">
		#ooyalavideo .panel_wrapper, #ooyalavideo div.current {
			height: 550px;
			padding-top: 5px;
		}
		
		#ResponseDiv {
			height: 400px;
			padding-top: 5px;
			font: 11px Verdana, Arial, Helvetica, sans-serif;
			text-align:center;
			border: 3px double;
					border-right-color: rgb(153, 153, 153);
					border-bottom-color: rgb(153, 153, 153);
					border-left-color: rgb(204, 204, 204);
					border-top-color: rgb(204, 204, 204);

		}
		
		#portal_insert, #portal_cancel, #select_insert, #select_cancel, #upload_insert, #upload_cancel, #remote_insert, #remote_cancel {
					font: 13px Verdana, Arial, Helvetica, sans-serif;
					height: auto;
					width: auto;
					background-color: transparent;
					background-image: url(../../../../../wp-admin/images/fade-butt.png);
					background-repeat: repeat;
					border: 3px double;
					border-right-color: rgb(153, 153, 153);
					border-bottom-color: rgb(153, 153, 153);
					border-left-color: rgb(204, 204, 204);
					border-top-color: rgb(204, 204, 204);
					color: rgb(51, 51, 51);
					padding: 0.25em 0.75em;
		}
		#portal_insert:active, #portal_cancel:active, #select_insert:active, #select_cancel:active, #upload_insert:active, #upload_cancel:active, #remote_insert:active, #remote_cancel:active {
					background: #f4f4f4;
					border-left-color: #999;
					border-top-color: #999;
		}
		
		#pager {
			position: fixed;
			bottom: 0;
			right: 10px;
			height: 30px;
			width: 140px;
		}
		
		.figure {
			display: inline-block;
			vertical-align: top
			border: 1px solid #666;
			width: 160px;
			margin: 0px 10px 10px 10px;
		}
		
		.figure p{
		font: 1em/normal Arial, Helvetica, sans-serif;
		text-align: center;
    	margin: 10px 0 0 0;
		height: 5em;
		}
		
		.photo img {
		border: 1px solid #666;
		background-color: #FFF;
		padding: 4px;
		position: relative;
		top: -5px;
		left: -5px;
		}
		
		.photo {
		 background: url(drop_shadow.gif) right bottom no-repeat;	
		}
	</style>
	<title><?php echo _e('Embed Ooyala Video','ooyalavideo'); ?></title>
</head>

<body id="ooyalavideo" onload="<?php $tab = (isset($_GET['tab'])) ? $_GET['tab'] : $_POST['tab']; echo "mcTabs.displayTab('".$tab."_tab','".$tab."_panel');"; if ($_GET['tab']=='portal') echo "document.forms.portal_form.vid.style.backgroundColor = '#f30';" ?>tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">

  <div class="tabs">
    <ul>
      <li id="portal_tab" class="current"><span><a href="javascript:mcTabs.displayTab('portal_tab','portal_panel');" onmousedown="return false;"><?php echo _e('Ooyala video','ooyalavideo'); ?></a></span></li>
      <?php if ($attachments) { ?><li id="select_tab"><span><a href="javascript:mcTabs.displayTab('select_tab','select_panel');" onmousedown="return false;"><?php echo _e('Local video','ooyalavideo'); ?></a></span></li><?php } ?>
      <li id="upload_tab"><span><a href="javascript:mcTabs.displayTab('upload_tab','upload_panel');" onmousedown="return false;"><?php echo _e('Upload to Ooyala','ooyalavideo'); ?></a></span></li>
      <!--<li id="remote_tab"><span><a href="javascript:mcTabs.displayTab('remote_tab','remote_panel');" onmousedown="return false;"><?php echo _e('Video URL','ooyalavideo'); ?></a></span></li>-->
    </ul>
  </div>

<div class="panel_wrapper">

  <div id="portal_panel" class="current">
    <form name="portal_form" action="#">
    <div id="ooyalabuttons">
    Search Keyword:<input name="ooyalasearch" type="text" id="ooyala_search" value="" style="width: 200px" /> <input type='button' onclick='SearchRequest(this.form.ooyalasearch.value);' value='Search'/>
    <input type='button' onclick='MakeRequest("last_eight");' value='Last 8 Videos'/>
    </div>
    <div id='ResponseDiv'>
      Please Search by Keyword or choose the last 8 videos....
      </div>
        <table border="0" cellpadding="4" cellspacing="0">
          
           <tr>
            <td nowrap="nowrap" style="text-align:right;"><?php echo _e('Insert video ID:','ooyalavideo'); ?></td>
            <td>
              <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><input name="vid" type="text" id="portal_vid" value="" style="width: 200px" /></td>
                </tr>
              </table></td>
          </tr>
          <tr>
            <td>
	    <input type="submit" id="portal_insert" name="insert" value="<?php echo _e('Insert','ooyalavideo'); ?>" onclick="ev_checkData(this.form);" />
            </td>
            <td align="right"><input type="button" id="portal_cancel" name="cancel" value="<?php echo _e('Cancel','ooyalavideo'); ?>" onclick="tinyMCEPopup.close();" /></td>
          </tr>
        </table>
      <input type="hidden" name="tab" value="portal" />
    </form>
  </div>
  
   <div id="upload_panel" class="panel">
    
<?php
class OoyalaPartnerAPI 
{ 
  static function signed_params($params) 
  { 
    if (!array_key_exists('expires', $params)) { 
      $params['expires'] = time() + 900;  // 15 minutes 
    } 
 
    $string_to_sign = OOYALA_SECRET_CODE; 
    $param_string = 'pcode='.OOYALA_PARTNER_CODE; 
    $keys = array_keys($params); 
    sort($keys); 
 
    foreach ($keys as $key) { 
      $string_to_sign .= $key.'='.$params[$key]; 
      $param_string .= '&'.rawurlencode($key).'='.rawurlencode($params[$key]); 
    } 
      
    $digest = hash('sha256', $string_to_sign, true); 
    $signature = ereg_replace('=+$', '', trim(base64_encode($digest))); 
    return $param_string.'&signature='.rawurlencode($signature); 
  } 
} 
 
// Define any default labels to assign and the dynamic label prefix  
// for any user-selected dynamic labels 
  
$param_string = OoyalaPartnerAPI::signed_params(array( 
  'status' => 'pending',  
  'dynamic[0]' => '^/',
  'dynamic[1]' => '^/',
  'dynamic[2]' => '^/area/',
  'dynamic[3]' => '^/area/', 
  ));
?> 
  <script type="text/javascript"> 
      function onFileSelected(file) 
      { 
        document.getElementById('file_name').value = file.name; 
      } 
      function onProgress(event) 
      { 
        document.getElementById('status').innerHTML = (parseInt(event.ratio * 10000) / 100) + '%';  
      } 
      function onUploadComplete() 
      { 
        document.getElementById('status').innerHTML = 'Done!'; 
      } 
      function onUploadError(text) 
      { 
        document.getElementById('status').innerHTML = 'Upload Error: ' + text; 
      } 
      function onEmbedCodeReady(embedCode) 
      { 
        // Not used 
        // document.getElementById('embedCode').innerHTML = embedCode; 
      } 
 
      function onOoyalaUploaderReady()  
      { 
        try 
        { 
          ooyalaUploader.setParameters('<?php print $param_string ?>'); 
        } 
        catch(e) 
        { 
          alert(e); 
        } 
        ooyalaUploader.addEventListener('fileSelected', 'onFileSelected');  
        ooyalaUploader.addEventListener('progress', 'onProgress');  
        ooyalaUploader.addEventListener('complete', 'onUploadComplete');  
        ooyalaUploader.addEventListener('error', 'onUploadError');  
        ooyalaUploader.addEventListener('embedCodeReady', 'onEmbedCodeReady');  
          
        document.getElementById('uploadButton').disabled = false;  
      } 
  
      function startUpload() 
      { 
        try 
        { 
          ooyalaUploader.setTitle(document.getElementById('file_name').value); 
          ooyalaUploader.setDescription(document.getElementById('description').value); 

          if (document.getElementById('dynamic_label').value) 
          { 
            ooyalaUploader.addDynamicLabel('/' + document.getElementById('dynamic_label').value);
            ooyalaUploader.addDynamicLabel('/' + document.getElementById('dynamic_label2').value);
            ooyalaUploader.addDynamicLabel('/area/' + document.getElementById('dynamic_label3').value);
            ooyalaUploader.addDynamicLabel('/area/' + document.getElementById('dynamic_label4').value);
             
          } 
          var errorText = ooyalaUploader.validate();  

          if (errorText)  
          {  
            alert(errorText);  
            return false;  
          }  
          ooyalaUploader.upload(); 
        }  
        catch(e)  
        {  
          alert(e); 
        } 
        return false; 
      } 
    </script> 

<style type="text/css" media="screen">
	
	
	 
	label {
		font-size: 14px;
		font-weight: bold;	
		display: gray;
		margin-bottom: 1px;
		clear: both;
		width: 10em;
		float: left;
		text-align: right;
		margin-right: 0.5em;
		}
	
	fieldset {		
		width: 750px;
		background: white;
		border: 5px solid #999;
		}
	
	</style>
 <fieldset width="700px">
   <center><img src="img/ooyala_72dpi_dark_sm.png" /></center>
	<p>

  
<label>Filename</label><p><script src="http://www.ooyala.com/partner/uploadButton?width=100&height=20&label=Browse%20Button"></script> <p>
     <label></label><textarea id="file_name" cols="40"></textarea> <p>
    
<label>Description</label>
        <textarea id="description" rows="5" cols="40"></textarea><p>
<label>Syndicate</label>
          <select id="dynamic_label2" name="dynamic_label2" /> 
            <option value="">None</option>
            <option value="YouTube">YouTube</option>
            <option value="Boxee">Boxee</option>
            <option value="Roku">Roku</option>
          </select> <p>
          
<label>Category</label>
<select id="dynamic_label" name="dynamic_label" /> 
  <option value="">None</option>
  <option value="News">News</option>
  <option value="Sport">Sport</option>
  <option value="Business">Business</option>
  <option value="Culture">Culture</option>
</select> <p>


<label> </label> <input type="checkbox" id="dynamic_label4" name="dynamic_label4" value="Public" />Public<br />
<label> </label> <input type="checkbox" id="dynamic_label3" name="dynamic_label3" value="Private" />Private<p>




<label> </label>  <button id="uploadButton" onClick="return startUpload();">Upload!</button>  <a id="status"></a>
    
    
    </fieldset>
  </div>
</div>
</body>
</html>
