<?php
/**
 * Easy Referral for WooCommerce - Admin Settings General
 *
 * @version 1.0.2
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Admin_Settings_General' ) ) {

	class Admin_Settings_General {

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			add_filter( 'erwc_settings_general', array( $this, 'get_settings' ) );
		}

		/**
		 * update_salt_option_value.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function update_salt_option_value() {
			$salt = get_option( 'erwc_opt_salt', '' );
			if ( empty( $salt ) ) {
				$length        = 24;
				$random_string = substr( str_shuffle( str_repeat( $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil( $length / strlen( $x ) ) ) ), 1, $length );
				update_option( 'erwc_opt_salt', $random_string );
			}
		}

		/**
		 * get_settings.
		 *
		 * @version 1.0.2
		 * @since   1.0.0
		 *
		 * @param $settings
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		function get_settings( $settings ) {
			$settings = array(

				array(
					'name' => __( 'Easy Referral for WooCommerce', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'General Options.', 'easy-referral-for-woocommerce' ) . ERWC()->factory->get_instance( 'Admin\Admin_Settings' )->get_disabled_options_message(),
					'id'   => 'erwc_section_general',
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'erwc_opt_enable',
					'name'    => __( 'Enable Plugin', 'easy-referral-for-woocommerce' ),
					'desc'    => sprintf( __( 'Enable %s plugin', 'easy-referral-for-woocommerce' ), '<strong>' . __( 'Easy Referral for WooCommerce', 'easy-referral-for-woocommerce' ) . '</strong>' ),
					'default' => 'yes',
				),
				array(
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'id'       => 'erwc_opt_referral_status',
					'desc_tip' => __( 'The default status of a Referral after it has been created.', 'easy-referral-for-woocommerce' ),
					'desc'     => sprintf(
						__( 'You can edit the statuses as you want accessing <a href="%s">Referrals > Status</a>', 'easy-referral-for-woocommerce' ),
						add_query_arg( array(
							'taxonomy'  => ERWC()->factory->get_referral_status_tax()->tax_id,
							'post_type' => ERWC()->factory->get_referral_cpt()->cpt_id
						), admin_url( 'edit-tags.php' ) )
					),
					//'custom_attributes' => array( 'readonly' => 'readonly' ),
					'name'     => __( 'Defaut Referral Status', 'easy-referral-for-woocommerce' ),
					'options'  => ERWC()->factory->get_referral_status_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_status_tax()->get_probably_unpaid_status_id(),
				),
				array(
					'type'     => 'multiselect',
					'class'    => 'wc-enhanced-select',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'id'       => 'erwc_opt_referral_creation_order_status',
					'desc_tip' => __( 'The status an order needs to change to in order to create the Referral.', 'easy-referral-for-woocommerce' ),
					'name'     => __( 'Referral Creation Order Status', 'easy-referral-for-woocommerce' ),
					'options'  => wc_get_order_statuses(),
					'default'  => array('wc-completed')
				),
				array(
					'type'     => 'text',
					'id'       => 'erwc_opt_salt',
					'desc_tip' => __( 'A random string used to create the Referral Codes. If you change it the Referral Codes will also change!', 'easy-referral-for-woocommerce' ),
					//'custom_attributes' => array( 'readonly' => 'readonly' ),
					'name'     => __( 'Encryption Salt', 'easy-referral-for-woocommerce' ),
					'default'  => '',
				),
				array(
					'type'              => 'number',
					//'allowed_values'    => true === apply_filters( 'erwc_is_free_version', true ) ? array(1) : '',
					'allowed_values'    => array(1),
					'disable'           => apply_filters( 'erwc_is_free_version', true ),
					'id'                => 'erwc_opt_codes_total',
					'name'              => __( 'Referral Codes Amount', 'easy-referral-for-woocommerce' ),
					'desc_tip'          => __( 'The total amount of Referral Codes.', 'easy-referral-for-woocommerce' ),
					'custom_attributes' => array( 'min' => 1 ),
					'default'           => '1',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_general'
				),

			);

			$messages      = wp_list_filter( $settings, array( 'id' => 'erwc_section_general', 'type' => 'sectionend' ) );
			$code_settings = ERWC()->factory->get_referral_code_admin_settings();
			reset( $messages );
			$messages_index = key( $messages );
			array_splice( $settings, $messages_index + 1, 0, call_user_func_array( 'array_merge', $code_settings->get_settings() ) );

			return $settings;
		}
	}
}