<?php
defined( 'ABSPATH' ) || die();

require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';
require_once DNM_PLUGIN_DIR_PATH . 'admin/inc/DNM_Menu.php';

add_action( 'admin_menu', array( 'DNM_Menu', 'create_menu' ) );
