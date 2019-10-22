<?php
/**
 * Easy Referral for WooCommerce - Referral Status
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Referral;


use ThanksToIT\ExtendedWP\WP_Tax_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Referral\Referral_Status_Tax' ) ) {

	class Referral_Status_Tax {

		public $tax_id = 'erwc-status';

		/**
		 * handle_admin.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			add_action( 'erwc_referral_cpt_created', array( $this, 'register_taxonomy' ) );
			register_activation_hook( ERWC()->plugin_info['filesystem_path'], array( $this, 'create_terms_on_plugin_init' ) );
		}

		/**
		 * get_probably_unpaid_status_id.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_probably_unpaid_status_id() {
			$automatically_created_status = get_option( 'erwc_referral_status_terms', array() );
			if ( count( $automatically_created_status ) > 0 ) {
				return $automatically_created_status[0];
			} else {
				return '';
			}
		}

		/**
		 * create_terms_on_plugin_init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function create_terms_on_plugin_init() {
			$tax_manager = new WP_Tax_Manager();
			$tax_manager->create_terms( array(
				'tax_id'      => $this->tax_id,
				'terms'       => $this->get_default_terms(),
				'check_option' => 'erwc_referral_status_terms'
			) );
		}

		/**
		 * get_statuses.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param array $args
		 *
		 * @return array|int|\WP_Error
		 */
		function get_registered_terms( $args = array() ) {
			$args  = wp_parse_args( $args, array(
				'taxonomy'   => $this->tax_id,
				'get_only'   => '',
				'hide_empty' => false,
			) );
			$terms = get_terms( $args );
			if ( isset( $args['get_only'] ) && $args['get_only'] == 'id_and_title' ) {
				$terms = wp_list_pluck( $terms, 'name', 'term_id' );
			}
			if ( isset( $args['get_only'] ) && $args['get_only'] == 'slug_and_title' ) {
				$terms = wp_list_pluck( $terms, 'name', 'slug' );
			}
			return $terms;
		}

		/**
		 * get_default_terms.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		function get_default_terms() {
			return array(
				array(
					'slug'        => 'unpaid',
					'label'       => __( 'Unpaid', 'easy-referral-for-woocommerce' ),
					'description' => __( 'When a Referral has not been paid yet.', 'easy-referral-for-woocommerce' ),
				),
				array(
					'slug'        => 'paid',
					'label'       => __( 'Paid', 'easy-referral-for-woocommerce' ),
					'description' => __( 'After a Referral has been paid.', 'easy-referral-for-woocommerce' ),
				),
				array(
					'slug'        => 'rejected',
					'label'       => __( 'Rejected', 'easy-referral-for-woocommerce' ),
					'description' => __( 'When a Referral is not going to be paid for being considered a fraud or for any other reasons', 'easy-referral-for-woocommerce' ),
				),
			);
		}

		/**
		 * register_taxonomy.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param null $referral_cpt_id
		 */
		public function register_taxonomy( $referral_cpt_id = null ) {
			// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name'              => __( 'Status', 'marketplace-for-woocommerce' ),
				'singular_name'     => __( 'Status', 'marketplace-for-woocommerce' ),
				'search_items'      => __( 'Search Status', 'marketplace-for-woocommerce' ),
				'all_items'         => __( 'All Status', 'marketplace-for-woocommerce' ),
				'parent_item'       => __( 'Parent Status', 'marketplace-for-woocommerce' ),
				'parent_item_colon' => __( 'Parent Status:', 'marketplace-for-woocommerce' ),
				'edit_item'         => __( 'Edit Status', 'marketplace-for-woocommerce' ),
				'update_item'       => __( 'Update Status', 'marketplace-for-woocommerce' ),
				'add_new_item'      => __( 'Add New Status', 'marketplace-for-woocommerce' ),
				'new_item_name'     => __( 'New Status Name', 'marketplace-for-woocommerce' ),
				'menu_name'         => __( 'Status', 'marketplace-for-woocommerce' ),
			);

			$args = array(
				'hierarchical'       => true,
				'labels'             => $labels,
				'show_in_menu'       => true,
				//'show_in_menu'       => 'edit.php?trswc=trswc',
				'show_ui'            => true,
				'show_admin_column'  => true,
				'show_in_quick_edit' => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'status' ),
			);

			register_taxonomy( $this->tax_id, $referral_cpt_id, $args );
		}
	}
}