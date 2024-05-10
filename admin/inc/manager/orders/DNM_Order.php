<?php
defined( 'ABSPATH' ) || die();
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Config.php';
require_once DNM_PLUGIN_DIR_PATH . '/includes/helpers/DNM_Helper.php';


class DNM_Order {

	public static function fetch_orders() {
		global $wpdb;
		$response = array();
		$limit    = isset( $_POST['length'] ) ? intval( $_POST['length'] ) : 25;
		$start    = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 0;
		$order    = isset( $_POST['order'] ) ? intval( $_POST['order'][0]['column'] ) : 0;
		$dir      = isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'][0]['dir'] ) : 'desc';
		$search   = isset( $_POST['search'] ) ? esc_sql( $_POST['search']['value'] ) : '';

		$offset = $start;
		$limit  = $limit;

		$query = 'SELECT o.ID, c.name, c.email, c.phone, o.amount, o.created_at FROM ' . DNM_ORDERS . ' as o
		  			INNER JOIN ' . DNM_CUSTOMERS . ' as c
		  			ON o.customer_id = c.ID';
		if ( $search ) {
			$query .= " WHERE c.name LIKE '%{$search}%' OR c.email LIKE '%{$search}%' OR c.phone LIKE '%{$search}%' OR o.amount LIKE '%{$search}%' OR DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') LIKE '%{$search}%'";
		}

		$total_query   = $query;
		$total_data    = $wpdb->get_results( $total_query );
		$total_records = count( $total_data );
		$query        .= " ORDER BY o.id {$dir} LIMIT {$offset}, {$limit}";
		$data          = $wpdb->get_results( $query );
		$orders        = array();

		if ( $data ) {
			foreach ( $data as $order ) {
				$orders[] = array(
					DNM_Helper::get_prefix() . $order->ID,
					$order->name,
					$order->email,
					$order->phone,
					DNM_Config::get_amount_text( $order->amount ),
					DNM_Config::get_amount_text( $order->amount ),
					DNM_Config::date_format_text( $order->created_at ),
				);
			}
			$response = array(
				'draw'            => intval( $_POST['draw'] ),
				'recordsTotal'    => intval( $total_records ),
				'recordsFiltered' => intval( $total_records ),
				'data'            => $orders,
			);
			echo wp_json_encode( $response );
			die;
		}
	}

	public static function save_order() {
		try {
			check_ajax_referer( 'dnm_save_order', 'nonce' );

			$order_id       = DNM_Helper::get_post_value( 'order_id', 0, 'intval' );
			$name           = DNM_Helper::get_post_value( 'name', '', 'sanitize_text_field' );
			$email          = DNM_Helper::get_post_value( 'email', '', 'sanitize_email' );
			$phone          = DNM_Helper::get_post_value( 'phone', '', 'sanitize_text_field' );
			$address        = DNM_Helper::get_post_value( 'address', '', 'sanitize_text_field' );
			$amount         = DNM_Helper::get_post_value( 'amount', 0, 'floatval' );
			$payment_method = DNM_Helper::get_post_value( 'payment_method', '', 'sanitize_text_field' );

			$errors = array();
			$fields = array( 'name'   => $name, 'email'  => $email, 'phone'  => $phone, 'amount' => $amount );
			$errors = self::validate_order_fields( $fields );

			global $wpdb;
			$customer_id = $wpdb->get_var( $wpdb->prepare( 'SELECT ID FROM ' . DNM_CUSTOMERS . ' WHERE email = %s', $email ) );

			if ( $customer_id ) {
				$errors['email'] = 'Email already exists. Please use another email.';
			} else {
				$customerData = array(
					'name'       => $name,
					'email'      => $email,
					'phone'      => $phone,
					'address'    => $address,
					'created_at' => current_time( 'mysql' ),
				);

				$customer_id = DNM_Database::insertIntoTable( DNM_CUSTOMERS, $customerData );

				if ( false === $customer_id ) {
					$errors['message'] = 'Failed to save customer.';
				}
			}

			if ( ! empty( $errors ) ) {
				$errors['message'] = 'There were errors in your submission. Please correct them and try again.';
				wp_send_json_error( $errors );
			}

			$order_data = array(
				'order_id'       => $order_id,
				'type'           => 'donation',
				'payment_method' => $payment_method,
				'customer_id'    => $customer_id,
				'amount'         => $amount,
				'label'          => $payment_method,
				'created_at'     => current_time( 'mysql' ),
			);

			$order_id = DNM_Database::insertIntoTable( DNM_ORDERS, $order_data );

			if ( ! $order_id ) {
				wp_send_json_error( array( 'message' => 'Failed to save order.' ) );
			}

			wp_send_json_success( array( 'message' => 'Order has been saved successfully.' ) );
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}

	public static function validate_order_fields( $fields ) {
		$errors = array();
		foreach ( $fields as $field => $value ) {
			$errors[ $field ] = DNM_Helper::validate_field( $value, ucfirst( $field ) . ' is required.' );
		}
		return array_filter( $errors );
	}
}
