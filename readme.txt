=== Fiber Admin ===
Contributors: daomapsieucap
Tags: white label, admin tool, duplicate post, content protection
Requires at least: 4.7
Tested up to: 5.9.1
Requires PHP: 5.6
Stable tag: 1.7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Another admin tool with some extra functions for WordPress backend.

**FEATURES**

* Use your Site Settings to white label WordPress automatically.
* Customize admin login page by multiple options.
* Set Image Metadata automatically after uploading.
* Convert plain email text into link automatically.
* Enable SVG support.
* Drag and drop post types / taxonomies order.
* Duplicate post types with single item or bulk action.
* Protect your site image by disable right click / drag image into html page *(only for non-admin users)*.
* Protect your site content by disable these following keys: Ctrl / Cmd + S, Ctrl / Cmd + A, Ctrl / Cmd + C, Ctrl / Cmd + X, Ctrl / Cmd + Shift + I *(only for non-admin users)*.
* Disable WordPress comments in backend & WordPress Automatic Updates completely as default.
* Customize WordPress Database Error page.

== Frequently Asked Questions ==

= Why can't I save the settings for Customized WordPress Database Error page? =

At the first time using this setting, Fiber Admin will ask you to save it to create the `db-error.php` file. When your site has some security plugins like iThemes Security or Wordfence Security..., these plugins will have an option to prevent file editor. All you need is disable this option temporarily and save the Fiber Admin Database Error first, then you can activate the option to disable File Editor in security plugins.

== Installation ==

1. Install either via the WordPress.org plugin directory, or by uploading the files to your server.
2. Go to CMS admin plugins list and activate the plugin.
3. Go to Fiber Admin setting page and update the options.

== Screenshots ==

1. Main setting page with multiple options
2. Miscellaneous page to control some default features
3. Simple Custom Post Order setting for drag and drop Post Type / Taxonomy Order
4. Demo of an admin login page customization
5. Demo of a customized database error page

== Changelog ==

= 1.7.0 =
*Release Date - 07 March 2022*

* Changed: Enable duplicate post as default.
* Changed: Code improvement.