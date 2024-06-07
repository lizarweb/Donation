<?php
defined( 'ABSPATH' ) || die();
require_once DNM_PLUGIN_DIR_PATH . 'includes/constants.php';
require_once DNM_PLUGIN_DIR_PATH . 'public/inc/DNM_Language.php';
require_once DNM_PLUGIN_DIR_PATH . 'public/inc/DNM_Shortcode.php';
require_once DNM_PLUGIN_DIR_PATH . 'public/inc/DNM_Registration.php';

// Load translation.
add_action( 'plugins_loaded', array( 'DNM_Language', 'load_translation' ) );

// Add shortcode.
add_shortcode( 'donation_registration_form', array( 'DNM_Shortcode', 'custom_registration' ) );
add_shortcode( 'fixed_registration_form', array( 'DNM_Shortcode', 'fixed_registration' ) );
add_shortcode( 'member_registration_form', array( 'DNM_Shortcode', 'member_registration' ) );
add_shortcode( 'payment_status', array( 'DNM_Shortcode', 'payment_status' ) );
add_shortcode( 'donation_account', array( 'DNM_Shortcode', 'donation_account' ) );

// Save custom registration form.
add_action( 'wp_ajax_dnm_save_custom_registration_form', array( 'DNM_Registration', 'save_custom_registration_form' ) );
add_action( 'wp_ajax_nopriv_dnm_save_custom_registration_form', array( 'DNM_Registration', 'save_custom_registration_form' ) );

add_action( 'wp_ajax_dnm_save_fixed_registration_form', array( 'DNM_Registration', 'save_fixed_registration_form' ) );
add_action( 'wp_ajax_nopriv_dnm_save_fixed_registration_form', array( 'DNM_Registration', 'save_fixed_registration_form' ) );

add_action( 'wp_ajax_dnm_save_membership_registration_form', array( 'DNM_Registration', 'save_membership_registration_form' ) );
add_action( 'wp_ajax_nopriv_dnm_save_membership_registration_form', array( 'DNM_Registration', 'save_membership_registration_form' ) );


add_action( 'wp_ajax_nopriv_dnm_subscription_form', array( 'DNM_Registration', 'activate_subscription' ) );
add_action( 'wp_ajax_dnm_subscription_form', array( 'DNM_Registration', 'activate_subscription' ) );

add_action( 'wp_ajax_nopriv_dnm_verify_form', array( 'DNM_Registration', 'verify_subscription' ) );
add_action( 'wp_ajax_dnm_verify_form', array( 'DNM_Registration', 'verify_subscription' ) );

function start_session() {
    if(!session_id()) {
        session_start();
    }
}

add_action('init', 'start_session', 1);