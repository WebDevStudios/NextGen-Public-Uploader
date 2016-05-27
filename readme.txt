=== NextGEN Public Uploader ===
Contributors: WDS-Scott, williamsba1, rzen, webdevstudios, tw2113, JustinSainton
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3084056
Tags: nextgen public uploader,nextgen uploader,nextgen gallery,nextgen,gallery,image,upload,photo,picture,visitor,public,uploader
Requires at least: 4.0
Tested up to: 4.4.0
Stable tag: 1.9

NextGEN Public Uploader is an extension to NextGEN Gallery which allows frontend image uploads for your users.

== Description ==

The NextGEN Public Uploader plugin for WordPress allows users to upload images from the frontend of your website to a specified gallery in NextGEN. Upon upload the submitted image is marked as "excluded" and an email notification will be sent letting you know an image is waiting to be reviewed.

REMEMBER: Always backup your database!

= NextGEN Public Uploader is an extension of NextGEN Gallery =

[NextGEN Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/ "NextGEN Gallery")

Special thanks to Patrick McCoy for his help.

[Pluginize](https://pluginize.com/?utm_source=next-gen&utm_medium=text&utm_campaign=wporg) was launched in 2016 by [WebDevStudios](https://webdevstudios.com/) to promote, support, and house all of their [WordPress products](https://pluginize.com/shop/?utm_source=next-gen&utm_medium=text&utm_campaign=wporg). Pluginize is not only creating new products for WordPress all the time, but also provides [ongoing support and development for WordPress community favorites like CPTUI](https://wordpress.org/plugins/custom-post-type-ui/), [CMB2](https://wordpress.org/plugins/cmb2/), and more.

== Screenshots ==

1. Sample use in front-end (placed below gallery and in sidebar)

2. Excluded Images in gallery, uploaded by anonymous users

3. Plugin Settings

4. TinyMCE Integration

5. Shortcode Example

6. Upload Widget

== Installation ==

1. Upload the nextgen-public-uploader folder to the plugins directory in your WordPress or WPMU installation.

2. Activate NextGEN Public Uploader.

3. Drag the NextGEN Public Uploader widget to the desired sidebar or use the shortcode in your pages/posts.

View the plugin settings page for shortcode examples.

= For More Information Visit =

[NextGEN Public Uploader Homepage](http://webdevstudios.com/support/wordpress-plugins/nextgen-public-uploader/ "NextGEN Public Uploader")

REMEMBER: This plugin requires NextGEN Gallery in order to work.

== Frequently Asked Questions ==

= Will this plugin work without the NextGEN Gallery plugin? =

No, this plugin requires NextGEN Gallery in order to work.

= If you don't have it grab it here. =
[http://wordpress.org/extend/plugins/nextgen-gallery/](http://wordpress.org/extend/plugins/nextgen-gallery/ "NextGEN Gallery")

= Why am I getting the following error? =

"NextGEN Public Uploader requires NextGEN gallery in order to work. Please deactivate this plugin or activate NextGEN Gallery."

If you have installed NextGEN Gallery, please make sure that it is activated.

= Still Need Help? Please visit the NextGEN Public Uploader Support Forum =

[NextGEN Public Uploader Support](http://wordpress.org/support/plugin/nextgen-public-uploader)

== Changelog ==

= V1.8.1 - 11.14.2013 =
* Fix old php version bug with empty()

= V1.8 - 11.14.2013 =
* Converted widget to proper widget class extension.
* Better translation support, including changed textdomain to match WP3.7 translation changes.
* Translation pot file.

= V1.7 - 10.5.2012 =
* Confirmed Working: Tested with latest versions of NextGen and WordPress, everything works fine
* Moved settings menu: The menu now righfully resides as a sub-item of Gallery
* Updated settings: Dropped unnecessary options, updated all setings to use WordPress Settings API
* Security Updates: Added a couple more security measures for data sanitization

= V1.6.1 - 4.25.2011 =
* Security Patch (QuickFix): Adds random hash to images held for moderation. (Thanks to Linus-Neumann.de)

= V1.6 - 1.30.2010 =
* Updates: Added localization
* Updates: Displays gallery name in TinyMCE

= V1.5 - 12.7.2009 =
* New Feature: TinyMCE Button
* Bugfix: Widget Uploader
* Updates: Settings Page

= V1.4 - 11.5.2009 =
* New Feature: Image Description
* Updates: More options available via settings page
* Updates: Default Gallery Drop-down
* Updates: Added button to reset default values
* Updates: Edit more text areas from settings page
* Bugfix: Fixed bug when saving options

= V1.3 - 10.20.2009 =
* New Feature: Widget Uploader
* New Feature: Select which user level can upload
* Fixed: More than one form can be displayed
* Updates: More options available via settings page
* Updates: Readme.txt updated
* Updates: Check if NextGEN Gallery exists optimized
* Bugfix: Saving options with WPMU

= V1.2.2 - 10.7.2009 =
* New Feature: Ability to edit messages displayed

= V1.2.1 - 10.7.2009 =
* Bugfix: 404 File not found

= V1.2 - 10.7.2009 =
* Updates: Options page updated
* Updates: Readme.txt updated

= V1.1 - 10.5.2009 =
* Fixed: SVN repository

= V1.0 - 10.5.2009 =
* NextGEN Public Uploader is launched

== License ==

NextGEN Public Uploader is distributed under an open source license called the GNU General Public License, or GPL. The text of the license is distributed with every copy of this plugin.

== Upgrade Notice ==

* Completely different widget registration method in version 1.8. If you use the Widget uploader, you'll probably want to re-add and re-set the gallery.
* Translators. We switched to a new text-domain to align with the WordPress 3.7 translation changes. We also provided a pot file to use.

Before upgrading NextGEN Public Uploader please remember to backup your database and files.
