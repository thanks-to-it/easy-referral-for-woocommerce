<?php
/**
 * Easy Referral for WooCommerce - Admin Settings Authenticity
 *
 * @version 1.0.5
 * @since   1.0.5
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Admin_Settings_Codes' ) ) {

	class Admin_Settings_Codes {

		/**
		 * Admin_Settings_Authenticity constructor.
		 *
		 * @version 1.0.5
		 * @since   1.0.5
		 *
		 */
		public function __construct() {
			add_filter( 'erwc_settings_codes', array( $this, 'get_settings' ) );
			add_action( 'admin_init', function () {
				global $pagenow;
				if (
					! is_admin()
					|| 'admin.php' != $pagenow
					|| ! isset( $_REQUEST['page'] )
					|| 'wc-settings' != $_REQUEST['page']
					|| ! isset( $_REQUEST['tab'] )
					|| 'erwc' != $_REQUEST['tab']
					|| isset( $_REQUEST['section'] )
				) {
					return;
				}
				wp_redirect( admin_url( '/admin.php?page=wc-settings&tab=erwc&section=codes' ) );
				exit;
			} );
		}

		/**
		 * get_settings.
		 *
		 * @version 1.0.5
		 * @since   1.0.5
		 *
		 * @param $settings
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		function get_settings( $settings ) {
			$settings = array(

				array(
					'name' => __( 'Referral Codes', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => sprintf( __( "Referral Codes are unique per user and will be displayed on <a href='%s'>My Account > Referral > Referral Codes</a>.", 'easy-referral-for-woocommerce' ), add_query_arg( array( 'section' => 'referral_codes' ), ERWC()->factory->get_referral_tab()->get_endpoint_url() ) ) . '<br />' . __( "They will be used by Referrers to create a URL that once shared and visited will generate a Referral if the Referee makes a purchase.", 'easy-referral-for-woocommerce' ),
					'id'   => 'erwc_section_referral_codes',
				),
				array(
					'name'              => __( 'Referral Codes Amount', 'easy-referral-for-woocommerce' ),
					'type'              => 'number',
					//'allowed_values'    => true === apply_filters( 'erwc_is_free_version', true ) ? array(1) : '',
					'allowed_values'    => array(1),
					'disable'           => apply_filters( 'erwc_is_free_version', true ),
					'id'                => 'erwc_opt_codes_total',
					'desc_tip'          => __( 'The total amount of Referral Codes.', 'easy-referral-for-woocommerce' ),
					'custom_attributes' => array( 'min' => 1 ),
					'default'           => '1',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_referral_codes'
				),
			);

			$messages      = wp_list_filter( $settings, array( 'id' => 'erwc_section_referral_codes', 'type' => 'sectionend' ) );
			$code_settings = ERWC()->factory->get_referral_code_admin_settings();
			reset( $messages );
			$messages_index = key( $messages );
			array_splice( $settings, $messages_index + 1, 0, call_user_func_array( 'array_merge', $code_settings->get_settings() ) );

			return $settings;
		}
	}
}