=== CogWork ===
Contributors: Erik Terenius
Tags: CogWork, MinaAktiviteter, Dans.se, shop, events, booking, registration, payment, customers, members
Requires at least: 4.6
Tested up to: 6.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables shortcodes that includes resourses from CogWork (MinaAktiviteter/Dans.se/Idrott.se)

== Description ==

CogWork offers a cloud based service for administrating events, booking, payments, book keeping and much more.
All administration is done on a separate web site.

This plugin allows you to inlcude content and funtionality from CogWork with simple shortcodes.
To include a list of products or bookable events you only need to write [cw shop] where ever you want it to show

== Installation ==

1. Install the plugin through the WordPress plugins screen
2. Activate the plugin through the plugins screen in WordPress
3. Use the Settings->CogWork screen to configure the plugin
4. Place shortcodes on any web page you want content from CogWork to display.


== Frequently Asked Questions ==

= Examples of short codes =

[cw shop] List of events/articles with booking functionality
[cw calendar] Simple events and scheduled occasions
[cw membership] Click to register a new membership
[cwChildPages] Output ul list with links to childpages. 

= What short codes are planned? =

[cw instructors] Displaying all instructors and their current or upcomming classes<br />
[cw schedule] Display todays schedule with classes and locations (rooms)<br />
[cw login] Login into WordPress using a CogWork account<br />
[cw my-page] Showing current bookings etc for loged in WP users<br />

== Screenshots ==

1. Open settings to connect WordPress to your CogWork account
2. Place a shortcode where you want the content to show up. You can write the shortcode by hand directly on the page or use the media button labeled [CW].
3. The visitors sees data fetched from your CogWork account

== Changelog ==

= 0.3.3 =
* Added a setting for selecting http or https for server connection.
* Fixed broken link in settings.
* Can use both Curl and file_get_content to load external content

= 1.0 =
* New syntax. [cw shop] instead of [cwShop]
* Old shortcode [cwShop] still works for backward compatibility
* New syntax allows new shortcodes to be added without modifying this plugin
* Added a new shortcode [cw calendar]

= 1.1 =
* Added a WP media button [CW] to make it easier for the user to add CW shortcodes

= 1.1.1 =
* Added an option to show public data from other organizations
* Added more shortcodes in the [CW] media button

= 1.1.2 =
* Fixed a bug in version 1.1.1 that could display an error message to the end user

= 1.2 =
* Added support for translation
* Displays errors and problems when generating content as comments hidden in the generated HTML code

= 1.3 =
* Added an optional API Key in the plugin settings to request data that are not public

= 1.3.2 =
* Repository changes.Commented program code

= 1.4 =
* Potential content types as well as content type specific options are loaded from the CW server

= 1.4.1 =
* Bugfix. Dynamic options in version 1.4 only worked if WordPress was installed in a specific folder.

= 1.4.2 =
* Bugfix. Shortcode parameters without a specified value created wrong URL.

= 1.5 =
* Added shortcodes cwLink and cwChildPages for that does not fetch any external data
* Restructured code to make it easier to add new shortcodes and grasp the basic functionality

= 1.5.1 =
* Removed htmlformating in cwLinks since WP does this in advance

= 1.5.2 =
* Added shortcode cwService that does not fetch any external data
* Updated shortcode cwLink
* Updated shortcode cwChildPages

= 1.6 =
* Added CogWork block to blockeditor to to make it easier for the user to add CW shortcodes
* Added more functionality to cwLink cwToc/cwChildPages and cwService

= 1.6.1 =
* New ordering CwToc

= 1.6.2 =
* Language for cwShop will automatically be set to WordPress site language if it is supported language Swedish, English, Finnish or Spanish.
* Added more functionality to cwLink cwToc/CwChildPages and cwService

= 1.6.3 =
* Minor update to cwShortCodeProcessor class

= 1.6.4 =
* Fixed compatibility with PHP 5.5 and older versions.

= 1.6.5 =
* Disabled session_start() in admin mode. Improvements to how session cookie is stored from URL parameter

= 1.6.6 =
* Added Idrott.se as option for Extern CW-server under plugin settings

= 1.6.7 =
* Fixed compatibility issues with Elementor by disabling preview of shortcode in Elementor Pagebuilder
* Checked compatibility with WordPress 5.5

= 1.6.8 =
* Fixed error in cwShortCodeProcessor.php that caused error message

= 1.7 =
* Fixed combability issues with some websites by replacing PHP Session with cookie for storing data between page visits.

= 1.7.2 =
* Checked compatibility with WordPress 5.7

= 1.7.3 =
* Checked compatibility with WordPress 5.8

= 1.7.5 =
* Checked compatibility with WordPress 5.9

= 1.7.6 =
* Checked compatibility with WordPress 6.0

= 1.8.1 =
* Added login to website through Mina Aktiviteter user login. Timeout and error messages added to curl calls.

= 1.8.2 =
* Increased timout to 30 second for webshop. 



