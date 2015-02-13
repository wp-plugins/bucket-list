=== Plugin Name ===
Contributors: exiledesigns
Donate link: http://www.exiledesigns.com/
Tags: bucket-list, bucket, goals, to-do, achievements
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set your goals, change your life, realize your dreams and share the inspiration of your achievements!

== Description ==

Bucket List lets you create, organize and beautifully show off all the goals you have in life.

= Key Features =

Through an elegant and straight-forward administration interface, you can:

* Create as many goal categories and goals as you wish
* Smoothly organize, reorder and edit categories/goals without reload (AJAX)
* Mark a goal as achieved, and automatically register the date (you can as well modify the date)
* Link an achieved goal to an existing Post/Page in your blog
* Paste your Bucket List in any Post/Page using the [bucketlist] shortcode

= Widget =

We also included an optional widget that displays your latest achieved tasks. You can:

* Customize widget title (i.e. My Latest Achievements)
* Customize number of tasks displayed (5 by default)
* Set up a link to your complete Bucket List page (or any other page)
* Display or not the task category
* Display or not the date of achievement
 

== Screenshots ==

1. Front-end
2. Widget
3. Back-end


== Installation ==

1. Browse to the Plugins Menu in Wordpress. Then either search for "Bucket List" through the Search Tab, OR upload the "bucketlist.zip" file through the Uploads tab.
2. Activate the plugin by going back to the Plugins Menu.
3. Setup and configure Bucket List by clicking the new "Bucket List" Wordpress Menu.
4. To display the Bucket List in any of your Pages/Posts, simply paste this shortcode: **[bucketlist]**. If you would like to display only one or several categorie, paste **[bucketlist cat="4"]** or **[bucketlist cat="1,3,4"]**.
5. By default, the achievement date will show, but you can hide it: [bucketlist displaydate=0]

== Changelog ==

= 1.2.1 =
* Updated the BucketList database manager (PHP Class) to avoid PHP Error.
* Updated Retina handler (correct the selector in JavaScript)

= 1.2.0 =
* Add "dateformat" shortcode option, you can now specific a format date (refer to PHP documentation: http://php.net/manual/en/function.date.php, example: [bucketlist dateformat="m/d/Y"])
* Add format date option to the widget (refer to PHP documentation: http://php.net/manual/en/function.date.php)

= 1.1.1 =
* Fixed post selection (WordPress 4.1 update)

= 1.1.0 =
* You can now filter categories in or out your Bucket List, or display different categories on different pages using their IDs (example: [bucketlist cat="2,3"])
* The Latest Achievements Widget now also allows you to filter categories in or out
* Updated User Interface that fits WordPress 3.8 new colors, and retina support
* New branding! Bucket List Plugin is now linked to our new website: Cleio&Co (changes in the back-end: social media icons, link-back in the footer and header design).

= 1.0.3 =
* Fixed ability to display the credit link or not.
* The database isn't deleted anymore when deactivating the plugin.

= 1.0.2 =
* Fixed bug that prevented certain users to create categories.

= 1.0.1 =
* Fixed plugin path.

= 1 =
* First version commited to WordPress.