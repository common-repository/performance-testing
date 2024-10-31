=== Performance Testing ===
Contributors: jacobsantos
Donate link: http://www.santosj.name
Tags: wordpress, performance, testing, xdebug, debug, cachegrind
Requires at least: 2.5.0
Tested up to: trunk
Stable tag: trunk

Processes Xdebug cachegrind files for viewing in WordPress.

== Description ==

The Performance Testing plugin tests the performance of both PHP and MySQL, if mysqlnd driver is installed, to find overhead in both WordPress and WordPress plugins. The requirements of this plugin is that you use PHP5 and have XDebug installed.

This plugin is not meant for end users, you must know how to install XDebug (http://codex.wordpress.org/Testing_WordPress_Performance) and how to configure it. You must have permissions to enable the extension and access to the php.ini file to configure XDebug. When this is all finished, the plugin will take over the rest and all you will need to do is choose which files you want to process.

This plugin is meant for debugging WordPress and WordPress plugins. If you do not need to debug WordPress or WordPress plugins, then you do not need to use this plugin.

== Installation ==

This plugin requires XDebug and PHP5. You can optionally configure either mysql and/or mysqli for mysqlnd driver. If the mysqlnd driver exist, then new statistics will be available.

The assumption is that you already downloaded and extracted the archive that holds this file.

1. Upload the 'performance-testing' folder to the 'wp-content\plugins' directory
1. Activate the plugin titled "Performance Testing" through the "Plugins" menu in WordPress
1. Once you activation the plugin, go to the settings page under the Plugins menu.

== Screenshots ==

No screenshots.

== Configuration ==

To be completed later.

== Changelog ==

1. 0.1 - Alpha development. Implementing basic hooks and administration.
1. 0.2 - Basic hooks, administration, and mysqli port wpdb object.
1. 0.3 - Do not allow plugin activation when XDebug is not installed. MySQL install and uninstall. Completed cachegrind library.
1. 0.4 - Completed Plugin that completes all important versions. Based from the Google Summer of Code Project. First public version.

== TODO ==

1. The mysqlnd statistics is still incomplete, because there is currently no testbed available to see the statistics. This will have to be available.
1. Build unit tests for plugin code and go over inline documentation and improve as needed. Send over if needed and by September 1st 2008.
1. The cachegrind library needs to be unit tested and needs to conform to the file grammar better. The initial version works, but there are still some parts left over. The grammar would be better placed in an PHP extension, but there also needs to be a PHP userland counterpart for those who do not wish to install another PHP extension other than XDebug. Regardless, the PHP userland cachegrind library in use has to be improved to provide better statistics. This does not say that the statistics is not fairly accurate, it is, but it needs to be perfect for version 1.0 release.
1. The cachegrind processing takes far too long, it needs to be Ajax-ified, so that it is faster and the timeout is minimized. However, it might just be my machine is doing too much and is slowing down PHP itself.
1. 1.0-beta1 - Initial beta release for bug fixes.
1. 1.0-beta2 - Second beta release for bug fixes.
1. 1.0-rc1 - Release candidate 1 for real world testing.
1. 1.0-rc2 - Release candidate 2 for real world testing for bugs.
1. 1.0 - Production release and adding to WordPress Extend

== Frequently Asked Questions ==

There are no questions currently. If you need help contact me at www.santosj.name.

This plugin is alpha development. Use at your own risk. No feedback will be accepted at this time.