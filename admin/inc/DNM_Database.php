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

		$sql = 'CREATE TABLE IF NOT EXISTS ' . DNM_CUSTOMERS . ' (
		ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		reference_id varchar(255) DEFAULT NULL,
		user_id bigint(20) DEFAULT NULL,
		name varchar(191) DEFAULT NULL,
		email varchar(191) DEFAULT NULL,
		phone varchar(191) DEFAULT NULL,
		city varchar(191) DEFAULT NULL,
		state varchar(191) DEFAULT NULL,
		Subscription_status varchar(191) DEFAULT "inactive",
		sub_id varchar(191) DEFAULT NULL,
		auth_id varchar(191) DEFAULT NULL,
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
		transaction_id varchar(255) DEFAULT NULL,
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

		self::insert_default_email_templates();
		self::insert_default_email_settings();
		self::insert_default_peyment_settings();

		self::add_dnm_member_role();
	}

	public static function dropTables() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_ORDERS );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_CUSTOMERS );
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_SETTINGS );
	}

	public static function deactivation() {
		// self::dropTables();
	}

	public static function uninstall() {
		// self::dropTables();
	}

	public static function add_dnm_member_role() {
		add_role(
			'dnm_member', // System name for the role.
			__( 'DNM Member' ), // Display name for the role.
			array(
				'read' => true, // True allows this capability.
				// Additional capabilities...
			)
		);
	}

	public static function insert_default_email_templates() {
		global $wpdb;

		// Define the default email templates
		$default_templates = array(
			'payment_confirm_subject' => 'Your payment has been confirmed',
			'payment_confirm_body'    => 'Thank you for your payment. Your payment has been confirmed.',
			// Add more default templates as needed
		);

		// Prepare the data for the database
		$email_data = array(
			'option_name'  => 'email_templates',
			'option_value' => maybe_serialize( $default_templates ),
			'autoload'     => 'yes',
		);

		// Check if the email templates already exist
		$existing = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'email_templates' ) );

		if ( ! $existing ) {
			// If the templates don't exist, insert a new row
			$result = $wpdb->insert( DNM_SETTINGS, $email_data );
			if ( false === $result ) {
				throw new Exception( 'Failed to insert email templates.' );
			}
		}
	}

	public static function insert_default_email_settings() {

		global $wpdb;
		$email_settings = array( 'email_enable' => 0 );

		// Prepare the data for the database
		$email_data = array(
			'option_name'  => 'email_settings',
			'option_value' => maybe_serialize( $email_settings ),
			'autoload'     => 'yes',
		);

		// Check if the email templates already exist
		$existing = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'email_settings' ) );

		if ( ! $existing ) {
			// If the settings don't exist, insert a new row
			$result = $wpdb->insert( DNM_SETTINGS, $email_data );
			if ( false === $result ) {
				throw new Exception( 'Failed to insert email settings.' );
			}
		}
	}

	public static function insert_default_peyment_settings() {

		global $wpdb;
		$payment_settings = array(
			'phone_pay_enable'           => 0,
			'phone_pay_mode'             => '',
			'phone_pay_redirect_url'     => '',
			'phone_pay_merchant_id'      => '',
			'phone_pay_merchant_user_id' => '',
			'phone_pay_salt_key'         => '',
			'phone_pay_salt_index'       => '',
		);

		// Prepare the data for the database
		$email_data = array(
			'option_name'  => 'phone_pay_settings',
			'option_value' => maybe_serialize( $payment_settings ),
			'autoload'     => 'yes',
		);

		// Check if the email templates already exist
		$existing = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . DNM_SETTINGS . ' WHERE option_name = %s', 'phone_pay_settings' ) );

		if ( ! $existing ) {
			// If the settings don't exist, insert a new row
			$result = $wpdb->insert( DNM_SETTINGS, $email_data );
			if ( false === $result ) {
				throw new Exception( 'Failed to insert phone settings.' );
			}
		}
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

	public static function updateTable( $table, $data, $where ) {
		global $wpdb;
		$result = $wpdb->update( $table, $data, $where );

		if ( false === $result ) {
			throw new Exception( 'Failed to update data in ' . $table );
		}

		return $result;
	}

	public static function getRecord( $table, $column, $value ) {
		global $wpdb;
		$record = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $table . ' WHERE ' . $column . ' = %s', $value ) );
		return $record;
	}

	public static function getRecordCount( $table, $column, $value ) {
		global $wpdb;
		$record = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table . ' WHERE ' . $column . ' = %s', $value ) );
		return $record;
	}

	public static function getRecords( $table, $column, $value ) {
		global $wpdb;
		$records = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $table . ' WHERE ' . $column . ' = %s', $value ) );
		return $records;
	}

	public static function getReferencedCustomers( $reference_id ) {

		global $wpdb;
		$customers = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . DNM_CUSTOMERS . ' WHERE reference_id = %s', $reference_id ) );
		if ( $customers ) {
			foreach ( $customers as $customer ) {
				$customer->orders = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . DNM_ORDERS . ' WHERE customer_id = %d', $customer->ID ) );
			}
		}
		return $customers;
	}
}
