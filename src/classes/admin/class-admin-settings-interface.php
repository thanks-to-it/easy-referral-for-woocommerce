<?php
/**
 * Easy Referral for WooCommerce - Admin Settings Interface
 *
 * @version 1.0.2
 * @since   1.0.2
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Admin_Settings_Interface' ) ) {

	class Admin_Settings_Interface {
		/**
		 * Admin_Settings_Authenticity constructor.
		 *
		 * @version 1.0.2
		 * @since   1.0.2
		 *
		 */
		public function __construct() {
			add_filter( 'erwc_settings_interface', array( $this, 'get_settings' ) );
		}

		/**
		 * get_settings.
		 *
		 * @version 1.0.2
		 * @since   1.0.2
		 *
		 * @param $settings
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		function get_settings( $settings ) {
			$settings = array(

				// General
				array(
					'name' => __( 'Interface', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'Options regarding User Interface.', 'easy-referral-for-woocommerce' ). ERWC()->factory->get_instance( 'Admin\Admin_Settings' )->get_disabled_options_message(),
					'id'   => 'erwc_section_interface_general',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_interface_general'
				),
				array(
					'name' => __( 'Frontend', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'Options regarding User Interface', 'easy-referral-for-woocommerce' ),
					'id'   => 'erwc_section_interface_frontend',
				),
				array(
					'type'     => 'checkbox',
					'id'       => 'erwc_opt_period_filter',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'name'     => __( 'Period Filter', 'easy-referral-for-woocommerce' ),
					'desc'     => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'desc_tip' => __( 'Allows to filter Referrals by period (current month and previous month for now)', 'easy-referral-for-woocommerce' ),
					'default'  => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_interface_frontend'
				),
			);

			return $settings;
		}
	}
}