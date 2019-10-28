<?php
/**
 * Easy Referral for WooCommerce - Code Admin Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

use ThanksToIT\ERWC\My_Account\Referral_Codes_Tab;
use ThanksToIT\ERWC\Referral_Code;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Referral_Code_Admin_Settings' ) ) {

	class Referral_Code_Admin_Settings {

		/**
		 * get_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		function get_settings() {
			$amount = get_option( 'erwc_opt_codes_total', 1 );

			$settings = array();
			for ( $i = 1; $i <= $amount; $i ++ ) {
				$settings[] = array(
					array(
						'name' => "Referral Code #{$i}",
						'type' => "title",
						'desc' => sprintf( __( "A unique code per user that will be displayed on <a href='%s'>My Account > Referral > Referral Codes</a>.", 'easy-referral-for-woocommerce' ), add_query_arg( array( 'section' => 'referral_codes' ), ERWC()->factory->get_referral_tab()->get_endpoint_url() ) ) . '<br />' . __( "It will be used to create a URL that once visited and converting on a purchase will reward the Referrer.", 'easy-referral-for-woocommerce' ),
						'id'   => "erwc_code_section[{$i}]",
					),
					array(
						'name'    => __( 'Enable', 'easy-referral-for-woocommerce' ),
						'desc'    => __( 'Enable', 'easy-referral-for-woocommerce' ),
						//'desc_tip'=> __( 'Enables this Referral Code', 'easy-referral-for-woocommerce' ),
						'type'    => "checkbox",
						'id'      => "erwc_opt_code_enabled[{$i}]",
						'default' => 'yes'
					),
					array(
						'name'     => __( 'Reward Type', 'easy-referral-for-woocommerce' ),
						'desc_tip' => __( 'The method the Referrer will be rewarded.', 'easy-referral-for-woocommerce' ),
						'id'       => "erwc_opt_code_reward_type[{$i}]",
						'class'    => 'chosen_select',
						'type'     => "select",
						'default'  => 'order_total_percentage',
						'options'  => ERWC()->factory->get_referral_code_manager()->get_reward_types(),
					),
					array(
						'name'     => __( 'Reward Value', 'easy-referral-for-woocommerce' ),
						'desc_tip' => __( 'Rewards the Referrer based on the Reward Type option.', 'easy-referral-for-woocommerce' ),
						'type'     => "number",
						'id'       => "erwc_opt_code_reward_value[{$i}]",
					),
					array(
						'name'     => __( 'Usage Limit per Referee', 'easy-referral-for-woocommerce' ),
						'desc_tip' => __( 'How many times this Referral Code can be used by the same Referee. Zero or empty allows unlimited usage.', 'easy-referral-for-woocommerce' ).' '.__( 'Uses billing email for guests, and user ID for logged in users.', 'easy-referral-for-woocommerce' ),
						'type'     => "number",
						'default'  => 1,
						'id'       => "erwc_opt_referee_usage_limit[{$i}]",
					),
					/*array(
						'name'     => __( 'Coupon Code', 'easy-referral-for-woocommerce' ),
						'desc_tip' => __( 'Bounds this Referral Code to a Coupon code, i.e., once applied this Referral Code can give the Referee a discount and might be only applied if the Coupon conditions are met.', 'easy-referral-for-woocommerce' ),
						'id'       => "erwc_opt_code_coupon[{$i}]",
						'type'     => "text",
					),
					array(
						'name'    => __( 'Apply Coupon', 'easy-referral-for-woocommerce' ),
						'desc'    => __( 'Apply Coupon Code', 'easy-referral-for-woocommerce' ),
						'desc_tip'=> __( 'Applies the Coupon Code when the Referral URL is visited, giving a discount to the Referee.', 'easy-referral-for-woocommerce' ),
						//'desc_tip'=> __( 'Applies the Coupon Code when the Referral URL is visited.', 'easy-referral-for-woocommerce' ).'<br />'.__( 'Enable it only if you want to give a discount to the Referee.', 'easy-referral-for-woocommerce' ),
						'id'      => "erwc_opt_code_coupon_apply[{$i}]",
						'type'    => "checkbox",
						'default' => 'yes'
					),
					array(
						'name'     => __( 'Use Coupon Restrictions', 'easy-referral-for-woocommerce' ),
						'desc'     => __( 'Use Coupon restrictions', 'easy-referral-for-woocommerce' ),
						'desc_tip' => __( 'The Referral Code will only be considered valid if Coupon conditions are met.', 'easy-referral-for-woocommerce' ).'<br />'.__( '"Usage Restriction" and "Usage Limits" Coupon tabs are in your favor.', 'easy-referral-for-woocommerce' ),
						//'desc_tip' => __( 'The Referral Code will only be considered valid if Coupon conditions are met.', 'easy-referral-for-woocommerce' ).'<br />'.__( '"Usage Restriction" and "Usage Limits" Coupon tabs are in your favor.', 'easy-referral-for-woocommerce' ).'<br />'.__( 'Enable it to make sure your Referral Code won\'t be used multiple times by the same Referee.', 'easy-referral-for-woocommerce' ),
						'id'       => "erwc_opt_code_coupon_copy_restrictions[{$i}]",
						'type'     => "checkbox",
						'default'  => 'yes'
					),*/
					array(
						'type' => 'sectionend',
						'id'   => "erwc_code_section[{$i}]"
					)
				);
			}

			return $settings;
		}

		/**
		 * get_referral_codes_tab_url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return mixed
		 * @throws \ReflectionException
		 */
		function get_referral_codes_tab_url() {
			return ERWC()->factory->get_referral_codes_tab()->get_endpoint_url();
		}


	}
}