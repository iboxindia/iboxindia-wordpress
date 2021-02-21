<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Backup_Restore_Page' ) ) :

	/**
	 * Iboxindia_WP_Backup_Restore_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Backup_Restore_Page {

    /**
     * Instance of Iboxindia_WP_Backup_Restore_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Backup_Restore_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Backup_Restore_Page.
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
      add_action( 'ibx_wp_admin_menu', array( $this, 'add_menu' ) );
    }
    public function add_menu() {
      add_submenu_page( IBX_WP_PLUGIN_NAME, 'Backup Restore', 'Backup Restore', 'administrator', IBX_WP_PLUGIN_NAME.'-backup-restore', [ $this, 'show' ] );
    }


    public function show() {
      echo "back";
    }
  }
  Iboxindia_WP_Backup_Restore_Page::get_instance();
endif;
?>