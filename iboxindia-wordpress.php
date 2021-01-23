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

define( 'IBX_WP_PLUGIN_VER', '1.0.0' );

define( 'IBX_WP_REQUIRED_WP_VERSION', '5.4' );

define( 'IBX_WP_TEXT_DOMAIN', 'iboxindia' );

define( 'IBX_WP_PLUGIN', __FILE__ );

define( 'IBX_WP_PLUGIN_BASENAME', plugin_basename( IBX_WP_PLUGIN ) );

define( 'IBX_WP_PLUGIN_NAME', trim( dirname( IBX_WP_PLUGIN_BASENAME ), '/' ) );

define( 'IBX_WP_PLUGIN_DIR', untrailingslashit( dirname( IBX_WP_PLUGIN ) ) );

define( 'IBX_WP_PLUGIN_MODULES_DIR', IBX_WP_PLUGIN_DIR . '/modules' );

define( 'IBX_WP_PLUGIN_URL', untrailingslashit( plugins_url( '', IBX_WP_PLUGIN ) ) );

// Startup Notices.
require_once IBX_WP_PLUGIN_DIR . '/admin/ibx-notices/class-ibx-notices.php';

// 
require_once IBX_WP_PLUGIN_DIR . '/load.php';