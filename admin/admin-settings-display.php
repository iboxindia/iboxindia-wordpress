<?php

$action = sanitize_key( isset ( $_POST['action'] ) ? $_POST['action'] : '' );

$settings = IBX_WP::get_option( "settings" );
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
  $settings['hash'] = null;
	IBX_WP::update_option( "settings", $settings );
  ?>
  <script>
    window.location = window.location;
  </script>
  <?php
} else if($action == 'ibx_wp_settings_update') {
  $timeout = intval( sanitize_text_field( isset ( $_POST['timeout'] ) ? $_POST['timeout'] : 60 ) );
  $debug = boolval( sanitize_text_field( isset ( $_POST['debug'] ) ? $_POST['debug'] : 0 ) );
  $settings['timeout'] = $timeout;
  $settings['debug'] = $debug;
  IBX_WP::update_option( "settings", $settings );
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
    $settings['hash'] = $resp['jwt'];
    IBX_WP::update_option( "settings", $settings );
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
  ?>
<!-- pending tabbed design -->
  <div class="wrap">
    <div id="icon-themes" class="icon32"></div>  
    <h2>Iboxindia WP Settings</h2>
    <div class="iboxindia-settings login-logout-section">
      <?php if ( empty( $settings['hash'] ) ) { ?>
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
            $hash = strtoupper( $settings['hash'] );
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
              <input type="number" name="timeout" id="timeout" value="<?php echo $settings['timeout']; ?>"/>
            </td>
          </tr>
          <tr>
            <td>
              <label for="debug">Debug</label>
            </td>
            <td>
              <select name="debug" id="debug">
                <option value="1" <?php echo $settings['debug'] ? 'selected' : ''; ?>> True </option>
                <option value="0" <?php echo $settings['debug'] ? '' : 'selected'; ?>> False </option>
              </select>
            </td>
          </tr>
        </table>
        <button type="submit" class="btn btn-primary">Save</button>
      </form>
    </div>
  </div>
<?php } ?>
