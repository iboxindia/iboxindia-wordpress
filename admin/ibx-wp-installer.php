<?php
  $type = sanitize_key( isset ( $_GET['type'] ) ? $_GET['type'] : 'theme' );

  if ( ! current_user_can( 'install_themes' ) ) {
    die( __( 'Sorry, you are not allowed to install themes on this site.' ) );
  }
  include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

  $slug = sanitize_key( $_GET['slug'] );
  if ( !wp_verify_nonce( $_GET['nonce'], $slug)) {
    die( __( 'No naughty business please.' ) );
  }

  $package_info = ibx_wp_postman_get('/packages/' . $slug);
  // var_dump($package_info);
  $result = ibx_wp_postman_get($package_info['download_url'], [],'');

  $file_url = $result['http_scheme'] . '://' . ( $result['auth_key'] ? ( $result['auth_key'] . '@' ) : '' ) . $result['asset_url'];
  
  // //download file in uploads dir
  $result = ibx_wp_download_file($file_url, $result['asset_name']);

  // var_dump($package_info);
  if($package_info['type'] == 'theme') {
    $up = new Theme_Upgrader();
  } else if($package_info['type'] == 'plugin') {
    $up = new Plugin_Upgrader();
  }
  
  $up->install( $result['data']['file'] );
  // $result = json_encode($result['data']['file']);

  $link = admin_url('?page=iboxindia-wordpress&tab='.$package_info['type']);
?>
<script>
setTimeout(function(){
  window.location = "<?php echo $link; ?>";
}, 5000);
</script>