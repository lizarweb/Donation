<?php
defined( 'ABSPATH' ) || die();

global $wpdb;

// Tables Names.
define( 'DNM_USERS', $wpdb->base_prefix . 'users' );
define( 'DNM_POSTS', $wpdb->prefix . 'posts' );

define( 'DNM_PAYMENTS', $wpdb->base_prefix . 'payments' );

/* Menu page slugs for manager */
define( 'DNM_DASHBOARD', 'donation-dash' );
define( 'DNM_ORDERS_PAGE', 'donation-orders' );
define( 'DNM_SETTING_PAGE', 'donation-settings' );


define( 'DNM_ADMIN_CAPABILITY', 'manage_options' );
