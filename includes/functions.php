<?php

function ibx_wp_plugin_path( $path = '' ) {
	return path_join( IBX_WP_PLUGIN_DIR, trim( $path, '/' ) );
}

function ibx_wp_plugin_url( $path = '' ) {
	$url = plugins_url( $path, IBX_WP_PLUGIN );

	if ( is_ssl()
	and 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}
function ibx_wp_get_request_uri() {
	static $request_uri = '';

	if ( empty( $request_uri ) ) {
		$request_uri = add_query_arg( array() );
	}

	return esc_url_raw( $request_uri );
}

function ibx_wp_postman_get($uri='', $params=[], $base_url='https://wordpress.iboxindia.com') {
  $params['json']=true;
  $settings = IBX_WP::get_option( "settings" );
  $url = $base_url . $uri . '?' . http_build_query($params);

  $args = array(
    'headers' => array(
      'domain' => get_site_url(),
    )
  );
  if ( ! empty( $settings['hash'] ) ) {
    $args['headers']['Authorization'] = 'Bearer ' . $settings['hash'];
  }

  $response = wp_remote_get ( $url, $args );
  if ( is_wp_error( $response ) ) {
    $error_message = $response->get_error_message();
    $response = array(
      'error' => $error_message
    );
  } else {
    $body = wp_remote_retrieve_body( $response );
    $tempResponse = json_decode($body, true);
    if($tempResponse['statusCode'] == 200) {
      $response = $tempResponse['data'];
    } else {
      $response = array(
        'error' => $tempResponse
      );
    }
  }
  if( isset( $response['error'] ) ) { return false; }
  return $response;
}
function ibx_wp_postman_post($uri='', $params=[], $base_url='https://wordpress.iboxindia.com') {
  // $params['json']=true;
  $settings = IBX_WP::get_option( "settings" );
  $url = $base_url . $uri . '?json';
  // var_dump(json_encode($params));
  $args = array(
    'headers' => array(
      'domain' => get_site_url(),
    ),
    'body'    => json_encode($params)
  );
  if ( ! empty( $settings['hash'] ) ) {
    $args['headers']['Authorization'] = 'Bearer ' . $settings['hash'];
  }

  // var_dump($args);
  $response = wp_remote_post ( $url, $args );
  // var_dump($response);
  if ( is_wp_error( $response ) ) {
    $error_message = $response->get_error_message();
    $response = array(
      'error' => $error_message
    );
  } else {
    $body = wp_remote_retrieve_body( $response );
    $tempResponse = json_decode($body, true);
    if($tempResponse['statusCode'] == 200) {
      $response = $tempResponse['data'];
    } else {
      $response = array(
        'error' => $tempResponse
      );
    }
  }
  if( isset( $response['error'] ) ) { return false; }
  return $response;
}
// add_action("wp_ajax_ibx_wp_download", "ibx_wp_download");

function ibx_wp_download() { 
  
  $settings = IBX_WP::get_option( "settings" );
  if ( ! current_user_can( 'install_themes' ) ) {
    exit( __( 'Sorry, you are not allowed to install themes on this site.' ) );
  }
  include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

  $slug = sanitize_key( $_GET['slug'] );
  if ( !wp_verify_nonce( $_GET['nonce'], $slug)) {
    exit( __( 'No naughty business please.' ) );
  }

  $package_info = ibx_wp_postman_get('/packages/' . $slug);
  // var_dump($package_info);
  $result = ibx_wp_postman_get($package_info['download_url'], [],'');

  $file_url = $result['http_scheme'] . '://' . ( $result['auth_key'] ? ( $result['auth_key'] . '@' ) : '' ) . $result['asset_url'];

  // //download file in uploads dir
  $result = ibx_wp_download_file($file_url, $result['asset_name'], $settings['timeout']);

  if( $settings['debug'] ) {
    echo '<hr />';
    echo '<pre>';
    var_dump($package_info);
    echo '</pre>';
    echo '<hr />';
    echo '<pre>';
    var_dump($result);
    echo '</pre>';
    echo '<hr />';
  }

  if($package_info['type'] == 'theme') {
    $destination_path = WP_CONTENT_DIR . '/themes';
    // $up = new Theme_Upgrader();
  } else if($package_info['type'] == 'plugin') {
    $destination_path = WP_PLUGIN_DIR;
    // $up = new Plugin_Upgrader();
  }

  WP_Filesystem();
  $unzipfile = unzip_file( $$result['data']['file'], $destination_path);
    
  if ( $unzipfile ) {
    echo 'Successfully installed ' . $result['asset_name'] . ' to [' . $destination_path . ']';
  } else {
    echo 'Failed to install ' . $result['asset_name'] . ' to [' . $destination_path . ']';
  }

  // if( isset( $result['data']['file'] ) )
  //   $up->install( $result['data']['file'] );
  // extract file


  $result = json_encode($result['data']['file']);
  echo $result;
}