<?php
/**
 * Easy Referral for WooCommerce - WP Plugin
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ExtendedWP;

use ThanksToIT\DPWP\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ExtendedWP\WP_Plugin' ) ) {

	class WP_Plugin extends Singleton {
		public $plugin_info = array();

		function init() {
			// Add settings link on plugins page
			$path = $this->plugin_info['filesystem_path'];
			add_filter( 'plugin_action_links_' . plugin_basename( $path ), array( $this, 'add_action_links' ) );

			// Localization
			add_action( 'init', array( $this, 'handle_localization' ) );
		}

		/**
		 * Handle Localization
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function handle_localization() {
			$domain = $this->plugin_info['text_domain'];
			$locale = apply_filters( 'plugin_locale', is_admin() ? get_user_locale() : get_locale(), $domain );
			if ( function_exists( 'pll_current_language' ) ) {
				$locale = pll_current_language( 'locale' );
			}
			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . 'plugins' . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->plugin_info['filesystem_path'] ) ) . '/languages/' );
		}

		/**
		 * add_action_links.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $links
		 *
		 * @return mixed
		 */
		function add_action_links( $links ) {
			return $links;
		}

		/**
		 * Setups plugin.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $args
		 */
		function setup( $args ) {
			$args              = wp_parse_args( $args, array(
				'filesystem_path' => '',  // __FILE__
				'text_domain'     => ''
			) );
			$this->plugin_info = $args;
		}

		/**
		 * Gets plugin url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_plugin_url() {
			$path = $this->plugin_info['filesystem_path'];
			return plugin_dir_url( $path );
		}

		/**
		 * Gets plugins dir.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_plugin_dir() {
			$path = $this->plugin_info['filesystem_path'];
			return untrailingslashit( plugin_dir_path( $path ) ) . DIRECTORY_SEPARATOR;;
		}

	}
}
