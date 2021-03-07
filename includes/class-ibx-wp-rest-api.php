<?php

use function PHPSTORM_META\type;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Rest_API' ) ) :

	/**
	 * Iboxindia_WP_Rest_API
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Rest_API {

    private static $base_url = 'https://wordpress.iboxindia.com';

    public static function getPackages( $type ) {
      return Iboxindia_WP_Rest_Client::get(self::$base_url . '/packages', [ "type" => $type ]);
    }
    public static function getPackage( $slug ) {
      return Iboxindia_WP_Rest_Client::get(self::$base_url . '/packages/' . $slug );
    }
    public static function loginUser ($username, $password ) {
      return Iboxindia_WP_Rest_Client::post(self::$base_url . '/u/login', ["username" => $username, "password" => $password]);
    }
  }
endif;
?>