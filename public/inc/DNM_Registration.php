<?php

defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';

require_once DNM_PLUGIN_DIR_PATH . 'includes/vendor/autoload.php';

use PhonePe\PhonePe;

class DNM_Registration {

	public static function save_custom_registration_form() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'dnm_save_custom_registration_form' ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}

		$name    = sanitize_text_field( $_POST['name'] );
		$email   = sanitize_email( $_POST['email'] );
		$phone   = filter_var( $_POST['phone'], FILTER_SANITIZE_NUMBER_INT );
		$phone   = preg_replace( '/[^0-9]/', '', $phone ); // Remove non-digit characters
		$city    = sanitize_text_field( $_POST['city'] );
		$state   = sanitize_text_field( $_POST['state'] );
		$address = sanitize_text_field( $_POST['address'] );
		$amount  = sanitize_text_field( $_POST['amount'] );

		$donation_data = array(
			'name'         => $name,
			'email'        => $email,
			'phone'        => $phone,
			'city'         => $city,
			'state'        => $state,
			'address'      => $address,
			'amount'       => $amount,
			'payment_type' => 'custom',
		);

		// Below are the Test Details for Standard Checkout UAT, you can get your own from PhonePe Team. Make sure to keep the Salt Key and Salt Index safe (in environment variables or .env file).
		$phone_pay_settings = DNM_Config::get_phone_pay_settings();
		$phonepe            = PhonePe::init(
			$phone_pay_settings['phone_pay_merchant_id'], // Merchant ID
			// $phone_pay_settings['phone_pay_merchant_user_id'], // Merchant User ID
			$phone_pay_settings['phone_pay_salt_key'], // Salt Key
			$phone_pay_settings['phone_pay_salt_index'], // Salt Index
			$phone_pay_settings['phone_pay_redirect_url'], // Redirect URL, can be defined on per transaction basis
			$phone_pay_settings['phone_pay_redirect_url'], // Callback URL, can be defined on per transaction basis
			$phone_pay_settings['phone_pay_mode'] // or "PROD"
		);

		$amountInPaisa = $amount * 100; // Amount in Paisa
		$userMobile    = $phone; // User Mobile Number
		// $transactionID = 'MERCHANT' . rand( 100000, 999999 ); // Transaction ID to track and identify the transaction, make sure to save this in your database.
		$transactionID = 'TRANS' . date( 'ymdHis' );

		$user_data = array(
			'user'          => $donation_data,
			'transactionID' => $transactionID,
		);
		// Save the transaction ID in transient for 5 min.
		set_transient( 'user_data', $user_data, 30 * MINUTE_IN_SECONDS );

		$redirectURL = $phonepe->standardCheckout()->createTransaction( $amountInPaisa, $userMobile, $transactionID )->getTransactionURL();
		// You can also define the redirect and callback URL on per transaction basis
		// $redirectURL = $phonepe->standardCheckout()->createTransaction($amountInPaisa, $userMobile, $transactionID, "http://local.local/test/", "http://local.local/test/")->getTransactionURL();
		echo $redirectURL;
		exit;
	}

	public static function save_fixed_registration_form() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'dnm_save_fixed_registration_form' ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}

		$name         = sanitize_text_field( $_POST['name'] );
		$email        = sanitize_email( $_POST['email'] );
		$phone        = filter_var( $_POST['phone'], FILTER_SANITIZE_NUMBER_INT );
		$phone        = preg_replace( '/[^0-9]/', '', $phone ); // Remove non-digit characters
		$city         = sanitize_text_field( $_POST['city'] );
		$state        = sanitize_text_field( $_POST['state'] );
		$address      = sanitize_text_field( $_POST['address'] );
		$amount       = sanitize_text_field( $_POST['amount'] );
		$reference_id = sanitize_text_field( $_POST['reference_id'] );

		// conver $amount to integer
		$amount = (int) $amount;

		$errors = array();

		// check if amount is not 11000
		if ( $amount !== 11000 ) {
			$errors['amount'] = 'Amount should be 11000';
		}

		// validate reference_id only if it's not empty
		if ( ! empty( $reference_id ) ) {
			$errors = DNM_Helper::validate_reference_id( $reference_id, 10 );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		$donation_data = array(
			'name'         => $name,
			'email'        => $email,
			'phone'        => $phone,
			'city'         => $city,
			'state'        => $state,
			'address'      => $address,
			'amount'       => $amount,
			'payment_type' => '11000',
			'reference_id' => $reference_id,
		);

		// // Below are the Test Details for Standard Checkout UAT, you can get your own from PhonePe Team. Make sure to keep the Salt Key and Salt Index safe (in environment variables or .env file).
		$phone_pay_settings = DNM_Config::get_phone_pay_settings();
		$phonepe            = PhonePe::init(
			$phone_pay_settings['phone_pay_merchant_id'], // Merchant ID
			$phone_pay_settings['phone_pay_merchant_user_id'], // Merchant User ID
			$phone_pay_settings['phone_pay_salt_key'], // Salt Key
			$phone_pay_settings['phone_pay_salt_index'], // Salt Index
			$phone_pay_settings['phone_pay_redirect_url'], // Redirect URL, can be defined on per transaction basis
			$phone_pay_settings['phone_pay_redirect_url'], // Callback URL, can be defined on per transaction basis
			$phone_pay_settings['phone_pay_mode'] // or "PROD"
		);

		$amountInPaisa = $amount * 100; // Amount in Paisa
		$userMobile    = $phone; // User Mobile Number
		// $transactionID = 'MERCHANT' . rand( 100000, 999999 ); // Transaction ID to track and identify the transaction, make sure to save this in your database.
		$transactionID = 'TRANS' . date( 'ymdHis' );

		$user_data = array(
			'user'          => $donation_data,
			'transactionID' => $transactionID,
		);
		// Save the transaction ID in transient for 5 min.
		set_transient( 'user_data', $user_data, 30 * MINUTE_IN_SECONDS );

		$redirectURL = $phonepe->standardCheckout()->createTransaction( $amountInPaisa, $userMobile, $transactionID )->getTransactionURL();
		echo $redirectURL;
		exit;
	}

	public static function save_membership_registration_form() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'dnm_save_membership_registration_form' ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
		}

		$name         = sanitize_text_field( $_POST['name'] );
		$email        = sanitize_email( $_POST['email'] );
		$phone        = filter_var( $_POST['phone'], FILTER_SANITIZE_NUMBER_INT );
		$phone        = preg_replace( '/[^0-9]/', '', $phone ); // Remove non-digit characters
		$city         = sanitize_text_field( $_POST['city'] );
		$state        = sanitize_text_field( $_POST['state'] );
		$address      = sanitize_text_field( $_POST['address'] );
		$amount       = sanitize_text_field( $_POST['amount'] );
		$reference_id = sanitize_text_field( $_POST['reference_id'] );

		// conver $amount to integer
		$amount = (int) $amount;

		$errors = array();

		// check if amount is not 101, 501, or 11000
		// if ( $amount !== 101 && $amount !== 501 && $amount !== 11000 ) {
		// 	$errors['amount'] = 'Amount should be 101, 501, or 11000';
		// }

		// validate reference_id only if it's not empty
		if ( ! empty( $reference_id ) ) {
			$errors = DNM_Helper::validate_reference_id( $reference_id, 10 );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		$donation_data = array(
			'name'         => $name,
			'email'        => $email,
			'phone'        => $phone,
			'city'         => $city,
			'state'        => $state,
			'address'      => $address,
			'amount'       => $amount,
			'payment_type' => 'membership',
			'reference_id' => $reference_id,
		);

		// // Below are the Test Details for Standard Checkout UAT, you can get your own from PhonePe Team. Make sure to keep the Salt Key and Salt Index safe (in environment variables or .env file).
		$phone_pay_settings = DNM_Config::get_phone_pay_settings();
		// $merchantId         = $phone_pay_settings['phone_pay_merchant_id'];
		// $merchantUserId     = $phone_pay_settings['phone_pay_merchant_user_id'];
		// $saltKey            = $phone_pay_settings['phone_pay_salt_key'];
		// $saltIndex          = $phone_pay_settings['phone_pay_salt_index'];
		// $callbackUrl        = $phone_pay_settings['phone_pay_redirect_url'];


		$amountInPaisa = $amount * 100; // Amount in Paisa
		$userMobile    = $phone; // User Mobile Number
		$transactionID = 'TRANS' . date( 'ymdHis' ); // Transaction ID to track and identify the transaction, make sure to save this in your database.

		$user_data = array(
			'user'          => $donation_data,
			'transactionID' => $transactionID,
		);
		// Save the transaction ID in transient for 5 min.
		set_transient( 'user_data', $user_data, 30 * MINUTE_IN_SECONDS );

		$phonepe            = PhonePe::init(
			$phone_pay_settings['phone_pay_merchant_id'], // Merchant ID
			$phone_pay_settings['phone_pay_merchant_user_id'], // Merchant User ID
			$phone_pay_settings['phone_pay_salt_key'], // Salt Key
			$phone_pay_settings['phone_pay_salt_index'], // Salt Index
			$phone_pay_settings['phone_pay_redirect_url'], // Redirect URL, can be defined on per transaction basis
			$phone_pay_settings['phone_pay_redirect_url'], // Callback URL, can be defined on per transaction basis
			$phone_pay_settings['phone_pay_mode'] // or "PROD"
		);

		$redirectURL = $phonepe->standardCheckout()->createTransaction( $amountInPaisa, $userMobile, $transactionID )->getTransactionURL();
		echo $redirectURL;
		exit;
	}
}
