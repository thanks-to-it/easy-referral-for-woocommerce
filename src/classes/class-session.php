<?php
/**
 * Easy Referral for WooCommerce - Session
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Session' ) ) {

	class Session {

		/**
		 * set_session_var.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $var
		 * @param $value
		 */
		function set_session_var( $var, $value ) {
			WC()->session->set( $var, $value );
		}

		/**
		 * get_session_var.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $var
		 *
		 * @return array|string
		 */
		function get_session_var( $var ) {
			return WC()->session->get( $var );
		}

		/**
		 * unset_session_var.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $var
		 */
		function unset_session_var( $var ) {
			WC()->session->set( $var, null );
			WC()->session->__unset( $var );
		}
	}
}