<?php
defined( 'ABSPATH' ) || die();
require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';

class DNM_Database {

	public static function activation() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$wpdb->query( 'ALTER TABLE ' . DNM_USERS . ' ENGINE = InnoDB' );
		$wpdb->query( 'ALTER TABLE ' . DNM_POSTS . ' ENGINE = InnoDB' );

		// Create Customer table.
		$sql = 'CREATE TABLE IF NOT EXISTS ' . DNM_CUSTOMERS . ' (
		ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		name varchar(191) DEFAULT NULL,
		email varchar(191) DEFAULT NULL,
		phone varchar(191) DEFAULT NULL,
		address text DEFAULT NULL,
		created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at timestamp NULL DEFAULT NULL,
		PRIMARY KEY (ID)
		) ENGINE=InnoDB ' . $charset_collate;
		dbDelta( $sql );

		// Create Settings table.
		$sql = 'CREATE TABLE IF NOT EXISTS ' . DNM_SETTINGS . ' (
		ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		option_name varchar(191) DEFAULT NULL,
		option_value longtext DEFAULT NULL,
		autoload varchar(20) DEFAULT NULL,
		PRIMARY KEY (ID)
		) ENGINE=InnoDB ' . $charset_collate;
		dbDelta( $sql );

		// Create orders table
		$sql = 'CREATE TABLE IF NOT EXISTS ' . DNM_ORDERS . ' (
		ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		order_id bigint(20) UNSIGNED DEFAULT NULL,
		type varchar(50) DEFAULT NULL,
		payment_method varchar(50) DEFAULT NULL,
		customer_id bigint(20) UNSIGNED DEFAULT NULL,
		amount decimal(10,2) DEFAULT NULL,
		label varchar(191) DEFAULT NULL,
		created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at timestamp NULL DEFAULT NULL,
		PRIMARY KEY (ID),
		FOREIGN KEY (customer_id) REFERENCES ' . DNM_CUSTOMERS . '(ID)
		) ENGINE=InnoDB ' . $charset_collate;
		dbDelta( $sql );

		// self::insertRandomData();
	}

	public static function dropTables() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_ORDERS );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_CUSTOMERS );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_SETTINGS );
	}


	// public static function insertRandomData() {
	// global $wpdb;
	// for ( $i = 0; $i < 10000; $i++ ) {
	// Generate random data for customer
	// $wpdb->insert(
	// DNM_CUSTOMERS,
	// array(
	// 'name'    => 'Name' . $i,
	// 'email'   => 'email' . $i . '@example.com',
	// 'phone'   => '123-456-' . wp_rand( 1000, 9999 ),
	// 'address' => 'Address ' . $i,
	// )
	// );

	// $customer_id = $wpdb->insert_id;
	// Generate random data for order
	// $wpdb->insert(
	// DNM_ORDERS,
	// array(
	// 'ID'          => $i,
	// 'order_id'    => $i,
	// 'type'        => 'type' . wp_rand( 1, 5 ),
	// 'customer_id' => $customer_id,
	// 'amount'      => wp_rand( 1, 1000 ),
	// 'label'       => 'label' . wp_rand( 1, 5 ),
	// 'created_at'  => current_time( 'mysql' ),
	// 'updated_at'  => current_time( 'mysql' ),
	// )
	// );
	// }
	// }


	public static function deactivation() {
		self::dropTables();
	}

	public static function uninstall() {
		self::dropTables();
	}

	public static function insertIntoTable( $table, $data ) {
		global $wpdb;
		$wpdb->insert( $table, $data );
		$id = $wpdb->insert_id;
		if ( false === $id ) {
			throw new Exception( 'Failed to insert data into ' . $table );
		}
		return $id;
	}
}
