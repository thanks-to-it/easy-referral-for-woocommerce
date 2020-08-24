<?php
/**
 * Easy Referral for WooCommerce - Admin Settings General
 *
 * @version 1.0.6
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

use ThanksToIT\ERWC\Functions;
use ThanksToIT\ERWC\Pro\Manual_Referral_Code;

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

		function get_default_cart_field_container() {
			if ( apply_filters( 'erwc_is_free_version', true ) ) {
				return '';
			}else{
				$manual_referral_code = new Manual_Referral_Code();
				return $manual_referral_code->get_default_cart_field_container();
			}			
		}

		/**
		 * get_settings.
		 *
		 * @version 1.0.6
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
					'name' => __( 'General', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'General Options from Easy Referral for WooCommerce plugin.', 'easy-referral-for-woocommerce' ) . ERWC()->factory->get_instance( 'Admin\Admin_Settings' )->get_disabled_options_message(),
					'id'   => 'erwc_section_general',
				),
				array(
					'name'    => __( 'Enable Plugin', 'easy-referral-for-woocommerce' ),
					'type'    => 'checkbox',
					'id'      => 'erwc_opt_enable',
					'desc'    => sprintf( __( 'Enable %s plugin', 'easy-referral-for-woocommerce' ), '<strong>' . __( 'Easy Referral for WooCommerce', 'easy-referral-for-woocommerce' ) . '</strong>' ),
					'default' => 'yes',
				),
				array(
					'name'     => __( 'Encryption Salt', 'easy-referral-for-woocommerce' ),
					'type'     => 'text',
					'id'       => 'erwc_opt_salt',
					'desc_tip' => __( 'A random string used to create the Referral Codes. If you change it the Referral Codes will also change!', 'easy-referral-for-woocommerce' ),
					//'custom_attributes' => array( 'readonly' => 'readonly' ),
					'default'  => '',
				),
				array(
					'name'     => __( 'Defaut Referral Status', 'easy-referral-for-woocommerce' ),
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
					'options'  => ERWC()->factory->get_referral_status_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_status_tax()->get_probably_unpaid_status_id(),
				),
				array(
					'name'     => __( 'Referral Creation Order Status', 'easy-referral-for-woocommerce' ),
					'type'     => 'multiselect',
					'class'    => 'wc-enhanced-select',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'id'       => 'erwc_opt_referral_creation_order_status',
					'desc_tip' => __( 'The status an order needs to change to in order to create the Referral.', 'easy-referral-for-woocommerce' ),
					'options'  => wc_get_order_statuses(),
					'default'  => array('wc-completed')
				),
				array(
					'name'     => __( 'Referral URL Param', 'easy-referral-for-woocommerce' ),
					'type'     => 'text',
					'id'       => 'erwc_opt_referral_url_param',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'desc_tip' => __( 'The parameter responsible for generating the Referral URL.', 'easy-referral-for-woocommerce' ),
					'desc'     => 'e.g. '.ERWC()->factory->get_referral_code_manager()->get_referrer_code_url('abc123'),
					'default'  => ERWC()->factory->get_referral_code_manager()->get_referral_url_param(),
				),
				array(
					'name'     => __( 'Show referrals on orders list', 'easy-referral-for-woocommerce' ),
					'type'     => 'checkbox',
					'id'       => 'erwc_opt_referrals_col_orders_list',
					'desc_tip' => sprintf( __( 'Display a new column on the <a href="%s">orders list page</a> showing which orders contain referrals.', 'easy-referral-for-woocommerce' ), admin_url( 'edit.php?post_type=shop_order' ) ),
					'desc'     => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'default'  => 'no'
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_general'
				),
				array(
					'name' => __( 'Referral Info on Frontend', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => sprintf( __( 'Referral info displayed on frontend mostly inside the <a href="%s">Referral Tab</a> on <a href="%s">My Account</a>.', 'easy-referral-for-woocommerce' ), ERWC()->factory->get_referral_tab()->get_endpoint_url(), get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ),
					'id'   => 'erwc_section_referral_info_frontend',
				),
				array(
					'type'     => 'checkbox',
					'id'       => 'erwc_opt_interface_referral_codes_dashboard',
					'name'     => __( 'Codes on the Dashboard', 'easy-referral-for-woocommerce' ),
					'desc'     => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Also displays Referral Codes on <a href="%s">My Account > Dashboard</a>. ', 'easy-referral-for-woocommerce' ), get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ),
					'default'  => 'yes',
				),
				array(
					'type'     => 'checkbox',
					'id'       => 'erwc_opt_referrer_details_section',
					'name'     => __( 'Referrer Details Section', 'easy-referral-for-woocommerce' ),
					'desc'     => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'desc_tip' => sprintf( __( 'Displays a <a href="%s">section</a> inside the Referrals tab with fields related to payment.', 'easy-referral-for-woocommerce' ), ERWC()->factory->get_referral_tab()->get_endpoint_url() . '?section=referrer_details' ).'<br />'.__( 'The fields will be also visible on the user profile page.', 'easy-referral-for-woocommerce' ),
					'default'  => 'yes',
				),
				array(
					'type'     => 'checkbox',
					'id'       => 'erwc_opt_interface_period_filter',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'name'     => __( 'Period Filter', 'easy-referral-for-woocommerce' ),
					'desc'     => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'desc_tip' => __( 'Allows to filter Referrals by period (current month and previous month for now).', 'easy-referral-for-woocommerce' ),
					'default'  => 'no',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_referral_info_frontend'
				),
				array(
					'name' => __( 'Apply Manually', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'Instead of only accessing a referral link URL, a Referee will be able to also apply the Referral Code manually.', 'easy-referral-for-woocommerce' ),
					'id'   => 'erwc_apply_manually',
				),
				array(
					'name'              => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'type'              => 'checkbox',
					'disable'           => apply_filters( 'erwc_is_free_version', true ),
					'id'                => 'erwc_opt_apply_code_manually',
					'desc'              => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'default'           => 'no',
				),
				array(
					'name'        => __( 'Cart Position', 'easy-referral-for-woocommerce' ),
					'type'        => 'text',
					'id'          => 'erwc_opt_apply_code_manually_cart_pos',
					'desc_tip'    => __( 'The action hook used to position the Referral Code field on the cart page.', 'easy-referral-for-woocommerce' ),
					'disable'     => apply_filters( 'erwc_is_free_version', true ),
					'placeholder' => 'woocommerce_cart_actions',
					'default'     => 'woocommerce_cart_actions',
				),
				array(
					'name'        => __( 'Cart Position Priority', 'easy-referral-for-woocommerce' ),
					'type'        => 'number',
					'id'          => 'erwc_opt_apply_code_manually_cart_pos_priority',
					'desc_tip'    => __( 'The priority used on the action hook to position the Referral Code field on the cart page.', 'easy-referral-for-woocommerce' ),
					'disable'     => apply_filters( 'erwc_is_free_version', true ),
					'default'     => 10,
				),
				array(
					'name'        => __( 'Cart Template', 'easy-referral-for-woocommerce' ),
					'type'        => 'textarea',
					'desc'        => Functions::format_template_variables( array( 'nonce', 'input', 'title', 'btn_title' ) ),
					'id'          => 'erwc_opt_apply_code_manually_cart_template',
					'desc_tip'    => __( 'The template used to display the Referral Code field on the cart page.', 'easy-referral-for-woocommerce' ),
					'disable'     => apply_filters( 'erwc_is_free_version', true ),
					'default'     => $this->get_default_cart_field_container(),
					'css'         =>'width:100%;min-height:135px',
				),
				array(
					'name'        => __( 'Checkout Position', 'easy-referral-for-woocommerce' ),
					'type'        => 'text',
					'id'          => 'erwc_opt_apply_code_manually_checkout_pos',
					'desc_tip'    => __( 'The action hook used to position the Referral Code field on the checkout page.', 'easy-referral-for-woocommerce' ),
					'disable'     => apply_filters( 'erwc_is_free_version', true ),
					'placeholder' => 'woocommerce_before_checkout_form',
					'default'     => 'woocommerce_before_checkout_form',
				),
				array(
					'name'        => __( 'Checkout Position Priority', 'easy-referral-for-woocommerce' ),
					'type'        => 'number',
					'id'          => 'erwc_opt_apply_code_manually_checkout_pos_priority',
					'desc_tip'    => __( 'The priority used on the action hook to position the Referral Code field on the checkout page.', 'easy-referral-for-woocommerce' ),
					'disable'     => apply_filters( 'erwc_is_free_version', true ),
					'default'     => 10,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_apply_manually'
				),
				/*array(
					'name' => __( 'Referrals', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'Referrals are the proof that a Referee visited a URL shared by a Referrer and has made a purchase.', 'easy-referral-for-woocommerce' ) . '<br />' . sprintf( __( 'The Shop Owner can view all the global Referrals accessing <a href="%s">Referrals</a>.', 'easy-referral-for-woocommerce' ), admin_url( 'edit.php?post_type=' . ERWC()->factory->get_referral_cpt()->cpt_id ) ) . '<br />' . sprintf( __( 'Referrers can see their own Referrals on <a href="%s">My Account > Referral > Referrals</a>.', 'easy-referral-for-woocommerce' ), ERWC()->factory->get_referral_tab()->get_endpoint_url() ),
					'id'   => 'erwc_section_referrals',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_referrals'
				),*/



				array(
					'name' => __( 'Rewards as Discount', 'easy-referral-for-woocommerce' ),
					'type' => 'title',
					'desc' => __( 'Rewards from Referrals can be applied as discounts to Referrers on their next purchases.', 'easy-referral-for-woocommerce' ),
					'id'   => 'erwc_section_rewards_as_discount',
				),
				array(
					'name'    => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'type'    => 'checkbox',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'id'      => 'erwc_opt_rewards_as_discount_enable',
					'desc'    => __( 'Enable', 'easy-referral-for-woocommerce' ),
					'default' => 'no',
				),
				array(
					'name'     => __( 'Available Status', 'easy-referral-for-woocommerce' ),
					'type'     => 'select',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'id'       => 'erwc_opt_rewards_as_discount_available_status',
					'desc_tip' => __( 'Status that will be used to consider a Referral Reward available for discount.', 'easy-referral-for-woocommerce' ),
					'options'  => ERWC()->factory->get_referral_status_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_status_tax()->get_probably_unpaid_status_id(),
				),
				array(
					'name'     => __( 'Authenticity Available Status', 'easy-referral-for-woocommerce' ),
					'type'     => 'select',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'id'       => 'erwc_opt_rewards_as_discount_auth_available_status',
					'desc_tip' => __( 'Authencitiy Status that will be used to consider a Referral Reward available for discount.', 'easy-referral-for-woocommerce' ),
					'options'  => ERWC()->factory->get_referral_authenticity_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_authenticity_tax()->get_probably_reliable_status_id()
				),
				array(
					'name'     => __( 'Unavailable Status', 'easy-referral-for-woocommerce' ),
					'type'     => 'select',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'id'       => 'erwc_opt_rewards_as_discount_unavailable_status',
					'desc_tip' => __( 'Status that will be used to consider a Referral Reward unavailable for discount. The Referral will also change to this status after the discount has been applied.', 'easy-referral-for-woocommerce' ),
					'options'  => ERWC()->factory->get_referral_status_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'default'  => ERWC()->factory->get_referral_status_tax()->get_probably_paid_status_id(),
				),
				array(
					'name'     => __( 'Discount Column on Orders', 'easy-referral-for-woocommerce' ),
					'type'     => 'text',
					'disable'  => apply_filters( 'erwc_is_free_version', true ),
					'id'       => 'erwc_opt_rewards_as_discount_orders_column',
					'desc_tip' => __( 'Adds a column on the Orders List page displaying discounts from referral rewards.' ).'<br />'.__( 'Leave it empty to disable.' ),
					'options'  => ERWC()->factory->get_referral_status_tax()->get_registered_terms( array( 'get_only' => 'id_and_title' ) ),
					'placeholder' => __( 'Discount', 'easy-referral-for-woocommerce' ),
					'default'  => '',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'erwc_section_rewards_as_discount'
				),

			);

			/*$messages      = wp_list_filter( $settings, array( 'id' => 'erwc_section_referral_codes', 'type' => 'sectionend' ) );
			$code_settings = ERWC()->factory->get_referral_code_admin_settings();
			reset( $messages );
			$messages_index = key( $messages );
			array_splice( $settings, $messages_index + 1, 0, call_user_func_array( 'array_merge', $code_settings->get_settings() ) );*/

			return $settings;
		}
	}
}