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
		set_transient( 'user_data', $user_data, 5 * MINUTE_IN_SECONDS );

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

		// check if reference_id is used 10 times. If yes, then return error.
		$reference_id_count = DNM_Database::getRecordCount( DNM_CUSTOMERS, 'reference_id', $reference_id );

		if ( $reference_id_count >= 10 ) {
			$errors['reference_id'] = 'Reference ID is already used 10 times';
		}

		if ( ! empty( $reference_id ) ) {
			// check if reference_id is correct format and exists in database.
			if ( ! preg_match( '/^MP[0-9]{1}$/', $reference_id ) ) {
				$errors['reference_id'] = 'Reference ID should be in format MP123456';
			}
		}

		$reference_id_exists = DNM_Database::getRecord( DNM_CUSTOMERS, 'reference_id', $reference_id );

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
		// $phonepe            = PhonePe::init(
		// $phone_pay_settings['phone_pay_merchant_id'], // Merchant ID
		// $phone_pay_settings['phone_pay_merchant_user_id'], // Merchant User ID
		// $phone_pay_settings['phone_pay_salt_key'], // Salt Key
		// $phone_pay_settings['phone_pay_salt_index'], // Salt Index
		// $phone_pay_settings['phone_pay_redirect_url'], // Redirect URL, can be defined on per transaction basis
		// $phone_pay_settings['phone_pay_redirect_url'], // Callback URL, can be defined on per transaction basis
		// $phone_pay_settings['phone_pay_mode'] // or "PROD"
		// );

		// $amountInPaisa = $amount * 100; // Amount in Paisa
		// $userMobile    = $phone; // User Mobile Number
		// // $transactionID = 'MERCHANT' . rand( 100000, 999999 ); // Transaction ID to track and identify the transaction, make sure to save this in your database.
		// $transactionID = 'TRANS' . date( 'ymdHis' );

		// $user_data = array(
		// 'user'          => $donation_data,
		// 'transactionID' => $transactionID,
		// );
		// // Save the transaction ID in transient for 5 min.
		// set_transient( 'user_data', $user_data, 5 * MINUTE_IN_SECONDS );

		// $redirectURL = $phonepe->standardCheckout()->createTransaction( $amountInPaisa, $userMobile, $transactionID )->getTransactionURL();
		// echo $redirectURL;
		// exit;

		// Your JSON payload
		$data = array(
			'merchantId'             => $phone_pay_settings['phone_pay_merchant_id'],
			'merchantSubscriptionId' => 'MSUB123456789012345',
			'merchantUserId'         => $phone_pay_settings['phone_pay_merchant_user_id'],
			'authWorkflowType'       => 'PENNY_DROP',
			'amountType'             => 'FIXED',
			'amount'                 => 39900,
			'frequency'              => 'MONTHLY',
			'recurringCount'         => 12,
			'mobileNumber'           => '9xxxxxxxxx',
			'deviceContext'          => array(
				'phonePeVersionCode' => 400922,
			),
		);

		// Convert the JSON payload to Base64
		$base64Payload = base64_encode( json_encode( $data ) );

		// Your request
		$request = array(
			'request' => $base64Payload,
		);

		// Your salt key and index
		$saltKey   = $phone_pay_settings['phone_pay_salt_key'];
		$saltIndex = $phone_pay_settings['phone_pay_salt_index'];

		// Calculate X-Verify
		$xVerify = hash( 'sha256', $base64Payload . '/v3/recurring/subscription/create' . $saltKey ) . '###' . $saltIndex;
		// Initialize cURL
		$ch = curl_init();

		// Set the options
		curl_setopt( $ch, CURLOPT_URL, 'https://api-preprod.phonepe.com/apis/pg-sandbox/v3/recurring/subscription/create' );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $request ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				'Content-Type: application/json',
				'X-Verify: ' . $xVerify,
			)
		);

		// Execute and get the response
		$response = curl_exec( $ch );

		// Close cURL
		curl_close( $ch );

		// Decode the response
		$responseData = json_decode( $response, true );


		// Check the response
		// if ( $responseData['success'] === true ) {
		// 	echo 'Subscription created successfully. Subscription ID: ' . $responseData['data']['subscriptionId'];
		// } else {
		// 	echo 'Failed to create subscription. Error: ' . $responseData['message'];
		// }

		// check subscription creation status
		// https://api-preprod.phonepe.com/apis/pg-sandbox/v3/recurring/subscription/status/{merchantId}/{merchantSubscriptionId}

		// Your merchant ID and subscription ID
		$merchantId             = $phone_pay_settings['phone_pay_merchant_id'];
		$merchantSubscriptionId = $responseData['data']['subscriptionId'];

		// Your salt key and index
		$saltKey   = $phone_pay_settings['phone_pay_salt_key'];
		$saltIndex = $phone_pay_settings['phone_pay_salt_index'];

		// Calculate X-Verify
		$xVerify = hash( 'sha256', '/v3/recurring/subscription/status/' . $merchantId . '/' . $merchantSubscriptionId . $saltKey ) . '###' . $saltIndex;

		// Initialize cURL
		$ch = curl_init();

		// Set the options
		curl_setopt( $ch, CURLOPT_URL, 'https://api-preprod.phonepe.com/apis/pg-sandbox/v3/recurring/subscription/status/' . $merchantId . '/' . $merchantSubscriptionId );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
				'Content-Type: application/json',
				'X-Verify: ' . $xVerify,
			)
		);

		// Execute and get the response
		$response = curl_exec( $ch );

		// Close cURL
		curl_close( $ch );

		// Decode the response
		$responseData2 = json_decode( $response, true );

		var_dump($responseData2); die;

		// Check the response
		// if ( $responseData['success'] === true ) {
		// 	echo 'Subscription status fetched successfully. Subscription ID: ' . $responseData['data']['subscriptionId'] . '. Status: ' . $responseData['data']['state'];
		// } else {
		// 	echo 'Failed to fetch subscription status. Error: ' . $responseData['message'];
		// }
	}
}

