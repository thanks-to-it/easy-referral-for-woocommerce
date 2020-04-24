<?php
/**
 * Easy Referral for WooCommerce - Admin Settings
 *
 * @version 1.0.2
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

use ThanksToIT\ERWC\Design_Pattern\Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Admin_Settings' ) ) {

	class Admin_Settings extends \WC_Settings_Page {

		/**
		 * Setup settings class.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id    = 'erwc';
			$this->label = __( 'Referral', 'easy-referral-for-woocommerce' );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'admin_head', array( $this, 'admin_style' ) );
		}

		/**
		 * get_default_messages.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $message_type
		 *
		 * @return string
		 */
		function get_default_message( $message_type ) {
			$message = '';
			switch ( $message_type ) {
				case 'disabled_options':
					$message = '<br /><span class="erwc-inline-message" style="margin-top:3px;"><i class="erwc-icon dashicons-before dashicons-awards"></i>' . sprintf( __( 'Disabled options can be unlocked using the <a href="%s" target="_blank">Pro version</a>', 'easy-referral-for-woocommerce' ), 'https://wpfactory.com/item/easy-referral-for-woocommerce/' ) . '</span>';
					break;
			}
			return $message;
		}

		/**
		 * get_disabled_option_message.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_disabled_options_message(){
			return true === ( apply_filters( 'erwc_is_free_version', true ) ) ? $this->get_default_message( 'disabled_options' ) : '';
		}

		/**
		 * admin_style.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function admin_style() {
			if (
				! isset( $_REQUEST['tab'] ) ||
				! isset( $_REQUEST['page'] ) ||
				$_REQUEST['tab'] != $this->id ||
				$_REQUEST['page'] != 'wc-settings'
			) {
				return;
			}
			?>
			<style>
				.erwc-icon{
					margin:3px 2px 0 -4px;
					line-height:1px;
					position:relative;
					display:inline-block;
					top:-1px;					
				}
				.erwc-icon:before{
					font-size:20px;
					vertical-align: middle;
					color:#999;
				}
				.erwc-inline-message {
					background: #e8e8e8;
					padding: 4px 9px 4px;
					color: #888;
					font-size: 13px;
					vertical-align: middle;
					display:inline-block;
					clear:both;
					margin: 0px 0 0 0px;
				}
			</style>
			<?php
		}

		/**
		 * Get sections.
		 *
		 * @version 1.0.2
		 * @since   1.0.0
		 *
		 * @return array
		 */
		public function get_sections() {
			$sections = array(
				''             => __( 'General', 'easy-referral-for-woocommerce' ),
				'authenticity' => __( 'Authenticity', 'easy-referral-for-woocommerce' ),
				'interface'    => __( 'Interface', 'easy-referral-for-woocommerce' )
			);
			return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		}

		/**
		 * Get settings array.
		 *
		 * @version 1.0.2
		 * @since   1.0.0
		 *
		 * @param string $current_section Optional. Defaults to empty string.
		 *
		 * @return array Array of settings
		 */
		public function get_settings( $current_section = '' ) {
			if ( '' == $current_section ) {
				$settings = apply_filters( 'erwc_settings_general', array() );
			} elseif ( 'authenticity' === $current_section ) {
				$settings = apply_filters( "erwc_settings_{$current_section}", array() );
			} elseif ( 'interface' === $current_section ) {
				$settings = apply_filters( "erwc_settings_{$current_section}", array() );
			}

			/**
			 * Filter MyPlugin Settings
			 *
			 * @since 1.0.0
			 *
			 * @param array $settings Array of the plugin settings
			 */
			return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
		}

		/**
		 * Output the settings.
		 *
		 * @since 1.0.0
		 */
		public function output() {
			global $current_section;
			$settings = $this->get_settings( $current_section );
			\WC_Admin_Settings::output_fields( $settings );
		}

		/**
		 * Save settings.
		 *
		 * @since 1.0.0
		 */
		public function save() {
			global $current_section;
			$settings = $this->get_settings( $current_section );
			\WC_Admin_Settings::save_fields( $settings );
		}
	}
}