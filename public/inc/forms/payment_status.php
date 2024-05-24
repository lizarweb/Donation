<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Database.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/vendor/autoload.php';

use PhonePe\PhonePe;

$user_data = get_transient( 'user_data' ); // Transaction ID to track and identify the transaction, make sure to save this in your database.

// check if user_data is empty
if ( empty( $user_data ) ) {
	?>
	<div class="alert alert-danger text-center" role="alert">
		<p>There was an error processing your transaction. Please try again later.</p>
	</div>
	<?php
	return;
}

try {

	$user         = $user_data['user'];
	$payment_type = isset( $user['payment_type'] ) ? $user['payment_type'] : 'membership';
	$reference_id = isset( $user['reference_id'] ) ? $user['reference_id'] : null;

	// $transaction_id = 'MERCHANT' . rand( 100000, 999999 ); // Transaction ID to track and identify the transaction, make sure to save this in your database.
	$phone_pay_settings = DNM_Config::get_phone_pay_settings();
	if ( empty( $phone_pay_settings ) ) {
		throw new Exception( 'Phone pay settings are not configured properly.' );
	}
	$phonepe = PhonePe::init(
		$phone_pay_settings['phone_pay_merchant_id'], // Merchant ID
		$phone_pay_settings['phone_pay_merchant_user_id'], // Merchant User ID
		$phone_pay_settings['phone_pay_salt_key'], // Salt Key
		$phone_pay_settings['phone_pay_salt_index'], // Salt Index
		$phone_pay_settings['phone_pay_redirect_url'], // Redirect URL, can be defined on per transaction basis
		$phone_pay_settings['phone_pay_redirect_url'], // Callback URL, can be defined on per transaction basis
	);

	$success = $phonepe->standardCheckout()->isTransactionSuccessByTransactionId( $user_data['transactionID'] ); // Returns true if transaction is successful, false otherwise.

	if ( $success ) {
		global $wpdb;


		// check if transactionID already exists.
		$exists = DNM_Database::getRecord( DNM_ORDERS, 'transaction_id', $user_data['transactionID'] );

		// If transactionID does not exist, then proceed
		if ( ! $exists ) {
			$wpdb->query( 'BEGIN' );

			try {
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

				if ( $payment_type === 'membership' ) {
					// register WordPress user in user table with role 'dnm_member'
					$user_id = wp_insert_user(
						array(
							'user_login' => $user['email'],
							'user_pass'  => wp_generate_password(),
							'user_email' => $user['email'],
							'role'       => 'dnm_member',
						)
					);

					$customerData['user_id'] = $user_id;

					if ( is_wp_error( $user_id ) ) {
						throw new Exception( 'Failed to register user' );
					}
				}

				$customer_id = DNM_Database::insertIntoTable( DNM_CUSTOMERS, $customerData );

				if ( ! $customer_id ) {
					throw new Exception( 'Failed to insert customer data' );
				}

				$order_data = array(
					'order_id'       => DNM_Helper::getNextOrderId( $payment_type ),
					'transaction_id' => $user_data['transactionID'], // Corrected 'tnasaction_id' to 'transaction_id'
					'type'           => $payment_type,
					'payment_method' => 'Phonepe',
					'customer_id'    => $customer_id,
					'amount'         => $user['amount'],
					'created_at'     => current_time( 'mysql' ),
				);


				$order_id = DNM_Database::insertIntoTable( DNM_ORDERS, $order_data );



				if ( ! $order_id ) {
					throw new Exception( 'Failed to insert order data' );
				}

				$wpdb->query( 'COMMIT' );

				// check if email Notfications are enabled.
				$email_enable = DNM_Config::get_email_settings();
				if ( $email_enable['email_enable'] ) {
					// send email to customer.
					$email_tempates = DNM_Config::get_email_templates();
					$subject        = $email_tempates['payment_confirm_subject'];
					$body           = $email_tempates['payment_confirm_body'];

					$placeholders = array(
						'{name}'           => $user['name'],
						'{amount}'         => $user['amount'],
						'{transaction_id}' => $user_data['transactionID'],
					);

					$subject = strtr( $subject, $placeholders );
					$body    = strtr( $body, $placeholders );

					wp_mail( $user['email'], $subject, $body );
				}
			} catch ( Exception $e ) {
				$wpdb->query( 'ROLLBACK' ); // If any exception is thrown, rollback the transaction
				error_log( $e->getMessage() ); // Log the error message for debugging
			}
		}
		?>
		<div class="alert alert-success text-center" role="alert">
			<p>Your transaction was successful.</p>
		</div>
		<?php
	} else {
		?>
		<div class="alert alert-danger text-center" role="alert">
			<p>Your transaction was not successful. Please try again later.</p>
		</div>
		<?php
	}
} catch ( Exception $e ) {
	$wpdb->query( 'ROLLBACK' );
	// error_log($e->getMessage());
	// Handle exception here, e.g., show a user-friendly error message
	?>
	<div class="alert alert-danger text-center" role="alert">
		<p>There was an error processing your transaction. Please try again later.</p>
	</div>
	<?php
}
