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

		// Create payments table
		$sql = 'CREATE TABLE IF NOT EXISTS ' . DNM_PAYMENTS . ' (
        ID bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        label varchar(191) DEFAULT NULL,
        created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at timestamp NULL DEFAULT NULL,
        PRIMARY KEY (ID)
        ) ENGINE=InnoDB ' . $charset_collate;
		dbDelta( $sql );
	}

	public static function deactivation() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_PAYMENTS );
	}

	public static function uninstall() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . DNM_PAYMENTS );
	}
}
