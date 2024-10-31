<?php
/**
 * Performance Testing Plugin to track the Xdebug cachegrind output files. Look
 * at the readme.txt file for more information.
 *
 * Code developed by Jacob Santos falls under the New BSD license. Some files
 * and code will be marked with GPL v2 code. Please make note of the license.
 *
 * @internal
 * The coding standards used within this plugin do not follow the WordPress
 * coding standards. The closest is the Zend Framework coding standards. Please
 * do not email me about this fact. I do not agree with every WordPress coding
 * standard and with my code, I follow different standards. This plugin will not
 * be part of the core.
 *
 * Notice that the comments do not extend 80 characters.
 * Every bracket is on its own line.
 *
 * @author Jacob Santos <plugin-dev@santosj.name>
 * @license New BSD
 * @package WordPress_Plugin
 * @subpackage Performance_Testing
 * @version 0.4.1
 * @since 0.1
 */

/*
Plugin Name: Performance Benchmark Testing
Plugin URI: http://www.santosj.name/performance/
Description: Performance Testing Plugin
Author: Jacob Santos
Version: 0.4.1
Author URI: http://www.santosj.name
 */

/*
Copyright (c) 2008, Jacob Santos
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
	Redistributions of source code must retain the above copyright
		notice, this list of conditions and the following disclaimer.
	Redistributions in binary form must reproduce the above copyright
		notice, this list of conditions and the following disclaimer in the
		documentation and/or other materials provided with the distribution.
	Neither the name of the Jacob Santos nor the
		names of its contributors may be used to endorse or promote products
		derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY Jacob Santos ``AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Jacob Santos BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

if( version_compare(PHP_VERSION, '5.0', '<') )
{
	/*
	 * PHP4 is unsupported at this time. The Xdebug 2 version this plugin
	 * supports does not support PHP4, therefore there is no reason for this
	 * plugin to support it either.
	 *
	 * It will skew the performance results. PHP5.1+ does run faster than PHP4,
	 * however, the heavy bits, the overhead, shouldn't matter which PHP
	 * version. PHP version improvements to functions notwithstanding.
	 *
	 * Note: This should just back out of the file within the global scope.
	 * However, if the plugin loads in a function, it might completely back out
	 * of the function, including the one that activates this plugin.
	 */
	return;
}

/**
 * Performance plugin base path to not do tricky voodoo to get plugin path from
 * ABSPATH and plugin directory function. Less overhead.
 *
 * @var string
 */
define('PERFORMANCE_PLUGIN_BASE', dirname(__FILE__));

/**
 * Keep track of the version in a constant for upgrading later.
 *
 * @var float
 */
define('PERFORMANCE_PLUGIN_VERSION', '0.4.1');

/** Installation, Upgrading, and Init hooks. */
include_once PERFORMANCE_PLUGIN_BASE . '/hooks/setup.php';
/** Administration Hooks for displaying admin menu and pages. */
include_once PERFORMANCE_PLUGIN_BASE . '/hooks/admin.php';
/** Shutdown Hook for capturing the location of cachegrind directory. */
include_once PERFORMANCE_PLUGIN_BASE . '/hooks/shutdown.php';

register_activation_hook( __FILE__, array('DragonU_Performance_Setup_Hooks', 'activation') );
register_deactivation_hook( __FILE__, array('DragonU_Performance_Setup_Hooks', 'deactivation') );

new DragonU_Performance_Setup_Hooks();
new DragonU_Performance_Admin_Hooks();
new DragonU_Performance_Shutdown_Hook();