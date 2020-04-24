<?php
/**
 * Easy Referral for WooCommerce - Factory Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Test' ) ) {

	class Test {
		protected static $instance = NULL;

		public static function get_instance() {

			// create an object
			NULL === self::$instance and self::$instance = new self;

			return self::$instance; // return the object
		}

		public function __construct() {
			error_log('TEST Class');
		}

		/*static function wp_footer(){
			error_log('TEST Class - WP - Footer');
		}*/

		function wp_footer(){
			error_log('TEST Class - WP - Footer');
		}
	}
}