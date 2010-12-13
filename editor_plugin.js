(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('ooyala_video');
	
	tinymce.create('tinymce.plugins.ooyalavideoPlugin', {
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			ed.addCommand('mce_ooyalavideo', t._ooyalavideo, t);
			ed.addButton('ooyala_video',{
				title : 'ooyalavideo.desc', 
				cmd : 'mce_ooyalavideo',
				image : url + '/img/ooyalavideo-button.png'
			});
		},
		
		getInfo : function() {
			return {
				longname : 'Ooyala for Wordpress',
				author : 'David Searle;',
				authorurl : 'http://www.ooyala.com',
				infourl : 'http://www.ooyala.com/wordpress',
				version : '1.0'
			};
		},
		
		// Private methods
		_ooyalavideo : function() { // open a popup window
			ooyalavideo_insert();
			return true;
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ooyala_video', tinymce.plugins.ooyalavideoPlugin);
})();