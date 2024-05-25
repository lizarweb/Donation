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

		$query = 'SELECT o.ID, o.order_id, o.transaction_id, c.ID as reference , c.reference_id as referenced_by , o.type, o.payment_method, c.name, c.email, c.phone, o.amount, o.created_at, o.updated_at FROM ' . DNM_ORDERS . ' as o
            INNER JOIN ' . DNM_CUSTOMERS . ' as c
            ON o.customer_id = c.ID WHERE o.type = "11000"';

		if ( $search ) {
			$query .= " AND (c.reference_id LIKE '%{$search}%' OR o.transaction_id LIKE '%{$search}%' OR c.name LIKE '%{$search}%' OR c.email LIKE '%{$search}%' OR c.phone LIKE '%{$search}%' OR o.amount LIKE '%{$search}%' OR DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') LIKE '%{$search}%')";
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
					'<strong>' . DNM_Helper::get_prefix() . $order->order_id . '</strong>',
					$order->name,
					'<a href="mailto:' . $order->email . '">' . $order->email . '</a>',
					$order->phone,
					'<strong>' . DNM_Config::get_amount_text( $order->amount ) . '</strong>',
					DNM_Config::date_format_text( $order->created_at ),
					$order->created_at ? DNM_Config::date_format_text( $order->updated_at ) : '<span class="badge bg-danger">N/A</span>',
					$order->payment_method ? '<span class="badge bg-info">' . $order->payment_method . '</span>' : '<span class="badge bg-secondary">N/A</span>',
					$order->transaction_id ? $order->transaction_id : '-',
					'MP' . $order->reference ? 'MP' . $order->reference : '-',
					$order->referenced_by ? $order->referenced_by : '-',
					'<div class="btn-group" role="group" aria-label="Basic example">
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=save&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-fill"></i></a>
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=reference&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-people-fill"></i></a>
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=invoice&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-receipt"></i></a>
						<button class="btn btn-sm btn-outline-danger delete-order" data-id="' . $order->ID . '" data-nonce="' . wp_create_nonce( 'dnm_delete_order' ) . '"><i class="bi bi-trash-fill"></i></button>
					</div>',
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

	public static function fetch_custom_orders() {
		global $wpdb;
		$response = array();
		$limit    = isset( $_POST['length'] ) ? intval( $_POST['length'] ) : 25;
		$start    = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 0;
		$order    = isset( $_POST['order'] ) ? intval( $_POST['order'][0]['column'] ) : 0;
		$dir      = isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'][0]['dir'] ) : 'desc';
		$search   = isset( $_POST['search'] ) ? esc_sql( $_POST['search']['value'] ) : '';

		$offset = $start;
		$limit  = $limit;

		$query = 'SELECT o.ID, o.order_id, o.transaction_id, o.type, o.payment_method, c.name, c.email, c.phone, o.amount, o.created_at, o.updated_at FROM ' . DNM_ORDERS . ' as o
            INNER JOIN ' . DNM_CUSTOMERS . ' as c
            ON o.customer_id = c.ID WHERE o.type = "custom"';

		if ( $search ) {
			$query .= " AND (c.name LIKE '%{$search}%' OR o.transaction_id LIKE '%{$search}%' OR c.email LIKE '%{$search}%' OR c.phone LIKE '%{$search}%' OR o.amount LIKE '%{$search}%' OR DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') LIKE '%{$search}%')";
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
					'<strong>' . DNM_Helper::get_prefix() . $order->order_id . '</strong>',
					$order->name,
					'<a href="mailto:' . $order->email . '">' . $order->email . '</a>',
					$order->phone,
					'<strong>' . DNM_Config::get_amount_text( $order->amount ) . '</strong>',
					DNM_Config::date_format_text( $order->created_at ),
					$order->created_at ? DNM_Config::date_format_text( $order->updated_at ) : '<span class="badge bg-danger">N/A</span>',
					$order->payment_method ? '<span class="badge bg-info">' . $order->payment_method . '</span>' : '<span class="badge bg-secondary">N/A</span>',
					$order->transaction_id ? $order->transaction_id : '-',
					'<div class="btn-group" role="group" aria-label="Basic example">
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=save&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-fill"></i></a>
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=invoice&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye-fill"></i></a>
						<button class="btn btn-sm btn-outline-danger delete-order" data-id="' . $order->ID . '" data-nonce="' . wp_create_nonce( 'dnm_delete_order' ) . '"><i class="bi bi-trash-fill"></i></button>
					</div>',
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

	public static function fetch_memberships_orders() {
		global $wpdb;
		$response = array();
		$limit    = isset( $_POST['length'] ) ? intval( $_POST['length'] ) : 25;
		$start    = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 0;
		$order    = isset( $_POST['order'] ) ? intval( $_POST['order'][0]['column'] ) : 0;
		$dir      = isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'][0]['dir'] ) : 'desc';
		$search   = isset( $_POST['search'] ) ? esc_sql( $_POST['search']['value'] ) : '';

		$offset = $start;
		$limit  = $limit;

		$query = 'SELECT o.ID, o.order_id, o.transaction_id, o.type, o.payment_method, c.ID as reference , c.reference_id as referenced_by, c.name, c.email, c.phone, o.amount, o.created_at, o.updated_at FROM ' . DNM_ORDERS . ' as o
            INNER JOIN ' . DNM_CUSTOMERS . ' as c
            ON o.customer_id = c.ID WHERE o.type = "membership"';

		if ( $search ) {
			$query .= " AND (c.reference_id LIKE '%{$search}%' OR c.name LIKE '%{$search}%' OR o.transaction_id LIKE '%{$search}%' OR c.email LIKE '%{$search}%' OR c.phone LIKE '%{$search}%' OR o.amount LIKE '%{$search}%' OR DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') LIKE '%{$search}%')";
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
					'<strong>' . DNM_Helper::get_prefix() . $order->order_id . '</strong>',
					$order->name,
					'<a href="mailto:' . $order->email . '">' . $order->email . '</a>',
					$order->phone,
					'<strong>' . DNM_Config::get_amount_text( $order->amount ) . '</strong>',
					DNM_Config::date_format_text( $order->created_at ),
					$order->created_at ? DNM_Config::date_format_text( $order->updated_at ) : '<span class="badge bg-danger">N/A</span>',
					$order->payment_method ? '<span class="badge bg-info">' . $order->payment_method . '</span>' : '<span class="badge bg-secondary">N/A</span>',
					$order->transaction_id ? $order->transaction_id : '-',
					'MP' . $order->reference ? 'MP' . $order->reference : '-',
					$order->referenced_by ? $order->referenced_by : '-',
					'<div class="btn-group" role="group" aria-label="Basic example">
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=save&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-fill"></i></a>
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=reference&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-people-fill"></i></a>
						<a href="' . DNM_Helper::get_page_url( 'donation-orders' ) . '&action=invoice&id=' . $order->ID . '" class="btn btn-sm btn-outline-secondary"><i class="bi bi-receipt"></i></a>
						<button class="btn btn-sm btn-outline-danger delete-order" data-id="' . $order->ID . '" data-nonce="' . wp_create_nonce( 'dnm_delete_order' ) . '"><i class="bi bi-trash-fill"></i></button>
					</div>',
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

			$fields = array(
				'order_id'       => array(
					'default' => 0,
					'filter'  => 'intval',
				),
				'name'           => array(
					'default' => '',
					'filter'  => 'sanitize_text_field',
				),
				'email'          => array(
					'default' => '',
					'filter'  => 'sanitize_email',
				),
				'phone'          => array(
					'default' => '',
					'filter'  => 'sanitize_text_field',
				),
				'city'           => array(
					'default' => '',
					'filter'  => 'sanitize_text_field',
				),
				'state'          => array(
					'default' => '',
					'filter'  => 'sanitize_text_field',
				),
				'address'        => array(
					'default' => '',
					'filter'  => 'sanitize_text_field',
				),
				'amount'         => array(
					'default' => 0,
					'filter'  => 'floatval',
				),
				'payment_method' => array(
					'default' => '',
					'filter'  => 'sanitize_text_field',
				),
				'type'           => array(
					'default' => 'custom',
					'filter'  => 'sanitize_text_field',
				),
			);

			$data = self::get_post_values( $fields );

			$errors = DNM_Helper::validate_fields( $fields, $data, array( 'order_id', 'address', 'payment_method', 'city', 'state' ) );

			global $wpdb;

			try {
				$wpdb->query( 'BEGIN' );

				$customer_id = $wpdb->get_var( $wpdb->prepare( 'SELECT ID FROM ' . DNM_CUSTOMERS . ' WHERE email = %s', $data['email'] ) );

				// if ( $customer_id && $data['order_id'] == 0 ) {
				// $errors['email'] = 'Email already exists. Please use another email.';
				// } else {
					$customerData = array(
						'name'       => $data['name'],
						'email'      => $data['email'],
						'phone'      => $data['phone'],
						'city'       => $data['city'],
						'state'      => $data['state'],
						'address'    => $data['address'],
						'created_at' => current_time( 'mysql' ),
					);

					if ( $customer_id ) {
						// Update the customer data
						$customerData['updated_at'] = current_time( 'mysql' );
						$update_result              = DNM_Database::updateTable( DNM_CUSTOMERS, $customerData, array( 'ID' => $customer_id ) );

						if ( false === $update_result ) {
							throw new Exception( 'Failed to update customer.' );
						}
					} else {
						// Insert new customer data
						$customer_id = DNM_Database::insertIntoTable( DNM_CUSTOMERS, $customerData );

						if ( false === $customer_id ) {
							throw new Exception( 'Failed to save customer.' );
						}
					}
					// }

					if ( ! empty( $errors ) ) {
						wp_send_json_error( $errors );
					}

					$order_data = array(
						'order_id'       => $data['order_id'],
						'type'           => $data['type'],
						'payment_method' => $data['payment_method'],
						'customer_id'    => $customer_id,
						'amount'         => $data['amount'],
						'label'          => $data['payment_method'],
						'created_at'     => current_time( 'mysql' ),
					);

					if ( $data['order_id'] != 0 ) {
						// Update the order data
						unset( $order_data['type'] );
						$order_data['updated_at'] = current_time( 'mysql' );
						$update_result            = DNM_Database::updateTable( DNM_ORDERS, $order_data, array( 'order_id' => $data['order_id'] ) );
						$message                  = 'Order has been updated successfully.';
						if ( false === $update_result ) {
							throw new Exception( 'Failed to update order.' );
						}
					} else {
						// Insert new order data
						$last_order_id          = DNM_Helper::getNextOrderId();
						$order_data['order_id'] = $last_order_id;
						$order_id               = DNM_Database::insertIntoTable( DNM_ORDERS, $order_data );
						$message                = 'Order has been saved successfully.';
						if ( ! $order_id ) {
							throw new Exception( 'Failed to save order.' );
						}
					}

					$wpdb->query( 'COMMIT' );
			} catch ( Exception $e ) {

				$wpdb->query( 'ROLLBACK' );

				// Handle the error
				self::handle_errors( array( 'message' => $e->getMessage() ) );
			}

			wp_send_json_success( array( 'message' => $message ) );

		} catch ( Exception $e ) {
			self::handle_errors( array( 'message' => $e->getMessage() ) );
		}
	}

	public static function delete_order() {
		try {
			check_ajax_referer( 'dnm_delete_order', 'nonce' );
			$order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0;

			if ( $order_id == 0 ) {
				throw new Exception( 'Invalid order id.' );
			}

			global $wpdb;
			$delete_result = $wpdb->delete( DNM_ORDERS, array( 'ID' => $order_id ) );

			if ( false === $delete_result ) {
				throw new Exception( 'Failed to delete order.' );
			}

			wp_send_json_success( array( 'message' => 'Order has been deleted successfully.' ) );

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}

	private static function get_post_values( $fields ) {
		$data = array();
		foreach ( $fields as $key => $options ) {
			$data[ $key ] = DNM_Helper::get_post_value( $key, $options['default'], $options['filter'] );
		}
		return $data;
	}

	private static function handle_errors( $errors ) {
		$errors['message'] = 'There were errors in your submission. Please correct them and try again.';
		wp_send_json_error( $errors );
		exit;
	}

	public static function get_order( $order_id ) {
		global $wpdb;
		$query = 'SELECT o.ID, c.ID as reference_code, o.order_id, o.type, o.payment_method, c.name, c.email, c.phone, c.city, c.reference_id, c.state, c.address, o.amount, o.created_at FROM ' . DNM_ORDERS . ' as o
		  			INNER JOIN ' . DNM_CUSTOMERS . ' as c
		  			ON o.customer_id = c.ID
		  			WHERE o.ID = %d';
		$order = $wpdb->get_row( $wpdb->prepare( $query, $order_id ) );
		return $order;
	}

	public static function get_orders_count( $type ) {
		global $wpdb;
		$query = 'SELECT COUNT(*) FROM ' . DNM_ORDERS . ' WHERE type = %s';
		$count = $wpdb->get_var( $wpdb->prepare( $query, $type ) );
		return $count;
	}

	public static function get_customers_count() {
		global $wpdb;
		$query = 'SELECT COUNT(*) FROM ' . DNM_CUSTOMERS;
		$count = $wpdb->get_var( $query );
		return $count;
	}

	public static function get_customers_by_reference_id( $reference_id ) {
		global $wpdb;
		$query     = 'SELECT * FROM ' . DNM_CUSTOMERS . ' WHERE reference_id = %s';
		$customers = $wpdb->get_results( $wpdb->prepare( $query, $reference_id ) );
		return $customers;
	}
}
