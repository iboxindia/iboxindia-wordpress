<?php
/**
 * Plugin Name: Iboxindia Worpress
 * Plugin URI: https://iboxindia.com/
 * Description: Plugin to configure sites for clients of IboxIndia. Kindly contact info@iboxindia.com for more information.
 * Version: 1.0.0
 * Author: IboxIndia
 * Author URI: https://iboxindia.com
 * Text Domain: iboxindia
 *
 * @package Iboxindia
 */

/**
 * Set constants.
 */
if ( ! defined( 'IBX_WP_NAME' ) ) {
	define( 'IBX_WP_NAME', __( 'IboxIndia', 'iboxindia' ) );
}

if ( ! defined( 'IBX_WP_VER' ) ) {
	define( 'IBX_WP_VER', '2.4.0' );
}

if ( ! defined( 'IBX_WP_FILE' ) ) {
	define( 'IBX_WP_FILE', __FILE__ );
}

if ( ! defined( 'IBX_WP_BASE' ) ) {
	define( 'IBX_WP_BASE', plugin_basename( IBX_WP_FILE ) );
}

if ( ! defined( 'IBX_WP_DIR' ) ) {
	define( 'IBX_WP_DIR', plugin_dir_path( IBX_WP_FILE ) );
}

if ( ! defined( 'IBX_WP_URI' ) ) {
	define( 'IBX_WP_URI', plugins_url( '/', IBX_WP_FILE ) );
}

if ( ! function_exists( 'ibx_wp_setup' ) ) :

	/**
	 * Iboxindia Wordpress Setup
	 *
	 * @since 1.0.5
	 */
	function ibx_wp_setup() {
		// require_once IBX_WP_DIR . 'inc/classes/class-ibx-wp.php';

		// Admin.
		require_once IBX_WP_DIR . 'classes/class-ibx-wp-admin.php';
	}

	add_action( 'plugins_loaded', 'ibx_wp_setup' );

endif;

// Ibox Notices.
require_once IBX_WP_DIR . 'admin/ibx-notices/class-ibx-notices.php';