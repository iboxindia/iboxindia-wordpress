<?php

// require_once WPCF7_PLUGIN_DIR . '/admin/includes/admin-functions.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/help-tabs.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/tag-generator.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/welcome-panel.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/config-validator.php';


add_action( 'admin_init', 'ibx_wp_admin_init', 10, 0 );

function ibx_wp_admin_init() {
	$settings = IBX_WP::get_option( "settings" );
	if ( empty( $settings ) ) {
		$settings = array(
			'hash' => '',
			'logged_in' => false,
			'timeout' => 30,
			'debug' => false,
		);
	}
	IBX_WP::update_option( "settings", $settings );
}

add_action( 'admin_menu', 'ibx_wp_admin_menu', 9, 0 );

function ibx_wp_enqueue_admin_style( $hook ) {
	// var_dump( $hook );
	wp_enqueue_style( 'ibx-wp-items', IBX_WP_PLUGIN_URL . '/assets/css/ibx-wp-items.css', array(), IBX_WP_PLUGIN_VER );

}

add_action( 'admin_enqueue_scripts', 'ibx_wp_enqueue_admin_style' ); 

function ibx_wp_admin_menu() {
	do_action( 'ibx_wp_admin_menu' );

	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_menu_page(  IBX_WP_PLUGIN_NAME, 'Iboxindia', 'administrator', IBX_WP_PLUGIN_NAME, 'displayPluginAdminDashboard', 'dashicons-admin-generic', 55 );

	//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	add_submenu_page( IBX_WP_PLUGIN_NAME, 'Iboxindia Settings', 'Settings', 'administrator', IBX_WP_PLUGIN_NAME.'-settings', 'displayPluginAdminSettings' );

	$settings = IBX_WP::get_option( "settings" );

	if( !empty( $settings['hash'] ) ) {
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page( IBX_WP_PLUGIN_NAME, 'Iboxindia Import Data', 'Import Demo Data', 'administrator', IBX_WP_PLUGIN_NAME.'-demo-import', 'displayPluginAdminImportData' );
	}
}
function displayPluginAdminDashboard() {
	require_once 'admin-display.php';
}
function displayPluginAdminSettings() {
	require_once 'admin-settings-display.php';
}
function displayPluginAdminImportData() {
	echo "TODO";
}