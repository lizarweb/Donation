<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';

class DNM_Shortcode {

	public static function custom_registration() {
		self::enqueue_dnm_assets();
		ob_start();
		require_once DNM_PLUGIN_DIR_PATH . 'public/inc/forms/registration_form.php';
		return ob_get_clean();
	}

	public static function fixed_registration() {
		self::enqueue_dnm_assets();
		ob_start();
		require_once DNM_PLUGIN_DIR_PATH . 'public/inc/forms/fixed_registration_form.php';
		return ob_get_clean();
	}

	public static function member_registration() {
		self::enqueue_dnm_assets();
		ob_start();
		require_once DNM_PLUGIN_DIR_PATH . 'public/inc/forms/member_registration_form.php';
		return ob_get_clean();
	}

	public static function payment_status() {
		self::enqueue_dnm_assets();
		ob_start();
		require_once DNM_PLUGIN_DIR_PATH . 'public/inc/forms/payment_status.php';
		return ob_get_clean();
	}

	public static function donation() {
		ob_start();
		require_once DNM_PLUGIN_DIR_PATH . 'public/inc/forms/donation_form.php';
		return ob_get_clean();
	}

	public static function donation_account() {
		self::enqueue_dnm_assets();
		ob_start();
		require_once DNM_PLUGIN_DIR_PATH . 'public/inc/forms/donation_account.php';
		return ob_get_clean();
	}

	public static function enqueue_dnm_assets() {
		wp_enqueue_style( 'dnm-bootstrap', DNM_PLUGIN_URL . '/assets/css/bootstrap.min.css', array(), DNM_VERSION );
		// wp_enqueue_style( 'dnm-bootstrap-icons', DNM_PLUGIN_URL . '/assets/css/bootstrap-icons.min.css', array(), DNM_VERSION );
		wp_enqueue_style( 'dnm-public-css', DNM_PLUGIN_URL . '/assets/css/public.css', array(), DNM_VERSION );

		wp_enqueue_script( 'dnm-public-js', DNM_PLUGIN_URL . '/assets/js/public.js', array( 'jquery' ), DNM_VERSION, true );
		wp_enqueue_script( 'dnm-bootstrap', DNM_PLUGIN_URL . '/assets/js/bootstrap.min.js', array( 'jquery' ), DNM_VERSION, true );
		wp_localize_script(
			'dnm-public-js',
			'dnmData',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'date_format' => DNM_Config::date_format(),
				'currency'    => DNM_Config::get_currency(),
			)
		);
	}
}
