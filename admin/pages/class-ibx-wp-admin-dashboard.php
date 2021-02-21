<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Dashboard_Page' ) ) :

	/**
	 * Iboxindia_WP_Dashboard_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Dashboard_Page {

    /**
     * Instance of Iboxindia_WP_Dashboard_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Dashboard_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Dashboard_Page.
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
    }

    public function show() {
      $action = sanitize_key( isset ( $_GET['action'] ) ? $_GET['action'] : '' );

      if($action == 'ibx_wp_download') {
        Iboxindia_WP_Package_Installer_Page::get_instance()->show();
      } else {
        global $pagenow;
        $hash = Iboxindia_WP_Settings::get( "hash" );
        $open_source = true;
        
        if( !empty( $hash ) ) {
          $open_source = false;
        }

        $tab = 'themes'; 

        $params=[];
        if ( isset ( $_GET['tab'] ) && sanitize_key( $_GET['tab'] ) == 'plugins' ) {
          $tab = sanitize_key( $_GET['tab'] ); 
          $existing_items = [];
          $plugin_array = get_plugins();
          foreach ( $plugin_array as $key => $plugin ) {
            $temp_array = explode( '/', $key );
            $existing_items[str_replace( '.php', '', end( $temp_array ) )] = $plugin;
          }
          $base_uri='/packages';
          $params['type']='plugin';
        } else {
          $existing_items = wp_get_themes();
          $base_uri='/packages';
          $params['type']='theme';
        }

        function ibx_wp_admin_tabs( $current = 'themes' ) { 
          $tabs = array( 'themes' => 'Themes', 'plugins' => 'Plugins' ); 
          $links = array();
          echo '<div id="icon-themes" class="icon32"><br></div>';
          echo '<h2 class="nav-tab-wrapper">';
          foreach( $tabs as $tab => $name ){
              $class = ( $tab == $current ) ? ' nav-tab-active' : '';
              echo "<a class='nav-tab$class' href='?page=iboxindia&tab=$tab'>$name</a>";
              
            }
            echo '</h2>';
          }
      
          function render_items_array ( $items, $existing_items = [] ) {?>
            <div class="ibx-items wp-clearfix">
              <?php foreach( $items as $item ) {
                render_item ( $item, isset( $existing_items[ $item[ 'slug' ] ] ) ? $existing_items[$item['slug']] : null );
                // render_item ( $item );
              } ?>
            </div>
            <?php
          }

          function render_item ( $item, $existing_item = null ) {
              // var_dump ($item);
            ?>
            <div class="ibx-item" tabindex="0" >
              <div class="ibx-item-screenshot">
                <div class="ibx-item-version tag">
                  <?php echo 'v' . $item['latest_version']; ?>
                </div>
                <img src="<?php echo $item['thumbnail_url']; ?>" alt="<?php echo $item['name']; ?>">
              </div>
              <?php if ( $existing_item != null ) { ?>
                <div class="update-message notice inline notice-warning notice-alt">
                  <p>
                    Installed. 
                    <?php if (version_compare($existing_item['Version'], $item['latest_version'], '<')) { ?>
                      <button data-existing-ver="<?php echo $existing_item['ver']; ?>" data-current-ver="<?php echo $item['latest_version']; ?>" class="button-link" type="button">Update now</button>				
                    <?php } ?>
                  </p>
                </div>
              <?php } ?>
              <div class="ibx-item-container">
                <h2 class="ibx-item-name" id="<?php echo $item['slug']; ?>">
                  <?php echo $item['name']; ?>
                </h2>
                <?php if ( $existing_item == null ) { ?>
                  <div class="ibx-item-actions">
                    <?php
                      $nonce = wp_create_nonce($item['slug']);
                      $link = admin_url('admin.php?page=iboxindia&action=ibx_wp_download&slug='.$item['slug'].'&nonce='.$nonce);
                      echo '<a class="button button-primary" data-nonce="' . $nonce . '" data-slug="' . $item['slug'] . '" href="' . $link . '">Install</a>';
                    ?>
                  </div>
                <?php } ?>
              </div>
            </div>
            <?
          }
        ?>
        
        <div class="wrap">
          <h2>Iboxindia - <?php echo $open_source ? 'Open Source' : 'Premium'; ?></h2>
          
          <?php ibx_wp_admin_tabs( $tab ); ?>
          <div class="ibx-items-browser">
            <?php
              $items = ibx_wp_postman_get( $base_uri, $params );
              // var_dump( $items );
              if( $items ) {
                render_items_array ( $items, $existing_items );
              }
            ?>
          </div>
        </div> <?php 
      }
    }
  }
endif;
?>