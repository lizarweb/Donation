<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Menu.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/DNM_Order.php';

add_action( 'admin_menu', array( 'DNM_Menu', 'create_menu' ) );

add_action( 'wp_ajax_dnm-fetch-orders', array( 'DNM_Order', 'fetch_orders' ) );
add_action( 'wp_ajax_dnm-fetch-custom-orders', array( 'DNM_Order', 'fetch_custom_orders' ) );
add_action( 'wp_ajax_dnm-fetch-memberships-orders', array( 'DNM_Order', 'fetch_memberships_orders' ) );
add_action( 'wp_ajax_dnm-save-order', array( 'DNM_Order', 'save_order' ) );
add_action( 'wp_ajax_dnm-delete-order', array( 'DNM_Order', 'delete_order' ) );

add_action( 'wp_ajax_dnm-save-general-settings', array( 'DNM_Config', 'save_general_settings' ) );
add_action( 'wp_ajax_dnm-save-payment-settings', array( 'DNM_Config', 'save_payment_settings' ) );
add_action( 'wp_ajax_dnm-save-email-settings', array( 'DNM_Config', 'save_email_settings' ) );
add_action( 'wp_ajax_dnm-save-email-template-settings', array( 'DNM_Config', 'save_email_template_settings' ) );
