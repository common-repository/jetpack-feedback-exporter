=== Plugin Name ===
Contributors: jtwiest
Donate Link: http://jtwventures.com/projects/jetpack-feedback-exporter
Tags: csv, jetpack, feedback, export, excel
Requires at least: 3.2
Tested up to: 3.51
Stable tag: 1.23
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export Jetpack Contact Form Feedback data to a csv file. Select which form you would like and click export, that's it! (Jetpack must be installed)

== Description ==

A plugin that lets you export all of the form submission from Jetpack Feedback forms. Navigation to 'Tools' -> 'Feedback Exporter', choose the desired form to export, select if you would like to include the date, click export and your done!

= Features =

* Export all data
* Export into CSV
* Opens easily in excel 
* Contains header tags
* Optional Date 
* NOTE: You must have jetpack installed.

== Installation ==

For an automatic installation through WordPress:

1. Go to the 'Add New' plugins screen in your WordPress admin area
1. Search for 'Export Users to CSV'
1. Click 'Install Now' and activate the plugin
1. Go the 'Tools' menu, under 'Feedback Exporter'

For a manual installation via FTP:

1. Upload the 'feedback_exporter'`directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' screen in your WordPress admin area
1. Go the 'Tools' menu, under 'Feedback Exporter'

To upload the plugin through WordPress, instead of FTP:

1. Upload the downloaded zip file on the 'Add New' plugins screen (see the 'Upload' tab) in your WordPress admin area and activate.
1. Go the 'Tools' menu, under 'Feedback Exporter'

== Frequently Asked Questions ==

= How to use? =

Click on the 'Feedback Exporter' link in the 'Tools' menu, choose the form that you would like to export and click "Export". Your browser will then automatically start downloading a .csv file with your desired information. 

== Screenshots ==

1. Feedback Export Screen

== Changelog ==

= 1.23 =
* Added 2 more options for separating values ':', ';', ','

= 1.22 = 
* Removed testing printout.

= 1.21 =
* Fixed bug, sometimes the first row wouldn't print.

= 1.2 = 
* Improved load time by 12% through query optimization. 
* Fixed Bug related to commas within text.

= 1.1 = 
* Updated queries to support multisite installs.

= 1.0 =
* First public release.
* Added readme.txt.
* Added screenshot.

== Upgrade Notice ==

= 1.22 = 
Removed testing printout.

= 1.21 = 
Fixed bug, sometimes the first row wouldn't print.

= 1.2 = 
Improved load time by 12% by query optimization. 
Fixed Bug related to commas within text.

= 1.1 = 
Updated query statements to support multisite installs.

= 1.0 =
First release.