<?php
defined( 'ABSPATH' ) || die();

global $wpdb;

// Tables Names.
define( 'DNM_USERS', $wpdb->base_prefix . 'users' );
define( 'DNM_POSTS', $wpdb->prefix . 'posts' );

define( 'DNM_ORDERS', $wpdb->base_prefix . 'dnm_orders' );
define( 'DNM_CUSTOMERS', $wpdb->base_prefix . 'dnm_customers' );
define( 'DNM_SETTINGS', $wpdb->base_prefix . 'dnm_settings' );

/* Menu page slugs for manager */
define( 'DNM_DASHBOARD', 'donation-dash' );
define( 'DNM_ORDERS_PAGE', 'donation-orders' );
define( 'DNM_CUSTOM_ORDERS_PAGE', 'donation-custom-orders' );
define( 'DNM_MEMBERSHIPS_ORDERS_PAGE', 'donation-memberships-orders' );
define( 'DNM_SETTING_PAGE', 'donation-settings' );


define( 'DNM_ADMIN_CAPABILITY', 'manage_options' );
