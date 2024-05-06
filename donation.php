<?php
/**
 * Plugin Name: Donation
 * Plugin URI: #
 * Description: Donation
 * Version: 1.0.0
 * Author: Weblizar
 * Author URI: https://weblizar.com
 * Text Domain: donation
 */

defined( 'ABSPATH' ) || die();

if ( ! defined( 'DNM_PLUGIN_URL' ) ) {
	define( 'DNM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'DNM_PLUGIN_DIR_PATH' ) ) {
	define( 'DNM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

define( 'DNM_WEBLIZAR_PLUGIN_URL', '#' );
define( 'DNM_VERSION', '1.0.0' );

final class DN_Management {
	private static $instance = null;

	private function __construct() {
		$this->initialize_hooks();
		$this->setup_database();
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function initialize_hooks() {
		if ( is_admin() ) {
			require_once DNM_PLUGIN_DIR_PATH . 'admin/admin.php';
		}
		require_once DNM_PLUGIN_DIR_PATH . 'public/public.php';
	}

	private function setup_database() {
		require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Database.php';
		register_activation_hook( __FILE__, array( 'DNM_Database', 'activation' ) );
		register_deactivation_hook( __FILE__, array( 'DNM_Database', 'deactivation' ) );
		register_uninstall_hook( __FILE__, array( 'DNM_Database', 'uninstall' ) );
	}
}
DN_Management::get_instance();
