<?php
/**
 * Easy Referral for WooCommerce - Admin Settings Authenticity
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Admin_Settings_Authenticity' ) ) {

	class Admin_Settings_Authenticity {
		public function __construct() {
			add_filter( 'erwc_settings_authenticity', array( $this, 'get_settings' ) );
		}

		function get_settings( $settings ) {
			$settings = array(

				// General
				array(
					'name' => __( 'Referral Authenticity', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'This section provides options that will try to check Referrals Authenticity.', 'easy-referral-for-woocommerce' ),
					'id'   => 'erwc_section_general',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_general'
				),

				// Status
				array(
					'name' => __( 'Authenticity Status', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'How Referrals will be set according to Authenticity Checking.', 'easy-referral-for-woocommerce' ).'<br />'.__( 'If an Authenticity Checking detects some possible fraud, the correspondent Referral will be set as not Reliable.', 'easy-referral-for-woocommerce' ).'<br />'.sprintf(
							__( 'You can edit the statuses as you want accessing <a href="%s">Referrals > Authenticity</a>', 'easy-referral-for-woocommerce' ),
							add_query_arg( array(
								'taxonomy'  => ERWC()->factory->get_referral_authenticity_tax()->tax_id,
								'post_type' => ERWC()->factory->get_referral_cpt()->cpt_id
							), admin_url( 'edit-tags.php' ) )
						),
					'id'   => 'erwc_section_general',
				),
				array(
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'id'       => 'erwc_opt_auth_reliable_status',
					'desc_tip' => __( 'When a Referral is considered Reliable', 'easy-referral-for-woocommerce' ),
					'name'     => __( 'Reliable Status', 'easy-referral-for-woocommerce' ),
					'options'  => ERWC()->factory->get_referral_authenticity_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_authenticity_tax()->get_probably_reliable_status_id(),
				),
				array(
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'id'       => 'erwc_opt_auth_not_reliable_status',
					'desc_tip' => __( 'When a Referral is not considered Reliable', 'easy-referral-for-woocommerce' ),
					'name'     => __( 'Not Reliable Status', 'easy-referral-for-woocommerce' ),
					'options'  => ERWC()->factory->get_referral_authenticity_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_authenticity_tax()->get_probably_not_reliable_status_id(),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_auth_status'
				),


				// Authentication Methods
				array(
					'name' => __( 'Authenticity Checking', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'Fraud detection mechanisms that will try to check if a Referral is reliable.', 'easy-referral-for-woocommerce' ),
					'id'   => 'erwc_section_auth_methods',
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'erwc_opt_checking_email',
					'name'    => __( 'Email Comparing', 'easy-referral-for-woocommerce' ),
					'desc'    => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'desc_tip'    => __( 'Checks if Referrer and Referee emails are identical.', 'easy-referral-for-woocommerce' ),
					'default' => 'yes',
				),
				array(
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'id'       => 'erwc_opt_checking_email_status',
					'desc_tip' => __( "Once the possible fraud is detected this is how it's going to be displayed.", 'easy-referral-for-woocommerce' ),
					//'desc'     => __( 'The way this checking will be displayed if valid', 'easy-referral-for-woocommerce' ),
					'name'     => __( ' &nbsp;', 'easy-referral-for-woocommerce' ),
					'options'  => ERWC()->factory->get_referral_checking_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_checking_tax()->get_probably_email_checking_id(),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_auth_methods'
				),
			);


			return $settings;
		}
	}
}