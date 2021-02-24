<?php
/*
	Plugin Name: Google Keyword Suggest
	Plugin URI: http://wordpress.org/plugins/google-keyword-suggest/
	Description: Keyword research made fast & simple: Using the Google Keyword Suggest Plugin by SEOmotion improves your keyword research tasks and SEO.
	Author: Stefan Herndler
	Version: 1.0.2
	Author URI: http://www.herndler.org
	Text Domain: google-keyword-suggest
	Domain Path: /languages
*/
/*
	Copyright 2014 Stefan Herndler | Daniel Herndler (email : support@herndler.org | support@seomotion.org)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 3, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Created by Stefan Herndler.
 * User: Stefan
 * Date: 12.08.14 14:59
 * Version: 1.0.0
 * Since: 0.0.1
 */

// define the internal plugin name if not defined yet
if (!defined("GOOGLE_KS_INTERNAL_PLUGIN_NAME")) {
	define("GOOGLE_KS_INTERNAL_PLUGIN_NAME", "google_keyword_suggest");
}

// load plugin init class
require_once(dirname(__FILE__) . "/classes/init.php");
// load ajax callback function
require_once(dirname(__FILE__) . "/ajax.php");

global $g_obj_GoogleKS;
// get a new instance of the Plugin
if (empty($g_obj_GoogleKS)) {
	$g_obj_GoogleKS = new GoogleKS_Init();
}
// executes the Plugin
$g_obj_GoogleKS->Run();

// register hook to activate the Plugin
register_activation_hook(__FILE__, array('GoogleKS_Init', 'Activation'));
// register hook to deactivate the Plugin
register_deactivation_hook(__FILE__, array('GoogleKS_Init', 'Deactivation'));

// only admin is allowed to execute the following commands
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// register hook to uninstall the Plugin
register_uninstall_hook(__FILE__, array('GoogleKS_Init', 'Uninstall'));


/**
 * todo: save latest language and country
 * todo: save history of keywords
 * todo: count occurrences of keywords in headline and content (separate)
 * todo: adWords API count monthly volume
 *
 */