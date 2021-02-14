<?php

// require_once WPCF7_PLUGIN_DIR . '/admin/includes/admin-functions.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/help-tabs.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/tag-generator.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/welcome-panel.php';
// require_once WPCF7_PLUGIN_DIR . '/admin/includes/config-validator.php';


if ( ! class_exists( 'Iboxindia_WP_Admin' ) ) :

	/**
	 * Admin
	 */
	class Iboxindia_WP_Admin {

		/**
		 * Instance of Iboxindia_WP_Admin
		 *
		 * @since 2.3.7
		 * @var (Object) Iboxindia_WP_Admin
		 */
		private static $instance = null;

		/**
		 * Instance of Iboxindia_WP_Admin.
		 *
		 * @since 2.3.7
		 *
		 * @return object Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 2.3.7
		 */
		private function __construct() {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'ibx_wp_notice_before_markup', array( $this, 'notice_assets' ) );
		}

		/**
		 * Admin Notices
		 *
		 * @since 2.3.7
		 * @return void
		 */
		public function admin_notices() {

			$image_path = esc_url( IBX_WP_PLUGIN_URL . 'inc/assets/images/logo.svg' );

			Iboxindia_WP_Notices::add_notice(
				array(
					'id'      => 'ibx-wp-start-notice',
					'type'    => 'info',
					'class'   => 'ibx-wp-star',
					// 'show_if' => ( false === ::get_instance()->is_white_labeled() ),
					/* translators: %1$s white label plugin name and %2$s deactivation link */
					'message' => sprintf(
						'<div class="ibx-notice-container">
							<div class="notice-image" style="display: flex;">
								<img src="%1$s" class="hide custom-logo" alt="Iboxindia Wordpress" itemprop="logo" style="max-width: 90px;">
							</div>
							<div class="notice-content">
								<div class="notice-heading">
									%2$s
								</div>
								%3$s<br />
								<div class="ibx-review-notice-container">
									<a href="%4$s" class="ibx-notice-close ibox-review-notice button-primary" target="_blank">
									%5$s
									</a>
									
									<span class="dashicons dashicons-calendar"></span>
									<a href="#" data-repeat-notice-after="%6$s" class="ibox-notice-close ibox-review-notice">
									%7$s
									</a>
									
									<span class="dashicons dashicons-smiley"></span>
									<a href="#" class="ibx-notice-close ibox-review-notice">
									%8$s
									</a>
								</div>
							</div>
						</div>',
						$image_path,
						__( 'Hello! We noticed that the Iboxindia Wordpress Setup plugin is active. If you have installed this plugin by mistake, we request you to kindly deactivate it to remove this notice.', 'iboxindia' ),
						__( 'If you need any assistance from us to setup your website and get online within minutes, just click the below button to get in touch. &mdash; Thanks a ton!', 'iboxindia' ),
						'https://iboxindia.com/contact?wordpress=true&site=blog_url',
						__( 'Ok, Please help me.', 'iboxindia' ),
						MONTH_IN_SECONDS,
						__( 'Nope, maybe later', 'iboxindia' ),
						__( 'Support our free efforts', 'iboxindia' )
					),
				)
			);
		}

		/**
		 * Enqueue Iboxindia WP Notices CSS.
		 *
		 * @since 2.3.7
		 *
		 * @return void
		 */
		public static function notice_assets() {
			$file = is_rtl() ? 'ibx-wp-notices-rtl.css' : 'ibx-wp-notices.css';
			wp_enqueue_style( 'ibx-wp-notices', IBX_WP_PLUGIN_URL . '/assets/css/' . $file, array(), IBX_WP_PLUGIN_VER );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Iboxindia_WP_Admin::get_instance();

endif;

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