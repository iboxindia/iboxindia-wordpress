<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Rest_Client' ) ) :

	/**
	 * Iboxindia_WP_Rest_Client
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Rest_Client {
    private static $base_url = 'https://wordpress.iboxindia.com';

    public static function get($uri='', $params=[]) {
      $params['json']=true;
      $hash = Iboxindia_WP_Settings::get( "hash" );
      $url = self::$base_url . $uri . '?' . http_build_query($params);

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

    public static function post($uri='', $params=[]) {
      $hash = Iboxindia_WP_Settings::get( "hash" );
      $url = self::$base_url . $uri . '?json';
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
  }
endif;
?>