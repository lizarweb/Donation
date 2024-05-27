<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Config.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/helpers/DNM_Helper.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Database.php';
require_once DNM_PLUGIN_DIR_PATH . 'includes/vendor/autoload.php';

use PhonePe\PhonePe;

$user_data = get_transient( 'user_data' ); // Transaction ID

if ( $user_data ) {
	$user                  = $user_data['user'];
	$payment_type          = isset( $user['payment_type'] ) ? $user['payment_type'] : 'membership';
	$reference_id          = isset( $user['reference_id'] ) ? $user['reference_id'] : null;
	$merchantTransactionId = $user_data['transactionID'];
	$phone_pay_settings    = DNM_Config::get_phone_pay_settings();
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
        $phone_pay_settings['phone_pay_mode']
	);

	$response_success = $phonepe->standardCheckout()->isTransactionSuccessByTransactionId( $merchantTransactionId );

	if ( $response_success ) {
		// check if transactionID already exists.
		$exists_order = DNM_Database::getRecord( DNM_ORDERS, 'transaction_id', $merchantTransactionId );

		// If transactionID does not exist, then proceed
		if ( ! $exists_order ) {
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

				if ( $payment_type === 'membership' ) {
					$customer_exits = DNM_Database::getRecord( DNM_CUSTOMERS, 'email', $user['email'] );
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

				?>
				<div class="alert alert-success text-center" role="alert">
					<p>Your transaction was successful. Thank you for your payment.</p>
				</div>
				<?php

				// Clear the transient
				delete_transient( 'user_data' );

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
		}
	} else {
		?>
		<div class="alert alert-danger text-center" role="alert">
			<p>Your transaction was not successful. Please try again later.</p>
		</div>
		<?php
	}
} else {
	?>
	<div class="alert alert-success text-center" role="alert">
		<p>Your transaction was successful. Thank you for your payment.</p>
	</div>
	<?php
}
