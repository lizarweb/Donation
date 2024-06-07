<?php

defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';

require_once DNM_PLUGIN_DIR_PATH . 'includes/vendor/autoload.php';

use PhonePe\PhonePe;
use Stripe\Subscription;

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
		
		// Start the session if it's not already started
		if (!session_id()) {
			session_start();
		}
		
		// Save the user data in the session
		$_SESSION['user_data'] = $user_data;

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
		
		// Start the session if it's not already started
		if (!session_id()) {
			session_start();
		}
		
		// Save the user data in the session
		$_SESSION['user_data'] = $user_data;

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
		$errors       = array();

		// convert $amount to integer
		$amount = (int) $amount;

		$customer_exits = DNM_Database::getRecord( DNM_USERS, 'user_email', $email );
		if ( $customer_exits ) {
			$errors['email'] = 'Email already exists';
		}

		// validate reference_id only if it's not empty
		if ( ! empty( $reference_id ) ) {
			$errors = DNM_Helper::validate_reference_id( $reference_id, 10 );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}
		$user_id        = null; // Define $user_id here
		$customer_exits = DNM_Database::getRecord( DNM_USERS, 'user_email', $email );
		if ( ! $customer_exits ) {
			$new_pass = wp_generate_password();
			$user_id  = wp_insert_user(
				array(
					'user_login' => $email,
					'user_pass'  => $new_pass,
					'user_email' => $email,
					'role'       => 'dnm_member',
				)
			);
			if ( is_wp_error( $user_id ) ) {
				throw new Exception( 'Failed to register user' );
			}
			// send email to customer with username and password
			$subject = 'Your account has been created';
			$body    = 'Your account has been created. Here are your login details:<br>';
			$body   .= 'Username: ' . $email . '<br>';
			$body   .= 'Password: ' . $new_pass . '<br>';
			wp_mail( $email, $subject, $body );
		}

		// customer data
		$customerData = array(
			'name'         => $name,
			'email'        => $email,
			'phone'        => $phone,
			'city'         => $city,
			'state'        => $state,
			'address'      => $address,
			'reference_id' => $reference_id,
			'user_id'      => $user_id,
			'created_at'   => current_time( 'mysql' ),
		);
		$customer_id  = DNM_Database::insertIntoTable( DNM_CUSTOMERS, $customerData );
		if ( ! $customer_id ) {
			throw new Exception( 'Failed to insert customer data' );
		}

		$payment_type  = 'membership';
		$transactionId = 'TRAN' . date( 'ymdHis' );

		$order_data = array(
			'order_id'       => DNM_Helper::getNextOrderId( $payment_type ),
			'transaction_id' => $transactionId, // Corrected 'transaction_id' to 'transaction_id'
			'type'           => $payment_type,
			'payment_method' => 'Phonepe',
			'customer_id'    => $customer_id,
			'amount'         => $amount,
			'created_at'     => current_time( 'mysql' ),
		);

		// Check if the order already exists before inserting
		$exists_order = DNM_Database::getRecord( DNM_ORDERS, 'transaction_id', $transactionId );
		if ( ! $exists_order ) {
			$order_id = DNM_Database::insertIntoTable( DNM_ORDERS, $order_data );
			if ( ! $order_id ) {
				throw new Exception( 'Failed to insert order data' );
			}
		}

		if ( $user_id ) {
			wp_set_current_user( $user_id, $email );
			wp_set_auth_cookie( $user_id );
			do_action( 'wp_login', $email );
		}

		// Redirect the user to the account page
		$redirect_url = home_url( '/member-account' ); // Replace '/account' with the path to your account page

		wp_send_json(
			array(
				'message'  => 'success',
				'redirect' => $redirect_url,
			)
		);
		exit;
	}

	public static function save_membership_registration_form_old_method() {
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

		// convert $amount to integer
		$amount = (int) $amount;

		$errors = array();

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
		$merchantId         = $phone_pay_settings['phone_pay_merchant_id'];
		$merchantUserId     = $phone_pay_settings['phone_pay_merchant_user_id'];
		$saltKey            = $phone_pay_settings['phone_pay_salt_key'];
		$saltIndex          = $phone_pay_settings['phone_pay_salt_index'];
		$callbackUrl        = $phone_pay_settings['phone_pay_redirect_url'];

		$amountInPaisa = $amount * 100; // Amount in Paisa
		$userMobile    = $phone; // User Mobile Number
		$transactionID = 'TRANS' . date( 'ymdHis' ); // Transaction ID to track and identify the transaction, make sure to save this in your database.

		$user_data = array(
			'user'          => $donation_data,
			'transactionID' => $transactionID,
		);
		
		// Start the session if it's not already started
		if (!session_id()) {
			session_start();
		}
		
		// Save the user data in the session
		$_SESSION['user_data'] = $user_data;

		$subscriptionId = 'SUBS' . date( 'ymdHis' );
		$authRequestId  = 'AUTH' . date( 'ymdHis' );

		// create phonepe user subscription here.
		$subscription = DNM_Helper::create_phonepe_user_subscription( $subscriptionId, $userMobile, $amountInPaisa, $frequency = 'MONTHLY', $recurringCount = 12 );

		if ( $subscription['state'] === 'CREATED' ) {
			// pay using subscription
			$responseData = DNM_Helper::pay_using_phonepe_user_subscription( $merchantId, $merchantUserId, $subscriptionId, $authRequestId, $saltKey, $saltIndex, $callbackUrl, 'WEB', 'UPI_QR', 'com.phonepe.app' );

			$code        = $responseData['code'];
			$redirectUrl = $responseData['data']['redirectUrl'];

			if ( $code === 'SUCCESS' ) {
				$response = array(
					'code'        => $code,
					'redirectUrl' => $redirectUrl,
				);

				echo json_encode( $response );

				$user                  = $user_data['user'];
				$payment_type          = isset( $user['payment_type'] ) ? $user['payment_type'] : 'membership';
				$reference_id          = isset( $user['reference_id'] ) ? $user['reference_id'] : null;
				$merchantTransactionId = $user_data['transactionID'];

				try {
					global $wpdb;
					$wpdb->query( 'BEGIN' );

					$customerData = array(
						'name'         => $user['name'],
						'email'        => $user['email'],
						'phone'        => $user['phone'],
						'city'         => $user['city'],
						'state'        => $user['state'],
						'address'      => $user['address'],
						'reference_id' => $reference_id,
						'created_at'   => current_time( 'mysql' ),
					);
					if ( ! isset( $customer_id ) ) {
						$customer_id = DNM_Database::insertIntoTable( DNM_CUSTOMERS, $customerData );
						if ( ! $customer_id ) {
							throw new Exception( 'Failed to insert customer data' );
						}
					}

					if ( true ) {
						$customer_exits = DNM_Database::getRecord( DNM_USERS, 'user_email', $user['email'] );
						if ( ! $customer_exits ) {
							$new_pass = wp_generate_password();
							$user_id  = wp_insert_user(
								array(
									'user_login' => $user['email'],
									'user_pass'  => $new_pass,
									'user_email' => $user['email'],
									'role'       => 'dnm_member',
								)
							);
							if ( is_wp_error( $user_id ) ) {
								throw new Exception( 'Failed to register user' );
							}
							$customerData['user_id'] = $user_id;
							// send email to customer with username and password
							$subject = 'Your account has been created';
							$body    = 'Your account has been created. Here are your login details:<br>';
							$body   .= 'Username: ' . $user['email'] . '<br>';
							$body   .= 'Password: ' . $new_pass . '<br>';
							wp_mail( $user['email'], $subject, $body );
						}
					}

					$order_data = array(
						'order_id'       => DNM_Helper::getNextOrderId( $payment_type ),
						'transaction_id' => $merchantTransactionId, // Corrected 'transaction_id' to 'transaction_id'
						'type'           => $payment_type,
						'payment_method' => 'Phonepe',
						'customer_id'    => $customer_id,
						'amount'         => $user['amount'],
						'created_at'     => current_time( 'mysql' ),
					);

					// Check if the order already exists before inserting
					$exists_order = DNM_Database::getRecord( DNM_ORDERS, 'transaction_id', $merchantTransactionId );
					if ( ! $exists_order ) {
						$order_id = DNM_Database::insertIntoTable( DNM_ORDERS, $order_data );
						if ( ! $order_id ) {
							throw new Exception( 'Failed to insert order data' );
						}
					} else {
						// If order already exists, get the order_id from the existing record
						$order_id = $exists_order['id'];
					}

					$wpdb->query( 'COMMIT' );

					// check if email Notifications are enabled.
					$email_enable = DNM_Config::get_email_settings();
					if ( $email_enable['email_enable'] ) {
						// send email to customer.
						$email_templates = DNM_Config::get_email_templates();
						$subject         = $email_templates['payment_confirm_subject'];
						$body            = $email_templates['payment_confirm_body'];

						$placeholders = array(
							'{name}'           => $user['name'],
							'{amount}'         => $user['amount'],
							'{transaction_id}' => $merchantTransactionId,
							'{reference_id}'   => $reference_id,
						);

						$subject = strtr( $subject, $placeholders );
						$body    = strtr( $body, $placeholders );

						wp_mail( $user['email'], $subject, $body );
					}
				} catch ( Exception $e ) {
					$wpdb->query( 'ROLLBACK' ); // If any exception is thrown, rollback the transaction
					error_log( $e->getMessage() ); // Log the error message for debugging
				}

				exit;
			}
		}

		// $phonepe            = PhonePe::init(
		// $phone_pay_settings['phone_pay_merchant_id'], // Merchant ID
		// $phone_pay_settings['phone_pay_merchant_user_id'], // Merchant User ID
		// $phone_pay_settings['phone_pay_salt_key'], // Salt Key
		// $phone_pay_settings['phone_pay_salt_index'], // Salt Index
		// $phone_pay_settings['phone_pay_redirect_url'], // Redirect URL, can be defined on per transaction basis
		// $phone_pay_settings['phone_pay_redirect_url'], // Callback URL, can be defined on per transaction basis
		// $phone_pay_settings['phone_pay_mode'] // or "PROD"
		// );

		// $redirectURL = $phonepe->standardCheckout()->createTransaction( $amountInPaisa, $userMobile, $transactionID )->getTransactionURL();
		// echo $redirectURL;
		// exit;
	}

	public static function activate_subscription() {
		$ID = $_POST['order_id'];
		$order    = DNM_Database::getRecord( DNM_ORDERS, 'ID', $ID );
		if ( ! $order ) {
			wp_send_json_error( array( 'message' => 'Order not found' ) );
		}

		$customer_id = $order->customer_id;
		$customer    = DNM_Database::getRecord( DNM_CUSTOMERS, 'ID', $customer_id );
		if ( ! $customer ) {
			wp_send_json_error( array( 'message' => 'Customer not found' ) );
		}

		$phone_pay_settings = DNM_Config::get_phone_pay_settings();

		$subscriptionId  = 'SUBS' . date( 'ymdHis' );
		$authRequestId   = 'AUTH' . date( 'ymdHis' );
		$amount_in_paisa = $order->amount * 100;
		$phone           = $customer->phone;

		// create phonepe user subscription here.
		$subscription = DNM_Helper::create_phonepe_user_subscription( $subscriptionId, $phone, $amount_in_paisa, 'MONTHLY', 12 );

		

		if ( $subscription['state'] === 'CREATED' ) {
			$sub_id = $subscription['subscriptionId'];
			// Pay using subscription.
			$responseData = DNM_Helper::pay_using_phonepe_user_subscription(
				$phone_pay_settings['phone_pay_merchant_id'],
				$phone_pay_settings['phone_pay_merchant_user_id'],
				$sub_id,
				$authRequestId,
				$phone_pay_settings['phone_pay_salt_key'],
				$phone_pay_settings['phone_pay_salt_index'],
				$phone_pay_settings['phone_pay_redirect_url'],
				'UPI_QR',
			);

			$code        = $responseData['code'];
			$redirectUrl = $responseData['data']['redirectUrl'];

			if ( $code === 'SUCCESS' ) {
				$response = array(
					'code'        => $code,
					'redirectUrl' => $redirectUrl,
				);

				// Update order status.
				$customer_data = array(
					'auth_id' => $authRequestId,
					'sub_id'  => $subscriptionId,
				);

				$updated = DNM_Database::updateTable( DNM_CUSTOMERS, $customer_data, array( 'ID' => $customer_id ) );

				echo wp_json_encode( $response ); exit;
			}

			wp_send_json_error( array( 'message' => 'Failed to activate subscription' ) );

		}
	}

	public static function verify_subscription() {
		$ID = $_POST['order_id'];
		$order    = DNM_Database::getRecord( DNM_ORDERS, 'ID', $ID );
		if ( ! $order ) {
			wp_send_json_error( array( 'message' => 'Order not found' ) );
		}

		$customer_id = $order->customer_id;
		
		$customer    = DNM_Database::getRecord( DNM_CUSTOMERS, 'ID', $customer_id );
		if ( ! $customer ) {
			wp_send_json_error( array( 'message' => 'Customer not found' ) );
		}

		$phone_pay_settings = DNM_Config::get_phone_pay_settings();

		$subscriptionId  = $customer->sub_id;
		$authRequestId   = $customer->auth_id;
		$amount_in_paisa = $order->amount * 100;
		$phone           = $customer->phone;

		// check auth status

		$auth_status = DNM_Helper::check_auth_request_status( $phone_pay_settings['phone_pay_merchant_id'], $authRequestId, $phone_pay_settings['phone_pay_salt_key'], $phone_pay_settings['phone_pay_salt_index'] );

		error_log( 'Auth Status: ' . print_r( $auth_status, true ) );

		if ( $auth_status['state'] == 'ACTIVE' ) {
				// update customer subscription status
				$customer_data = array(
					'Subscription_status' => 'active',
				);
				$updated = DNM_Database::updateTable( DNM_CUSTOMERS, $customer_data, array( 'ID' => $customer_id ) );
				wp_send_json_success( array( 'message' => 'Subscription activated successfully' ) );
			exit;
		}
	}
}
