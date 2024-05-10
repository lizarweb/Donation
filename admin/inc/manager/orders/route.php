<?php
defined( 'ABSPATH' ) || die();

$action = $_GET['action'] ?? '';
$action = $action ? sanitize_text_field( $action ) : '';

if ( 'save' === $action ) {
	require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/save.php';
} else {
	require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/manager/orders/index.php';
}
