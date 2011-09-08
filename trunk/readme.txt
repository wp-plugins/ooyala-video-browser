=== Ooyala Video ===
Ported By: Dave Searle
Contributors: dsearle
Tags: embedding, video, embed, portal, ooyala, shortcode
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.4

Easy embedding of videos for the Ooyala Video Platform.
Browse your Ooyala's videos, and easily insert them into your posts and page.


== Description ==
Easy embedding of videos for the Ooyala Video Platform.  
Browse and search your Ooyala's videos, and easily insert them into your posts and page.  
Upload videos to your Ooyala account directly from within WordPress.  

== Installation ==

Copy the subfolder "ooyala-video" with all included files into the "wp-content/plugins" folder of WordPress. Activate the plugin and setup your Backlot pcode and secret code in the Ooyala Settings screen. You will find your pcode and secret code under Account -> Developers in Backlot.

== Screenshots ==

1. A new media upload button is available to launch the Ooyala Search and Insertion GUI.
2. The Ooyala GUI allows you to search and insert videos from your Ooyala account. You can search by keyword or choose the last 8 videos uploaded to the account.

== Changelog ==

= 1.4 =

**New Features:**

* You can now set a video thumbnail as your post featured image (if your theme supports featured images)
* The API codes can now be entered directly in the settings screen. If you already have config.php file set, the plugin will read the values from there.

**Bugfixes and Improvements:**

* The plugin uses a media screen instead of a tinmyce button, and the popup look more like WP's own media screens
* The shortcode has been converted to use WP's shortcode API. Old version compatibility is maintained.
* Use WordPress's content_width as default if it is set for the video width.
* JavaScript can now be localized as well.
* Switched OoyalaBacklotAPI to use WP's HTTP class

= 1.3 =
* Pulled config into config.php
* Added Upload functionality to second tab with example label config

= 1.2 =
* Added screenshots to package for SVN. No other changes made.

== Upgrade Notice ==

= 1.4 =
The plugin has been updated for WordPress 3.0 and newer.

== Configuration ==

The configuration options are simple and do not need explanation. See the administration panel of the plugin in your WP admin frontend.
