<?php
defined( 'ABSPATH' ) || die();

class DNM_Language {
	public static function load_translation() {
		load_plugin_textdomain( 'donation', false, basename( DNM_PLUGIN_DIR_PATH ) . '/languages' );
	}
}
