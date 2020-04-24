<?php
/**
 * Easy Referral for WooCommerce - Sharer
 *
 * @version 1.0.6
 * @since   1.0.6
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Sharer' ) ) {

	class Sharer {

		/**
		 * init.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 */
		function init() {
			//add_filter( 'erwc_referral_codes_table_columns', array( $this, 'add_share_column_to_codes_table' ) );
			add_filter( 'erwc_referral_codes_table_column_value', array( $this, 'add_share_value_to_codes_table' ), 10, 4 );
		}

		/**
		 * add_share_column_to_codes_table.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		function add_share_column_to_codes_table( $columns ) {
			$columns['share'] = __( 'Share', 'easy-referral-for-woocommerce' );
			return $columns;
		}

		/**
		 * add_share_value_to_codes_table.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @param $value
		 * @param $column
		 * @param $code_id
		 * @param $code
		 *
		 * @return mixed
		 * @throws \ReflectionException
		 */
		function add_share_value_to_codes_table( $value, $column, $code_id, $code ) {
			$code_manager = ERWC()->factory->get_referral_code_manager();
			$referrer_code = $code_manager->encode( $code_id, get_current_user_id() );
			if ( 'share' === $column ) {
				$value = '<button data-url="'.esc_url( $code_manager->get_referrer_code_url( $referrer_code ) ).'" class="erwc-action-btn erwc-share-link-btn"><i class="dashicons dashicons-share share"></i></button>';
			}
			return $value;
		}
	}
}