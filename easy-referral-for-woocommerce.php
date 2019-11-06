<?php
/**
 * Plugin Name: Easy Referral for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/easy-referral-for-woocommerce
 * Description: Easy Referral System for WooCommerce
 * Version: 1.0.3
 * Author: Thanks to IT
 * Author URI: https://github.com/thanks-to-it
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: easy-referral-for-woocommerce
 * Domain Path: /src/languages
 * WC requires at least: 3.0.0
 * WC tested up to: 3.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once "vendor/autoload.php";

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) {
	return;
}

if ( ! function_exists( 'ERWC' ) ) {
	/**
	 * @param string $action
	 * @param array $skip_intervals
	 *
	 * @return \ThanksToIT\ERWC\Core
	 */
	function ERWC( $action = '', $skip_intervals = array() ) {
		return \ThanksToIT\ERWC\Core::get_instance();
	}
}

$plugin = ERWC();
$plugin->setup( array(
	'filesystem_path' => __FILE__,
	'text_domain'     => 'easy-referral-for-woocommerce'
) );
if ( true === apply_filters( 'erwc_init', true ) ) {
	$plugin->init();
}