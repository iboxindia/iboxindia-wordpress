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
// $base_url = "https://iboxindia.com/wordpress";

function ibx_wp_postman_get($uri='', $params=[]) {
  $params['json']=true;
  $settings = IBX_WP::get_option( "settings" );
  $base_url = "https://wordpress.iboxindia.com/packages";
  $curl = curl_init();
  $url = $base_url . $uri . '?' . http_build_query($params);
  // echo $url;
  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      // "x-hash: " . $settings['hash'],
    ),
  ) );
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  if ($err) {
    //Only show errors while testing
    //echo "cURL Error #:" . $err;
    $response = array(
      'error' => $err
    );
  } else {
    $tempResponse = json_decode($response, true);
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
function ibx_wp_postman_post() {
  echo '1';
}