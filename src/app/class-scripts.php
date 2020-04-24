<?php
/**
 * Easy Referral for WooCommerce - Scripts
 *
 * @version 1.0.6
 * @since   1.0.6
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Scripts' ) ) {

	class Scripts {
		/**
		 * init.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 */
		function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		function enqueue_scripts() {
			if ( ! is_account_page() ) {
				return;
			}
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? uniqid() : '.min';
			wp_enqueue_script( 'erwc', ERWC()->get_plugin_url() . 'assets/frontend' . $suffix . '.js', array(), $version );
			wp_enqueue_style( 'erwc', ERWC()->get_plugin_url() . 'assets/frontend' . $suffix . '.css', array(), $version );
		}
	}
}