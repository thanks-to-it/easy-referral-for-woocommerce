<?php
/**
 * Easy Referral for WooCommerce - Order Referrals Column
 *
 * @version 1.0.6
 * @since   1.0.6
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Orders_Referrals_Column' ) ) {
	class Orders_Referrals_Column {

		/**
		 * @version 1.0.6
		 * @since   1.0.6
		 */
		function init() {
			// Discount Column on Orders Page
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_column' ) );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'populate_column' ), 10, 2 );
			add_filter( 'manage_edit-shop_order_sortable_columns', array( $this, 'sortable_columns' ) );
			add_action('pre_get_posts',array($this,'pre_get_posts'));
		}

		/**
		 * pre_get_posts.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @param $wp_query
		 */
		function pre_get_posts( $wp_query ) {
			if (
				! is_admin() ||
				'no' == get_option( 'erwc_opt_referrals_col_orders_list', 'no' ) ||
				! function_exists( 'get_current_screen' ) ||
				empty( $screen = get_current_screen() ) ||
				'edit-shop_order' != $screen->id ||
				'shop_order' != $screen->post_type ||
				! $wp_query->is_main_query() ||
				'shop_order' !== $wp_query->get( 'post_type' )
			) {
				return;
			}
			if ( 'erwc_referral' == $wp_query->get( 'orderby' ) ) {
				$wp_query->set( 'orderby', 'meta_value' );
				//$wp_query->set( 'meta_key', '_erwc_referrer_code' );
				$meta_query   = empty( $meta_query = $wp_query->get( 'meta_query' ) ) ? array() : $meta_query;
				$meta_query[] = array(
					'relation' => 'OR',
					array(
						'key'     => '_erwc_referrer_code',
						'compare' => 'NOT EXISTS'
					),
					array(
						'key'     => '_erwc_referrer_code',
						'compare' => 'EXISTS'
					)
				);
				$wp_query->set( 'meta_query', $meta_query );
			}
		}

		/**
		 * add_column.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @param $columns
		 *
		 * @return array
		 */
		function add_column( $columns ) {
			if ( 'no' == get_option( 'erwc_opt_referrals_col_orders_list', 'no' ) ) {
				return $columns;
			}

			$position = get_option( 'alg_wc_ev_admin_column_position', 4 );
			$columns  = array_slice( $columns, 0, $position, true ) +
			            array( 'erwc_referral' => __( 'Referral', 'easy-referral-for-woocommerce' ) ) +
			            array_slice( $columns, $position, count( $columns ) - 1, true );
			return $columns;
		}

		/**
		 * populate_column.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @param $column
		 * @param $post_id
		 *
		 * @return mixed
		 */
		function populate_column( $column, $post_id ) {
			if ( 'erwc_referral' === $column ) {
				if ( empty( $referral_code = get_post_meta( $post_id, '_erwc_referrer_code', true ) ) ) {
					return $column;
				}
				$replace = array(
					'{dashicon}' => empty( $referral_cpt_id = get_post_meta( $post_id, '_erwc_referral_cpt_id', true ) ) ? 'dashicons-ellipsis' : 'dashicons-yes-alt',
					'{referral}' => ! empty( $referral_cpt_id ) ? '' : '',
					'{title}'    => ! empty( $referral_cpt_id ) ? __( 'A Referral has been created.', 'easy-referral-for-woocommerce' ) : sprintf( __( 'A Referral can be created if order status changes to (%s).', 'easy-referral-for-woocommerce' ), implode( ',', apply_filters( 'erwc_referral_creation_order_status', array( 'completed' ) ) ) )
				);
				$output  = '<span title="{title}" class="dashicons-before {dashicon}"></span>{referral}';
				$output  = str_replace( array_keys( $replace ), $replace, $output );
				echo $output;
			}
		}

		/**
		 * sortable_columns.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		function sortable_columns( $columns ) {
			if ( 'no' == get_option( 'erwc_opt_referrals_col_orders_list', 'no' ) ) {
				return $columns;
			}
			$columns['erwc_referral'] = 'erwc_referral';
			return $columns;
		}
	}
}