<?php
defined('ABSPATH') || die();

$action = $_GET['action'] ?? '';
$action = $action ? sanitize_text_field($action) : '';

if ('view' === $action) {
    require_once DNM_PLUGIN_DIR_PATH . 'public/inc/account/invoice.php';
} else {
    require_once DNM_PLUGIN_DIR_PATH . 'public/inc/account/donation_account.php';
}
