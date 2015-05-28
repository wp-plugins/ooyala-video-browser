<!-- Plugin title bar -->
<script type="text/html" id="tmpl-ooyala-title-bar">
	<h1 class="ooyala-title">Ooyala</h1>

	<div class="ooyala-title-links">
		<a class="ooyala-title-link ooyala-browse-link ooyala-browsing"><?php esc_html_e( "Back to Browse", 'ooyala' ); ?></a>
		<a class="ooyala-upload-toggle ooyala-title-link"><?php esc_html_e( "Upload", 'ooyala' ); ?></a>
		<a class="ooyala-title-link ooyala-about-link"><?php esc_html_e( "About", 'ooyala' ); ?></a>
		<a class="ooyala-title-link ooyala-privacy-link" target="_ooyala" href="http://www.ooyala.com/privacy"><?php esc_html_e( "Privacy Policy", 'ooyala' ); ?></a>
	</div>
</script>

<!-- About panel -->
<script type="text/html" id="tmpl-ooyala-about-text">
	<a class="ooyala-close ooyala-close-x"></a>

<?php
	/* TODO: Localize this text. */
	include( __DIR__ . '/ooyala-about-en-us.html' );
?>

	<p style="text-align: right">
		<a class="ooyala-close" href="#"><?php esc_html_e( "Close", 'ooyala' ); ?></a>
	</p>
</script>

<!-- Main attachments browser -->
<script type="text/html" id="tmpl-ooyala-attachments-browser">
<div class="ooyala-browser-container">
	<table class="ooyala-browser-flex-container">
		<tbody>
			<tr>
				<td class="ooyala-search-toolbar"></td>
			</tr>
			<tr>
				<td class="ooyala-browser-container">
					<div class="ooyala-browser">
						<div class="ooyala-results"></div>
						<div class="ooyala-search-spinner"></div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="ooyala-sidebar-container">
	<div class="ooyala-sidebar">
	</div>
</div>
</script>

<!-- Single attachment -->
<script type="text/html" id="tmpl-ooyala-attachment">
<# var classes = [];
	classes.push('type-' + data.asset_type);
	#>
	<div class="attachment-preview js--select-attachment ooyala-attachment {{ classes.join(' ') }}">
		<#  // if the status is uploading and WE are actually uploading it right now (will have a percent field)
			// i.e. assets can have the status of uploading if the upload was started and abandoned (or still in progress elswhere)
			if ( data.status === 'uploading' && 'percent' in data ) { #>
			<div class="thumbnail"><div class="media-progress-bar"><div></div></div></div>
		<# } else { #>
			<div class="thumbnail">
				<div class="centered">
				<# if (data.preview_image_url) { #>
					<img src="{{ data.preview_image_url }}" draggable="false" />
				<# } #>
				</div>
			</div>
		<# } #>
			<div class="asset-details">
				<span class="asset-name">{{ data.name }}</span>
			</div>

		<# if ( data.buttons.close ) { #>
			<a class="close media-modal-icon" href="#" title="<?php _e('Remove'); ?>"></a>
		<# } #>

		<# if ( data.buttons.check ) { #>
			<a class="check" href="#" title="<?php _e('Deselect'); ?>"><div class="media-modal-icon"></div></a>
		<# } #>
	</div>
</script>

<!-- Main sidebar details for single attachment -->
<script type="text/html" id="tmpl-ooyala-details">
<div class="ooyala-image-details">
	<div class="thumbnail">
		<# if(data.preview_image_url) { #>
			<img src="{{ data.preview_image_url }}" class="icon" draggable="false" />
		<# } #>
	</div>
</div>
<dl class="ooyala-image-details-list">

	<dt class="ooyala-title"><?php _e( "Title: ", 'ooyala' ); ?></dt>
	<dd class="ooyala-title">{{ data.name }}</dd>

	<# if (data.duration) { #>
	<dt class="ooyala-duration"><?php _e( "Duration: ", 'ooyala' ); ?></dt>
	<dd class="ooyala-duration">{{ data.duration_string }}</dd>
	<# } #>

	<dt class="ooyala-status"><?php _e( "Status: ", 'ooyala' ); ?></dt>
	<dd class="ooyala-status ooyala-status-{{ data.status }} {{ data.status == 'processing' ? 'loading' : '' }}">{{ data.status }}
	<# if (data.status=='uploading' && data.percent !== undefined) { #>
		<em class="progress">(<span>{{ data.percent }}</span>%)</em>
	<# } #>
	</dd>

	<# if ( data.description ) { #>
	<dt class="ooyala-description"><?php _e( "Description: ", 'ooyala' ); ?></dt>
	<#  if ( data.description.length > ( data.descriptionMaxLen + data.maxLenThreshold ) ) {
			var trunc = data.description.lastIndexOf(" ", data.descriptionMaxLen);
			if (trunc==-1) trunc = data.descriptionMaxLen;
			#>
	<dd class="ooyala-description">{{ data.description.slice(0,trunc) }}<span class="more">{{ data.description.slice(trunc) }}</span> <a href="#" class="show-more">(show&nbsp;more)</a></dd>
		<# } else { #>
	<dd class="ooyala-description">{{ data.description }}</dd>
		<# }
	 } #>

	<# if(data.labels && data.labels.length > 0) {
	#>
	<dt class="ooyala-labels"><?php _e( "Labels: ", 'ooyala' ); ?></dt>
	<dd class="ooyala-labels">
		<ul>
		<# for(var i = 0; i < data.labels.length; i++) { #>
			<li class="ooyala-label"><a href="#label-{{ data.labels[i].id }}" title="Click to refine your search by this label">{{ data.labels[i].name }}</a></li>
		<# } #>
		</ul>
	</dd>
	<# }
#>
</dl>
</script>

<!-- Player display options -->
<script type="text/html" id="tmpl-ooyala-display-settings">
<h3><?php esc_html_e( "Player Display Settings", 'ooyala' ); ?></h3>

<div class="ooyala-display-settings-wrapper {{ (data.model.forceEmbed || data.model.attachment.canEmbed()) ? '' : 'embed-warning' }}">
<div class="message"><?php esc_html_e( 'This asset may not display correctly due to its current status. Do you wish to embed it anyway?', 'ooyala' ); ?><a href="#">Show Player Settings</a></div>
<label class="setting">
	<span><?php _e( 'Player', 'ooyala' ); ?></span>
	<# if ( data.players.isFetching ) { #>
		<em class="loading"><?php _e( 'Retrieving players', 'ooyala' ); ?></em>
	<# } else { #>
		<select data-setting="player_id">
			<option value=""><?php esc_html_e( 'Default', 'ooyala' ); ?></option>
		<# data.players.each( function(item) { #>
			<option value="{{ item.get('id') }}">{{ item.get('name') }}</option>
		<# }); #>
		</select>
	<# } #>
</label>

<label class="setting">
	<span><?php _e( 'Platform', 'ooyala' ); ?></span>
	<select data-setting="platform">
		<option value=""><?php _e( 'Default', 'ooyala' ); ?></option>
		<# _.each(['flash','flash-only','html5-fallback','html5-priority'], function(value) { #>
			<option value="{{ value }}">{{ value }}</option>
		<# }); #>
	</select>
</label>

<div class="setting resolution">
	<span><?php _e( 'Size', 'ooyala' ); ?></span>
	<# if (data.model.attachment.get('downloadingResolutions')) { #>
		<em class="loading"><?php _e( 'Retrieving video resolutions', 'ooyala' ); ?></em>
	<# } else { #>
		<select data-setting="resolution">
		<# var resolutions = data.model.attachment.get('resolutions');
		if (resolutions && resolutions.length > 0) {
			for (var i=0; i < resolutions.length; i++) {
				var res = resolutions[i].join(' x ') #>
			<option value="{{ res }}">{{ res }}</option>
			<# }
		} #>
			<option value="custom"><?php _e( 'Custom', 'ooyala' ); ?></option>
		</select>
		<div class="custom-resolution">
			<input type="text" data-setting="width"/>
			X
			<input type="text" data-setting="height"/>
			<label><input type="checkbox" data-setting="lockAspectRatio"> <?php _e( 'Maintain aspect ratio', 'ooyala' ); ?></label>
		</div>
	<# } #>
</div>

<label class="setting">
	<span><?php _e( 'Enable Channels', 'ooyala' ); ?></span>
	<input type="checkbox" data-setting="enable_channels"/>
</label>

<label class="setting initial-time">
	<span><?php _e( 'Initial Time', 'ooyala' ); ?></span>
	<input type="text" data-setting="initial_time" min="0" max="{{ data.model.attachment.get('duration') / 1000 }}"> <?php _e( 'sec', 'ooyala' ); ?>
</label>

<label class="setting">
	<span><?php _e( 'Locale', 'ooyala' ); ?></span>
	<select data-setting="locale">
		<option value=''>User Default</option>
	<?php
	$locales = array(
		'zh_CN' => 'Chinese (Simplified)', /* need to verify these */
		'zh_TW' => 'Chinese (Traditional)',
		'en' => 'English',
		'fr' => 'French',
		'de' => 'German',
		'it' => 'Italian',
		'ja' => 'Japanese',
		'pl' => 'Polish',
		'pt' => 'Portuguese',
		'ru' => 'Russian',
		'es' => 'Spanish',
	);
	foreach ( $locales as $code => $label ) { ?>
		<option value="<?php esc_attr_e( $code ); ?>"><?php esc_html_e( $label, 'ooyala' ); ?></option>
<?php } ?>
	</select>
</label>

<label class="setting additional-parameters">
	<span><?php _e( 'Additional Player Parameters', 'ooyala' ); ?></span>
	<em class="error-message"><?php _e( 'There is an error in your syntax:', 'ooyala' ); ?></em>
	<textarea data-setting="additional_params_raw" placeholder="Key/value pairs in JSON or JavaScript object literal notation">{{ data.model.additional_params }}</textarea>
</label>
</div>
</script>

<!-- The square "More" button -->
<script type="text/html" id="tmpl-ooyala-more">
	<div class="attachment-preview">
		<div class="ooyala-more-spinner">
		</div>
		<div class="ooyala-more-text-container">
			<!--// <span class="ooyala-number-remaining"></span> //-->
			<span class="ooyala-more-text"><?php _e( "More", 'ooyala' ); ?></span>
		</div>
	</div>
</script>

<!-- Unsupported browser message -->
<script type="text/html" id="tmpl-ooyala-unsupported-browser">
	<h1><?php _e( "Sorry, this browser is unsupported!", 'ooyala' ); ?></h1>

	<p><?php _e( "The Ooyala plugin requires at least Internet Explorer 10 to function. This plugin also supports other modern browsers with proper CORS support such as Firefox, Chrome, Safari, and Opera.", 'ooyala' ); ?></p>
</script>

<!-- Asset upload panel -->
<script type="text/html" id="tmpl-ooyala-upload-panel">
	<a class="ooyala-close ooyala-close-x"></a>
	<# if ( data.controller.uploader.files.length ) {
		var file = data.controller.uploader.files[0];
		var isUploading = data.controller.uploader.state === ooyala.plupload.STARTED;
		#>
		<div class="file-name">File: {{ file.name }} <em class="file-size">({{ new Number( file.size ).bytesToString() }})</em>
		<# if( !isUploading ) { #>
			<a class="button ooyala-upload-browser" tabindex="10">Change</a>
		<# } #>
		</div>
		<label class="setting">Title<input type="text" value="{{ file.model.get('name') }}" data-setting="name" tabindex="20"></label>
		<label class="setting">Description<textarea data-setting="description" tabindex="30">{{ file.model.get('description') }}</textarea></label>
		<label class="setting">Post-processing Status
		<select data-setting="futureStatus" tabindex="40">
		<# var status = ['live','paused'];
			for( var i = 0; i < status.length; i++) { #>
				<option value="{{ status[i] }}" {{{ status[i] == file.model.get('futureStatus') ? ' selected="selected"' : '' }}}>{{ status[i] }}</option>
		<# } #>
		</select></label>
		<div class="ooyala-upload-controls {{ isUploading ? 'uploading' : '' }}">
			<div class="progress"><span>{{ ( file.model.asset && file.model.asset.get('percent') ) || 0 }}</span>%</div>
			<a class="button ooyala-stop-upload" tabindex="60">Cancel Upload</a>
			<a class="button ooyala-start-upload" tabindex="50">Start Upload</a>
		</div>
	<# } else { #>
		<div class="ooyala-upload-browser-container">
			<h4>Upload an asset to your account.</h4>
		<a class="button button-hero ooyala-upload-browser">Select File</a>
		</div>
	<# } #>
</script>

<!-- Current label refinement for search secondary toolbar -->
<script type="text/html" id="tmpl-ooyala-label-search">
	<?php esc_html_e( 'Refining by Label:', 'ooyala' ); ?>
	<span class="ooyala-selected-label"></span>
	<a href="#" title="Clear Label" class="ooyala-clear-label dashicons dashicons-dismiss"></a>
</script>