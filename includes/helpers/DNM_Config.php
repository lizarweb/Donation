<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';

class DNM_Config {

	public static function save_general_settings() {
		check_ajax_referer( 'dnm_save_general_settings', 'nonce' );
		$currency     = sanitize_text_field( $_POST['currency'] );
		$date_format  = sanitize_text_field( $_POST['date_format'] );
		$prefix        = sanitize_text_field( $_POST['prefix'] );
	
		// Handle the logo upload
		if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
			$uploaded_file = $_FILES['logo'];
			$upload_overrides = array( 'test_form' => false );
			$move_file = wp_handle_upload( $uploaded_file, $upload_overrides );
			if ( $move_file && ! isset( $move_file['error'] ) ) {
				$logo = $move_file['url'];
				update_option( 'dnm_logo', $logo );
			}
		}
	
		update_option( 'dnm_currency', $currency );
		update_option( 'dnm_date_format', $date_format );
		update_option( 'dnm_prefix', $prefix );
		wp_send_json_success( array( 'message' => __( 'Settings saved successfully', 'donation' ) ) );
	}

	public static function get_default_date_format() {
		return 'd-m-Y';
	}

	public static function get_default_currency() {
		return 'USD';
	}

	public static function get_currency() {
		$currency = get_option( 'dnm_currency' );
		if ( ! $currency ) {
			$currency = self::get_default_currency();
		}
		return $currency;
	}

	public static function currency_symbol() {
		return DNM_Helper::currency_symbols()[self::get_currency()];
	}

	public static function get_amount_text( $amount) {
		
		$amount = number_format( (float) $amount, 2, '.', '' );
		if ( 0.00 == $amount ) {
			return '-';
		}
		return self::currency_symbol() . number_format( (float) $amount, 2, '.', ',' );
	}

	public static function date_format() {
		$date_format = get_option( 'dnm_date_format' );
		if ( ! $date_format ) {
			$date_format = self::get_default_date_format();
		}
		return $date_format;
	}

	public static function date_format_text( $date ) {
		if (empty($date) || !strtotime($date)) {
			return '-';
		}
		return date( self::date_format(), strtotime( $date ) );
	}
}
