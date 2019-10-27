<?php
/**
 * Easy Referral for WooCommerce - Enhanced Admin Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Enhanced_Admin_Settings' ) ) {

	class Enhanced_Admin_Settings {
		public $settings_id = '';
		public $args = array();

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings_id
		 * @param array $args
		 */
		function init( $settings_id, $args = array() ) {
			$this->args        = wp_parse_args( $args, array() );
			$this->settings_id = $settings_id;
			add_filter( 'woocommerce_get_settings_' . $this->settings_id, array( $this, 'handle_hide_param' ), 10 );
			add_filter( 'woocommerce_get_settings_' . $this->settings_id, array( $this, 'handle_disable_param' ), 10 );
			add_action( 'woocommerce_settings_save_' . $this->settings_id, function () {
				add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'handle_allowed_values_param' ), 10, 3 );
			} );
			//add_filter( 'woocommerce_get_settings_' . $this->settings_id, array( $this, 'handle_allowed_values_param' ), 10 );
		}

		/**
		 * handle_allowed_values_param.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $value
		 * @param $option
		 * @param $raw_value
		 *
		 * @return string
		 */
		function handle_allowed_values_param( $value, $option, $raw_value ) {
			if (
				! isset( $option['allowed_values'] ) ||
				empty( $option['allowed_values'] ) ||
				in_array( $value, $option['allowed_values'] )
			) {
				return $value;
			}
			$value = isset( $option['default'] ) ? $option['default'] : '';
			return $value;
		}

		/**
		 * handle_disable_param.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings
		 *
		 * @return array
		 */
		function handle_disable_param( $settings ) {
			$fields = wp_list_filter( $settings, array( 'disable' => true ) );
			foreach ( $fields as $key => $field ) {
				$settings[ $key ]['custom_attributes']['disabled'] = 'disabled';
				$settings[ $key ]['custom_attributes']['readonly'] = 'readonly';
			}
			return $settings;
		}

		/**
		 * handle_hide_param.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings
		 *
		 * @return array
		 */
		function handle_hide_param( $settings ) {
			$fields   = wp_list_filter( $settings, array( 'hide' => true ) );
			$settings = array_diff_key( $settings, array_flip( array_keys( $fields ) ) );
			return $settings;
		}

	}
}