<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';

class DNM_Config {
	public static function get_default_date_format() {
		return 'd-m-Y';
	}

	public static function get_default_currency() {
		return 'USD';
	}
}
