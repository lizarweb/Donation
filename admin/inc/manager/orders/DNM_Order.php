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
					DNM_Helper::get_prefix().$order->ID,
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
}
