<?php
defined( 'ABSPATH' ) || die();

$action = $_GET['action'] ?? '';
$action = $action ? sanitize_text_field( $action ) : '';

if ( 'save' === $action ) {
	require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/save.php';
} elseif ( 'invoice' === $action ) {
	require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/invoice.php';
}elseif ( 'reference' === $action ) {
	require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/reference.php';
} else {
	require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/index.php';
}
