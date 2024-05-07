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
		return date( self::date_format(), strtotime( $date ) );
	}
}
