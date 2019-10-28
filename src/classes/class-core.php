<?php
/**
 * Easy Referral for WooCommerce - Core
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;

use ThanksToIT\ExtendedWP\WP_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Core' ) ) {

	class Core extends WP_Plugin {

		/**
		 * @var Factory
		 */
		public $factory;

		public function init() {
			parent::init(); // TODO: Change the autogenerated stub

			// Factory
			$this->factory = new Factory( '\\ThanksToIT\\ERWC', array( 'Pro' ) );

			// Admin
			$this->handle_admin();

			if ( 'yes' === get_option( 'erwc_opt_enable', 'yes' ) ) {

				// Shortcodes
				$this->factory->get_instance( 'Shortcodes' )->init();

				// Referral CPT
				$this->factory->get_referral_cpt()->init();

				// Referral Email
				$this->factory->get_referral_email()->init();

				// Referral Meta
				$this->factory->get_referral_meta()->init();

				// Referral Status Tax
				$this->factory->get_referral_status_tax()->init();

				// Referral Authenticity Tax
				$this->factory->get_referral_authenticity_tax()->init();

				// Referral Authenticity Checking Tax
				$this->factory->get_referral_checking_tax()->init();

				// Referral Authenticity Checking
				$this->factory->get_authenticity_checking()->init();

				// Referral Code Manager
				$this->factory->get_referral_code_manager()->init();

				// WC Coupon
				$this->factory->get_wc_coupon()->init();

				add_filter( 'erwc_is_free_version', function () {
					return false;
				} );
			}
		}

		/**
		 * handle_admin.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @throws \ReflectionException
		 */
		function handle_admin() {
			// Create salt option on plugin activation
			register_activation_hook( ERWC()->plugin_info['filesystem_path'], array( ERWC()->factory->get_admin_settings_general(), 'update_salt_option_value' ) );

			// Settings Page
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'create_admin_settings' ), 15 );

			// My account > Referral Codes tab
			//$this->factory->get_referral_codes_tab();

			// My account > Referral tab
			$this->factory->get_referral_tab();

			// Referrer Meta
			$this->factory->get_referrer_meta()->init();

			// Enhanced Settings
			$enhanced_admin_settings = $this->factory->get_instance( 'Admin\Enhanced_Admin_Settings' );
			$enhanced_admin_settings->init( 'erwc', array() );

			// Notices
			$enhanced_admin_settings = $this->factory->get_instance( 'Admin\Admin_Notices' )->init();
		}

		/**
		 * Creates admin settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		function create_admin_settings( $settings ) {
			ERWC()->factory->get_admin_settings_authenticity();
			ERWC()->factory->get_admin_settings_general()->init();
			$settings[] = ERWC()->factory->get_instance( 'Admin\Admin_Settings' );
			return $settings;
		}

		/**
		 * Adds action links.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $links
		 *
		 * @return array
		 */
		public function add_action_links( $links ) {
			$mylinks = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=erwc' ) . '">' . __( 'Settings', 'easy-referral-for-woocommerce' ) . '</a>',
			);
			return parent::add_action_links( array_merge( $mylinks, $links ) );
		}



	}
}
