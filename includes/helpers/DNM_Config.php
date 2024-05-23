<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';

class DNM_Config {

	public static function save_general_settings() {
		check_ajax_referer( 'dnm_save_general_settings', 'nonce' );
		$currency    = sanitize_text_field( $_POST['currency'] );
		$date_format = sanitize_text_field( $_POST['date_format'] );
		$prefix      = sanitize_text_field( $_POST['prefix'] );

		// Handle the logo upload
		if ( isset( $_FILES['logo'] ) && $_FILES['logo']['error'] == 0 ) {
			$uploaded_file    = $_FILES['logo'];
			$upload_overrides = array( 'test_form' => false );
			$move_file        = wp_handle_upload( $uploaded_file, $upload_overrides );
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

	public static function save_payment_settings() {
		// Check if the nonce is valid
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'dnm_save_payment_settings' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce', 'donation' ) ) );
			return;
		}

		$phone_pay_enable           = isset( $_POST['phone_pay_enable'] ) ? 1 : 0;
		$phone_pay_mode             = sanitize_text_field( $_POST['phone_pay_mode'] );
		$phone_pay_redirect_url     = sanitize_text_field( $_POST['phone_pay_redirect_url'] );
		$phone_pay_merchant_id      = sanitize_text_field( $_POST['phone_pay_merchant_id'] );
		$phone_pay_merchant_user_id = sanitize_text_field( $_POST['phone_pay_merchant_user_id'] );
		$phone_pay_salt_key         = sanitize_text_field( $_POST['phone_pay_salt_key'] );
		$phone_pay_salt_index       = sanitize_text_field( $_POST['phone_pay_salt_index'] );

		$payment_data = array(
			'option_name'  => 'phone_pay_settings',
			'option_value' => maybe_serialize(
				array(
					'phone_pay_enable'           => $phone_pay_enable,
					'phone_pay_mode'             => $phone_pay_mode,
					'phone_pay_redirect_url'     => $phone_pay_redirect_url,
					'phone_pay_merchant_id'      => $phone_pay_merchant_id,
					'phone_pay_merchant_user_id' => $phone_pay_merchant_user_id,
					'phone_pay_salt_key'         => $phone_pay_salt_key,
					'phone_pay_salt_index'       => $phone_pay_salt_index,
				)
			),
			'autoload'     => 'yes',
		);

		global $wpdb;

		try {
			$wpdb->query( 'BEGIN' );

			// Check if the settings already exist
			$existing = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'phone_pay_settings' ) );

			if ( $existing ) {
				// If the settings exist, update them
				$result = $wpdb->update( DNM_SETTINGS, $payment_data, array( 'ID' => $existing->ID ) );
				if ( false === $result ) {
					throw new Exception( 'Failed to update settings.' );
				}
			} else {
				// If the settings don't exist, insert a new row
				$result = $wpdb->insert( DNM_SETTINGS, $payment_data );
				if ( false === $result ) {
					throw new Exception( 'Failed to insert settings.' );
				}
			}

			// If everything is fine, commit the transaction
			$wpdb->query( 'COMMIT' );

			wp_send_json_success( array( 'message' => __( 'Settings saved successfully', 'donation' ) ) );
		} catch ( Exception $e ) {
			// An error occurred, rollback the transaction
			$wpdb->query( 'ROLLBACK' );

			// Handle the error
			wp_die( $e->getMessage() );
		}
	}

	public static function save_email_settings() {
		// Check if the nonce is valid
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'dnm_save_email_settings' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce', 'donation' ) ) );
			return;
		}

		$email_enable = isset( $_POST['email_enable'] ) ? 1 : 0;
		// $email_from   = sanitize_text_field( $_POST['email_from'] );
		// $email_to     = sanitize_text_field( $_POST['email_to'] );
		// $email_cc     = sanitize_text_field( $_POST['email_cc'] );
		// $email_bcc    = sanitize_text_field( $_POST['email_bcc'] );

		$email_data = array(
			'option_name'  => 'email_settings',
			'option_value' => maybe_serialize(
				array(
					'email_enable' => $email_enable,
					// 'email_from'   => $email_from,
					// 'email_to'     => $email_to,
					// 'email_cc'     => $email_cc,
					// 'email_bcc'    => $email_bcc,
				)
			),
			'autoload'     => 'yes',
		);

		global $wpdb;

		try {
			$wpdb->query( 'BEGIN' );

			// Check if the settings already exist
			$existing = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'email_settings' ) );

			if ( $existing ) {
				// If the settings exist, update them
				$result = $wpdb->update( DNM_SETTINGS, $email_data, array( 'ID' => $existing->ID ) );
				if ( false === $result ) {
					throw new Exception( 'Failed to update settings.' );
				}
			} else {
				// If the settings don't exist, insert a new row
				$result = $wpdb->insert( DNM_SETTINGS, $email_data );
				if ( false === $result ) {
					throw new Exception( 'Failed to insert settings.' );
				}
			}

			// If everything is fine, commit the transaction
			$wpdb->query( ' COMMIT' );

			wp_send_json_success( array( 'message' => __( 'Settings saved successfully', 'donation' ) ) );
		} catch ( Exception $e ) {
			// An error occurred, rollback the transaction
			$wpdb->query( 'ROLLBACK' );

			// Handle the error
			wp_die( $e->getMessage() );
		}
	}

	public static function save_email_template_settings() {
		// Check if the nonce is valid
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'dnm_save_email_template_settings' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce', 'donation' ) ) );
			return;
		}

		$payment_confirm_subject = sanitize_text_field( $_POST['payment_confirm_subject'] ) ? sanitize_text_field( $_POST['payment_confirm_subject'] ) : 'Payment Confirmation';
		$payment_confirm_body    = sanitize_text_field( $_POST['payment_confirm_body'] ) ? sanitize_text_field( $_POST['payment_confirm_body'] ) : 'Thank you for your payment. Your payment has been confirmed.';

		$email_data = array(
			'option_name'  => 'email_templates',
			'option_value' => maybe_serialize(
				array(
					'payment_confirm_subject' => $payment_confirm_subject,
					'payment_confirm_body'    => $payment_confirm_body,
				)
			),
			'autoload'     => 'yes',
		);

		global $wpdb;

		try {
			$wpdb->query( 'BEGIN' );

			// Check if the settings already exist
			$existing = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'email_templates' ) );

			if ( $existing ) {
				// If the settings exist, update them
				$result = $wpdb->update( DNM_SETTINGS, $email_data, array( 'ID' => $existing->ID ) );
				if ( false === $result ) {
					throw new Exception( 'Failed to update settings.' );
				}
			} else {
				// If the settings don't exist, insert a new row
				$result = $wpdb->insert( DNM_SETTINGS, $email_data );
				if ( false === $result ) {
					throw new Exception( 'Failed to insert settings.' );
				}
			}

			// If everything is fine, commit the transaction
			$wpdb->query( ' COMMIT' );

			wp_send_json_success( array( 'message' => __( 'Settings saved successfully', 'donation' ) ) );
		} catch ( Exception $e ) {
			// An error occurred, rollback the transaction
			$wpdb->query( 'ROLLBACK' );

			// Handle the error
			wp_die( $e->getMessage() );
		}
	}

	public static function get_phone_pay_settings() {
		global $wpdb;
		$phone_pay_settings = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'phone_pay_settings' ) );
		if ( $phone_pay_settings ) {
			$phone_pay_settings = maybe_unserialize( $phone_pay_settings->option_value );
		}
		return $phone_pay_settings;
	}

	public static function get_email_settings() {
		global $wpdb;
		$email_settings = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'email_settings' ) );
		if ( $email_settings ) {
			$email_settings = maybe_unserialize( $email_settings->option_value );
		}
		return $email_settings;
	}

	public static function get_email_templates() {
		global $wpdb;
		$email_templates = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'email_templates' ) );
		if ( $email_templates ) {
			$email_templates = maybe_unserialize( $email_templates->option_value );
		}
		return $email_templates;
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
		return DNM_Helper::currency_symbols()[ self::get_currency() ];
	}

	public static function get_amount_text( $amount ) {

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
		if ( empty( $date ) || ! strtotime( $date ) ) {
			return '-';
		}
		return date( self::date_format(), strtotime( $date ) );
	}
}
