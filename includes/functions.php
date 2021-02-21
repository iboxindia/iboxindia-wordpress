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
  $hash = Iboxindia_WP_Settings::get( "hash" );
  $url = $base_url . $uri . '?' . http_build_query($params);

  $args = array(
    'headers' => array(
      'domain' => get_site_url(),
    )
  );
  if ( ! empty( $hash ) ) {
    $args['headers']['Authorization'] = 'Bearer ' . $hash;
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
  $hash = Iboxindia_WP_Settings::get( "hash" );
  $url = $base_url . $uri . '?json';
  // var_dump(json_encode($params));
  $args = array(
    'headers' => array(
      'domain' => get_site_url(),
    ),
    'body'    => json_encode($params)
  );
  if ( ! empty( $hash ) ) {
    $args['headers']['Authorization'] = 'Bearer ' . $hash;
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
add_action("wp_ajax_ibx_wp_get_package_info", "ibx_wp_get_package_info");
function ibx_wp_get_package_info() {
  $hash = Iboxindia_WP_Settings::get( "hash" );

  $slug = sanitize_key( $_POST['slug'] );
  if ( !wp_verify_nonce( $_POST['nonce'], $slug)) {
    exit( __( 'No naughty business please.' ) );
  }

  $package_info = ibx_wp_postman_get('/packages/' . $slug);

  $result = ibx_wp_postman_get($package_info['download_url'], [],'');

  Iboxindia_WP_Settings::set( "package_info", $result);

  wp_send_json( $package_info );
  // wp_send_json($result);
}

add_action("wp_ajax_ibx_wp_download_package", "ibx_wp_download_package");
function ibx_wp_download_package() {
  
  $timeout = Iboxindia_WP_Settings::get( "settings" );
  if ( ! current_user_can( 'install_themes' ) ) {
    wp_send_json( __( 'Sorry, you are not allowed to install themes on this site.' ) );
  }

  $slug = sanitize_key( $_POST['slug'] );
  if ( !wp_verify_nonce( $_POST['nonce'], $slug)) {
    wp_send_json( __( 'No naughty business please.' ) );
  }

  $package_info = Iboxindia_WP_Settings::get( "package_info" );
  // wp_send_json($package_info);

  $file_url = $package_info['http_scheme'] . '://' . ( $package_info['auth_key'] ? ( $package_info['auth_key'] . '@' ) : '' ) . $package_info['asset_url'];

  // //download file in uploads dir
  $result = ibx_wp_download_file($file_url, $package_info['asset_name'], $timeout);

  Iboxindia_WP_Settings::set( "download_info", $result );

  wp_send_json($result);
}
add_action("wp_ajax_ibx_wp_install_package", "ibx_wp_install_package");
function ibx_wp_install_package() {
  $package_info = Iboxindia_WP_Settings::get( "package_info" );
  $download_info = Iboxindia_WP_Settings::get( "download_info" );

  $slug = sanitize_key( $_POST['slug'] );
  if ( !wp_verify_nonce( $_POST['nonce'], $slug)) {
    wp_send_json( __( 'No naughty business please.' ) );
  }

  $type = $package_info['type'];
  $asset = $download_info['asset']['data']['file'];

  if($type == 'theme') {
    $destination_path = WP_CONTENT_DIR . '/themes';
    // $up = new Theme_Upgrader();
  } else if($type == 'plugin') {
    $destination_path = WP_PLUGIN_DIR;
    // $up = new Plugin_Upgrader();
  }

  // $file_loc = wp_upload_dir() . '' . $asset;
  WP_Filesystem();
  $unzipfile = unzip_file( $file_loc, $destination_path);

  $resp = [];
  if ( $unzipfile ) {
    $resp['success'] = true;
    $resp['message'] = 'Successfully installed ' . $slug . ' from [' . $asset . '] to [' . $destination_path . ']';
  } else {
    $resp['success'] = false;
    $resp['message'] = 'Failed to install ' . $slug . ' from [' . $asset . '] to [' . $destination_path . ']';
  }

  wp_send_json($resp);

}