<?php
/**
 * Easy Referral for WooCommerce - WC Coupon
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\WC_Coupon' ) ) {

	class WC_Coupon {

		/**
		 * Initializes
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			/*add_filter( 'erwc_apply_discount_validation', function ( $validation, $wc_coupon ) {
				return $this->is_coupon_valid( $wc_coupon );
			}, 10, 2 );
			add_filter( 'erwc_apply_referral_code_validation', array( $this, 'check_coupon_before_applying_referral_code' ), 10, 3 );*/
		}

		/**
		 * check_coupon_before_applying_referral_code.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $valid
		 * @param $referrer_code
		 * @param $order
		 *
		 * @return bool
		 * @throws \Exception
		 */
		function check_coupon_before_applying_referral_code( $valid, $referrer_code, $order ) {
			$decoded = ERWC()->factory->get_referral_code_manager()->decode_referrer_code( $referrer_code );
			$code    = ERWC()->factory->get_referral_code_manager()->get_referral_code( $decoded['referral_code_id'] );

			// Depend on Coupon restrictions?
			if ( $code->copy_coupon_restrictions && ! empty( $code->coupon_code ) ) {
				$wc_coupon = new \WC_Coupon( $code->coupon_code );
				if (
					! $wc_coupon->get_id() ||
					( $wc_coupon->get_id() > 0 && ! $this->is_coupon_valid( $wc_coupon ) )
				) {
					return false;
				}
			}

			return $valid;
		}

		/**
		 * is_coupon_valid.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $coupon
		 * @param string $method
		 *
		 * @return bool
		 * @throws \Exception
		 */
		function is_coupon_valid( $coupon ) {
			$discounts      = new \WC_Discounts( WC()->cart );
			$valid_response = $discounts->is_coupon_valid( $coupon );
			if ( is_wp_error( $valid_response ) ) {
				return false;
			}
			return true;
		}
	}
}