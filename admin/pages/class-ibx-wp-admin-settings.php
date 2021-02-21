<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Settings_Page' ) ) :

	/**
	 * Iboxindia_WP_Settings_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Settings_Page {

    /**
     * Instance of Iboxindia_WP_Settings_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Settings_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Settings_Page.
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
      add_submenu_page( IBX_WP_PLUGIN_NAME, 'Iboxindia Settings', 'Settings', 'administrator', IBX_WP_PLUGIN_NAME.'-settings', [ $this, 'show' ] );
    }

    public function show() {
      $action = sanitize_key( isset ( $_POST['action'] ) ? $_POST['action'] : '' );

      // var_dump( $settings );
      function mask ( $str, $start = 0, $length = null ) {
        $mask = preg_replace ( "/\S/", "*", $str );
        if( is_null ( $length )) {
            $mask = substr ( $mask, $start );
            $str = substr_replace ( $str, $mask, $start );
        }else{
            $mask = substr ( $mask, $start, $length );
            $str = substr_replace ( $str, $mask, $start, $length );
        }
        return $str;
      }

      if($action == 'ibx_wp_settings_clear') {
        Iboxindia_WP_Settings::reset();
        ?>
        <script>
          window.location = window.location;
        </script>
        <?php
      } else if($action == 'ibx_wp_settings_update') {
        $timeout = intval( sanitize_text_field( isset ( $_POST['timeout'] ) ? $_POST['timeout'] : 60 ) );
        $debug = boolval( sanitize_text_field( isset ( $_POST['debug'] ) ? $_POST['debug'] : 0 ) );
        Iboxindia_WP_Settings::set( 'timeout', $timeout );
        Iboxindia_WP_Settings::set( 'debug', $debug );
        ?>
          <script>
            // setTimeout(function(){
              window.location = window.location;
            // }, 5000);
          </script>
        <?php
      } else if($action == 'ibx_wp_settings_login') {
        $username = sanitize_text_field( isset ( $_POST['username'] ) ? $_POST['username'] : '' );
        $password = sanitize_text_field( isset ( $_POST['password'] ) ? $_POST['password'] : '' );
        $resp = ibx_wp_postman_post('/u/login', ["username" => $username, "password" => $password]);
        // var_dump($resp);
        if( $resp['code'] == 200 ) {
          Iboxindia_WP_Settings::set( "hash", $resp['jwt'] );
        ?>
          <script>
            // setTimeout(function(){
              window.location = window.location;
            // }, 5000);
          </script>
        <?php
        } else {
          ?>
          <script>
            alert('<?php echo __( 'Invalid Credentials' ); ?>');
            window.location = window.location;
          </script>
        <?php  
        }
      } else {
        $hash = Iboxindia_WP_Settings::get( "hash" );
        $timeout = Iboxindia_WP_Settings::get( "timeout" );
        $debug = Iboxindia_WP_Settings::get( "debug" );
        
        ?>
      <!-- pending tabbed design -->
        <div class="wrap">
          <div id="icon-themes" class="icon32"></div>  
          <h2>Iboxindia WP Settings</h2>
          <div class="iboxindia-settings login-logout-section">
            <?php if ( empty( $hash ) ) { ?>
              <form method="POST">
                <input type="hidden" name="action" value="ibx_wp_settings_login" />
                <input type="text" name="username" placeholder="Username" />
                <input type="password" name="password" placeholder="Password" />
                <button type="submit" class="btn btn-primary">Save</button>
              </form> 
            <?php } else { ?>
              <form method="POST">
                <input type="hidden" name="action" value="ibx_wp_settings_clear" />
                <div class="">Logged in: <span><?php 
                  $hash = strtoupper( $hash );
                  $hash = substr( $hash, strlen( $hash ) - 16 );
                  echo mask( $hash, strlen( $hash ) - 16, strlen( $hash ) - 4 );
                ?></span>
                <button type="submit" class="btn btn-primary">Logout</button>
                </div>
              </form>
            <?php } ?>
          </div>
          <div class="iboxindia-settings">
            <form method="POST">
              <input type="hidden" name="action" value="ibx_wp_settings_update" />
              <table>
              <tr>
                  <td>
                    <label for="timeout">Timeout</label>
                  </td>
                  <td>
                    <input type="number" name="timeout" id="timeout" value="<?php echo $timeout; ?>"/>
                  </td>
                </tr>
                <tr>
                  <td>
                    <label for="debug">Debug</label>
                  </td>
                  <td>
                    <select name="debug" id="debug">
                      <option value="1" <?php echo $debug ? 'selected' : ''; ?>> True </option>
                      <option value="0" <?php echo $debug ? '' : 'selected'; ?>> False </option>
                    </select>
                  </td>
                </tr>
              </table>
              <button type="submit" class="btn btn-primary">Save</button>
            </form>
          </div>
        </div>
      <?php }
    }
  }
  Iboxindia_WP_Settings_Page::get_instance();
endif;
?>