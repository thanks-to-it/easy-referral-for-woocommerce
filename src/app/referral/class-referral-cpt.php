<?php
/**
 * Easy Referral for WooCommerce - Referral Post Type
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Referral;

use ThanksToIT\ERWC\Referral\Referral_Status_Tax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Referral\Referral_CPT' ) ) {

	class Referral_CPT {
		public $cpt_id = 'erwc-referral';

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			add_action( 'woocommerce_order_status_changed', array( $this, 'create_referral_from_order' ), 10, 3 );
			add_action( 'init', array( $this, 'register_post_type' ) );

		}

		/**
		 * handle_admin.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function handle_admin() {

		}

		/**
		 * get_empty_referrals_message.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function get_empty_referrals_message() {
			return sprintf(
				       __(
					       'It seems that you don\'t have any Referrals yet. Please try to share your <a href="%s">Referral Code URL</a> with your friends.',
					       'easy-referral-for-woocommerce'
				       ),
				       add_query_arg(
					       array( 'section' => 'referral_codes' ),
					       ERWC()->factory->get_referral_tab()->get_endpoint_url()
				       )
			       ) . ' ' . __( 'If they visit the URL and make a purchase you\'ll have your first Referral.', 'easy-referral-for-woocommerce' );
		}

		/**
		 * get_referrals_from_user_id.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $user_id
		 * @param null $args
		 *
		 * @return \WP_Query
		 */
		function get_referrals_from_user_id( $user_id, $args = null ) {
			$args                = wp_parse_args( $args, array(
				'post_type'      => $this->cpt_id,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'     => '_erwc_referrer_id',
						'value'   => $user_id,
						'compare' => '=',
					),
				),
			) );
			/*$query_string_period = isset( $_GET['period'] ) ? $_GET['period'] : 'current_month';
			switch ( $query_string_period ) {
				case 'previous_month':
					$today              = getdate();
					$args['date_query'] = array(
						'year'  => $today['year'],
						'month' => $today['mon'] - 1,
					);
					break;
				default :
					$today              = getdate();
					$args['date_query'] = array(
						'year'  => $today['year'],
						'month' => $today['mon'],
					);
					break;
			}*/
			$args = apply_filters('erwc_referrals_from_user_id_args',$args,$user_id);
			$the_query = new \WP_Query( $args );
			return $the_query;
		}

		/**
		 * get_total_sum_by_referral_status.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param \WP_Term $term
		 * @param array $args
		 *
		 * @return int|mixed|string
		 */
		function get_total_sum_by_referral_status( \WP_Term $term, $args = array() ) {
			$args         = wp_parse_args( $args, array(
				'user_id'      => null,
				'format_price' => false
			) );
			$format_price = $args['format_price'];

			$total   = 0;
			$user_id = $args['user_id'];
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			global $wpdb;
			$sql =
			"
				SELECT SUM(pm2.meta_value) AS total
				FROM {$wpdb->posts}
				JOIN {$wpdb->postmeta} AS pm ON (pm.post_id = wp_posts.ID AND pm.meta_key='_erwc_referrer_id' AND pm.meta_value=%d)
				JOIN {$wpdb->postmeta} AS pm2 ON (pm2.post_id = wp_posts.ID AND pm2.meta_key='_erwc_total_reward_value' AND pm2.meta_value <> '' AND (pm2.meta_value + 0) > 0)
				JOIN {$wpdb->term_relationships} AS tr ON tr.object_id = ID
				JOIN {$wpdb->term_taxonomy} AS tt ON (tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy='erwc-status')
				JOIN {$wpdb->terms} AS t ON (t.term_id = tt.term_id AND t.slug='%s')
				WHERE post_type = 'erwc-referral' AND post_status = 'publish'
			";
			$sql = apply_filters('erwc_referral_total_sum_by_status_sql', $sql, $term, $args);
			/*$query_string_period = isset( $_GET['period'] ) ? $_GET['period'] : 'current_month';
			$today               = getdate();
			switch ( $query_string_period ) {
				case 'previous_month':
					$year  = $today['year'];
					$month = $today['mon'] - 1;
					break;
				default :
					$year  = $today['year'];
					$month = $today['mon'];
					break;
			}
			$sql   .= "AND ( ( YEAR( {$wpdb->posts}.post_date ) = {$year} AND MONTH( {$wpdb->posts}.post_date ) = {$month} ) )";*/
			$total = $wpdb->get_var( $wpdb->prepare( $sql,
				$user_id, $term->slug
			) );

			if ( $format_price ) {
				return wc_price( $total );
			} else {
				return $total;
			}
		}

		/**
		 * create_referral_from_order.
		 *
		 * @version 1.0.2
		 * @since   1.0.0
		 *
		 * @param $order_id
		 * @param $from
		 * @param $to
		 *
		 * @throws \ReflectionException
		 */
		function create_referral_from_order( $order_id, $from, $to ) {
			$referrer_code = get_post_meta( $order_id, '_erwc_referrer_code', true );
			if (
				empty( $referrer_code ) ||
				! in_array( $to, apply_filters( 'erwc_referral_creation_order_status', array( 'completed' ) ) )
			) {
				return;
			}
			$decoded            = ERWC()->factory->get_referral_code_manager()->decode_referrer_code( $referrer_code );
			$code               = ERWC()->factory->get_referral_code_manager()->get_referral_code( $decoded['referral_code_id'] );
			$referral_id        = $this->get_referral_by_order_id( $order_id );
			$order              = wc_get_order( $order_id );
			$total_reward_value = ERWC()->factory->get_referral_code_manager()->calculate_total_reward_value( $referrer_code, $order );

			// Metas to add
			$meta_input = array();
			foreach ( get_object_vars( $code ) as $meta_key => $meta_value ) {
				$meta_input[ '_erwc_' . $meta_key ] = $meta_value;
			}
			$meta_input['_erwc_referrer_code']      = $referrer_code;
			$meta_input['_erwc_referrer_id']        = $decoded['referrer_id'];
			$meta_input['_erwc_order_id']           = $order_id;
			$meta_input['_erwc_currency']           = get_post_meta( $order_id, '_order_currency', true );
			$meta_input['_erwc_total_reward_value'] = $total_reward_value;
			$meta_input                             = apply_filters( 'erwc_referral_meta_input', $meta_input, $referrer_code, $order_id );

			$creating = true;
			if ( false !== $referral_id ) {
				$creating = false;
				foreach ( $meta_input as $meta_key => $meta_value ) {
					update_post_meta( $referral_id, $meta_key, $meta_value );
				}
			} else {
				$referral_id = wp_insert_post( array(
					'post_title'  => __( 'Referral', 'referral-system-for-woocommerce' ),
					'post_type'   => $this->cpt_id,
					'post_date'   => $order->get_date_modified()->date( "Y-m-d H:i:s" ),
					'post_status' => 'publish',
					'meta_input'  => $meta_input
				), true );
				wp_update_post( array(
					'ID'         => $referral_id,
					'post_title' => __( 'Referral' ) . ' ' . $referral_id
				) );

				// Update order with referral id
				update_post_meta( $order_id, '_erwc_referral_cpt_id', $referral_id );

				// Set as default status (probably unpaid)
				$referral_status = ERWC()->factory->get_referral_status_tax();
				wp_set_object_terms( $referral_id, (int) get_option( 'erwc_opt_referral_status', ERWC()->factory->get_referral_status_tax()->get_probably_unpaid_status_id() ), $referral_status->tax_id );
			}

			do_action( 'erwc_creating_or_updating_referral', $referral_id, $referrer_code, $order_id, $creating );
		}

		/**
		 * get_referral_by_order_id.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $order_id
		 *
		 * @return bool
		 */
		public function get_referral_by_order_id( $order_id ) {
			$the_query = new \WP_Query( array(
				'post_type'   => $this->cpt_id,
				'post_status' => 'publish',
				'fields'      => 'ids',
				'meta_query'  => array(
					array(
						'key'     => '_erwc_order_id',
						'value'   => $order_id,
						'compare' => '=',
					),
				),
			) );

			foreach ( $the_query->posts as $post ) {
				return $post;
			}

			// Restore original Post Data
			wp_reset_postdata();

			return false;
		}

		/**
		 * register_post_type.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function register_post_type() {
			$labels = array(
				'name'               => __( 'Referrals', 'easy-referral-for-woocommerce' ),
				'singular_name'      => __( 'Referral', 'easy-referral-for-woocommerce' ),
				'menu_name'          => __( 'Referrals', 'easy-referral-for-woocommerce' ),
				'name_admin_bar'     => __( 'Referral', 'easy-referral-for-woocommerce' ),
				'add_new'            => __( 'Add New', 'easy-referral-for-woocommerce' ),
				'add_new_item'       => __( 'Add New Referral', 'easy-referral-for-woocommerce' ),
				'new_item'           => __( 'New Referral', 'easy-referral-for-woocommerce' ),
				'edit_item'          => __( 'Edit Referral', 'easy-referral-for-woocommerce' ),
				'view_item'          => __( 'View Referral', 'easy-referral-for-woocommerce' ),
				'all_items'          => __( 'Referrals', 'easy-referral-for-woocommerce' ),
				'search_items'       => __( 'Search Referrals', 'easy-referral-for-woocommerce' ),
				'parent_item_colon'  => __( 'Parent Referrals:', 'easy-referral-for-woocommerce' ),
				'not_found'          => __( 'No Referrals found.', 'easy-referral-for-woocommerce' ),
				'not_found_in_trash' => __( 'No Referrals found in Trash.', 'easy-referral-for-woocommerce' ),
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'easy-referral-for-woocommerce' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				//'show_in_menu'       => 'edit.php?trswc=trswc',
				'query_var'          => false,
				'rewrite'            => array( 'slug' => 'referral' ),
				//'capability_type'    => 'alg_mpwc_commission',
				'capabilities'       => array(
					'edit_post'          => 'activate_plugins',
					'read_post'          => 'activate_plugins',
					'delete_post'        => 'activate_plugins',
					'edit_posts'         => 'activate_plugins',
					'edit_others_posts'  => 'activate_plugins',
					'delete_posts'       => 'activate_plugins',
					'publish_posts'      => 'activate_plugins',
					'read_private_posts' => 'activate_plugins'
				),
				'map_meta_cap'       => true,
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				//'menu_icon'          => 'dashicons-cart',
				'menu_icon'          => 'dashicons-randomize',
				'supports'           => array( 'title' ),
			);

			register_post_type( 'erwc-referral', $args );
			do_action( 'erwc_referral_cpt_created', $this->cpt_id );
		}
	}
}