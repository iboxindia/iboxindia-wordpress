<?php

require_once IBX_WP_PLUGIN_DIR . '/includes/functions.php';

if ( is_admin() ) {
	require_once IBX_WP_PLUGIN_DIR . '/admin/admin.php';
}

class IBX_WP {

	public static function load_modules() {
		// self::load_module( 'themes' );
		// self::load_module( 'plugins' );
	}

	protected static function load_module( $mod ) {
		$dir = IBX_WP_PLUGIN_MODULES_DIR;

		if ( empty( $dir ) or ! is_dir( $dir ) ) {
			return false;
		}

		$files = array(
			path_join( $dir, $mod . '/' . $mod . '.php' ),
			path_join( $dir, $mod . '.php' ),
		);

		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				include_once $file;
				return true;
			}
		}

		return false;
	}

	public static function get_option( $name, $default = false ) {
		$option = get_option( 'ibx_wp_plugin' );

		if ( false === $option ) {
			return $default;
		}

		if ( isset( $option[$name] ) ) {
			return $option[$name];
		} else {
			return $default;
		}
	}

	public static function update_option( $name, $value ) {
		$option = get_option( 'ibx_wp_plugin' );
		$option = ( false === $option ) ? array() : (array) $option;
		$option = array_merge( $option, array( $name => $value ) );
		update_option( 'ibx_wp_plugin', $option );
	}
}

add_action( 'plugins_loaded', 'ibx_wp', 10, 0 );

function ibx_wp() {
	IBX_WP::load_modules();
}

add_action( 'init', 'ibx_wp_init', 10, 0 );

function ibx_wp_init() {
	do_action( 'ibx_wp_init' );
}

add_action( 'admin_init', 'ibx_wp_upgrade', 10, 0 );

function ibx_wp_upgrade() {
	$old_ver = IBX_WP::get_option( 'version', '0' );
	$new_ver = IBX_WP_PLUGIN_VER;

	if ( $old_ver == $new_ver ) {
		return;
	}

	do_action( 'ibx_wp_upgrade', $new_ver, $old_ver );

	IBX_WP::update_option( 'version', $new_ver );
}

/* Install and default settings */

add_action( 'activate_' . IBX_WP_PLUGIN_BASENAME, 'ibx_wp_install', 10, 0 );

function ibx_wp_install() {
	if ( $opt = get_option( 'ibx_wp' ) ) {
		return;
	}

	ibx_wp_upgrade();
}
