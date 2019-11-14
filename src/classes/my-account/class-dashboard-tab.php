<?php
/**
 * Easy Referral for WooCommerce - Dashboard Tab
 *
 * @version 1.0.4
 * @since   1.0.4
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\My_Account;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\My_Account\Dashboard_Tab' ) ) {

	class Dashboard_Tab {

		/**
		 * init.
		 *
		 * @version 1.0.5
		 * @since   1.0.5
		 */
		function init() {
			add_action( 'woocommerce_account_dashboard', array( $this, 'add_content' ) );
		}

		/**
		 * add_content.
		 *
		 * @version 1.0.5
		 * @since   1.0.5
		 */
		function add_content() {
			if ( 'no' === get_option( 'erwc_opt_interface_referral_codes_dashboard', 'yes' ) ) {
				return;
			}
			echo '<h3>' . __( 'Your Referral Codes', 'easy-referral-for-woocommerce' ) . '</h3>';
			echo do_shortcode( '[erwc_referral_codes_table]' );
		}
	}
}