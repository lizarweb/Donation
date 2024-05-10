<?php
defined( 'ABSPATH' ) || die();
require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';
require_once DNM_PLUGIN_DIR_PATH . 'public/inc/DNM_Language.php';
require_once DNM_PLUGIN_DIR_PATH . 'public/inc/DNM_Shortcode.php';

// Load translation.
// add_action( 'plugins_loaded', array( 'DNM_Language', 'load_translation' ) );

// Add shortcode.
add_shortcode( 'school_management_registration', array( 'WLSM_Shortcode', 'registration' ) );