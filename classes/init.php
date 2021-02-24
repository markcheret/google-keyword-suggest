<?php
/**
 * Created by Stefan Herndler.
 * User: she
 * Date: 12.08.14 15:26
 * Version: 1.0.0
 * Since: 0.0.1
 */

// entry point has to be the index.php file
if (!defined("GOOGLE_KS_INTERNAL_PLUGIN_NAME")) {
	return;
}

// define class only once
if (!class_exists("GoogleKS_Init")) :

/**
 * Class GoogleKS_Init
 */
class GoogleKS_Init {

	// GoogleKS_LayoutEngine, reference to the layout engine
	// @since 1.0.0
	/** @var GoogleKS_LayoutEngine $a_obj_LayoutEngine */
	public $a_obj_LayoutEngine = null;

	/**
	 * @constructor
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * executes the plugin task
	 * @since: 1.0.0
	 * @return void
	 */
	public function Run() {
		// check if the user is logged in and is admin
		if (is_admin()) {
			// load layout engine class
			require_once(dirname(__FILE__) . "/layoutengine.php");
			// new instance of layout engine
			$this->a_obj_LayoutEngine = new GoogleKS_LayoutEngine();
			// run the layout engine
			$this->a_obj_LayoutEngine->Run();
		}
	}

	/**
	 * executed when the plugin is activated
	 * @since: 1.0.0
	 * @return void
	 */
	public static function Activation() {
		// do some specific stuff
	}

	/**
	 * executed when the plugin is deactivated
	 * @since: 1.0.0
	 * @return void
	 */
	public static function Deactivation() {
		// do some specific stuff
	}

	/**
	 * executed when the plugin gets uninstalled / deleted
	 * @since: 1.0.0
	 * @return void
	 */
	public static function Uninstall() {
		global $g_obj_GoogleKS;
		// user has to be logged in to uninstall the plugin
		if (!is_user_logged_in()) {
			wp_die(__('You must be logged in to run this script.', GOOGLE_KS_INTERNAL_PLUGIN_NAME));
		}
		// user needs the permission to install plugins
		if (!current_user_can('install_plugins')) {
			wp_die(__('You do not have permission to run this script.', GOOGLE_KS_INTERNAL_PLUGIN_NAME));
		}
		// plugin has to be initialized
		if (empty($g_obj_GoogleKS)) {
			wp_die(__('Plugin is not initialized.', GOOGLE_KS_INTERNAL_PLUGIN_NAME));
		}
	}
} // end of class

endif;