<?php
/**
 * Admin Notices
 *
 * @since 2.3.7
 * @package Iboxindia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
								<img src="%1$s" class="hide custom-logo" alt="Iboxindia" itemprop="logo" style="max-width: 90px;">
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
			wp_enqueue_style( 'ibx-wp-notices', IBX_WP_PLUGIN_URL . 'assets/css/' . $file, array(), IBX_WP_PLUGIN_VER );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Iboxindia_WP_Admin::get_instance();

endif;
