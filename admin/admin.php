<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Menu.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/DNM_Order.php';

add_action( 'admin_menu', array( 'DNM_Menu', 'create_menu' ) );

add_action( 'wp_ajax_dnm-fetch-orders', array( 'DNM_Order', 'fetch_orders' ) );
add_action( 'wp_ajax_dnm_save_order', array( 'DNM_Order', 'save_order' ) );
