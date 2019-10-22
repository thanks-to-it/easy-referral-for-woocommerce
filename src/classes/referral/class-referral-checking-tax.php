<?php
/**
 * Easy Referral for WooCommerce - Referral Checking
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

if ( ! class_exists( 'ThanksToIT\ERWC\Referral\Referral_Checking_Tax' ) ) {

	class Referral_Checking_Tax {
		public $tax_id = 'erwc-authentication-result';

		function init() {
			register_activation_hook( ERWC()->plugin_info['filesystem_path'], array( $this, 'create_terms_on_plugin_init' ) );
			add_action( 'erwc_referral_cpt_created', array( $this, 'register_taxonomy' ) );
		}

		/**
		 * get_registered_terms.
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
				'check_option' => 'erwc_referral_checking_terms'
			) );
		}

		/**
		 * get_probably_reliable_status_id.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_probably_email_checking_id() {
			$automatically_created_status = get_option( 'erwc_referral_checking_terms', array() );
			if ( count( $automatically_created_status ) > 0 ) {
				return $automatically_created_status[0];
			} else {
				return '';
			}
		}

		/**
		 * get_default_terms.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		public function get_default_terms() {
			return array(
				array(
					'slug'        => 'identical-emails',
					'label'       => __( 'Identical Emails', 'easy-referral-for-woocommerce' ),
					'description' => __( 'When Referrer and Referee emails are identical', 'easy-referral-for-woocommerce' ),
				),
			);
		}

		/**
		 * Registers taxonomy.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param null $cpt_id
		 */
		public function register_taxonomy( $cpt_id = null ) {
			// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name'              => __( 'Checking Result', 'marketplace-for-woocommerce' ),
				'singular_name'     => __( 'Checking', 'marketplace-for-woocommerce' ),
				'search_items'      => __( 'Search Checking', 'marketplace-for-woocommerce' ),
				'all_items'         => __( 'All Checking', 'marketplace-for-woocommerce' ),
				'parent_item'       => __( 'Parent Checking', 'marketplace-for-woocommerce' ),
				'parent_item_colon' => __( 'Parent Checking:', 'marketplace-for-woocommerce' ),
				'edit_item'         => __( 'Edit Checking', 'marketplace-for-woocommerce' ),
				'update_item'       => __( 'Update Checking', 'marketplace-for-woocommerce' ),
				'add_new_item'      => __( 'Add New Checking', 'marketplace-for-woocommerce' ),
				'new_item_name'     => __( 'New Checking Name', 'marketplace-for-woocommerce' ),
				'menu_name'         => __( 'Checking', 'marketplace-for-woocommerce' ),
			);

			$args = array(
				'hierarchical'       => true,
				'capabilities'       => array(
					'manage_terms' => 'manage_categories',
					'edit_terms'   => 'manage_categories',
					//'delete_terms' => '',
					'assign_terms' => 'edit_posts'
				),
				'public'             => false,
				'labels'             => $labels,
				'show_in_menu'       => true,
				//'show_in_menu'       => 'edit.php?trswc=trswc',
				'show_ui'            => true,
				'show_admin_column'  => true,
				'show_in_quick_edit' => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'checking' ),
			);

			register_taxonomy( $this->tax_id, $cpt_id, $args );


			/*register_taxonomy('test', $referral_cpt_id, array(
				'capabilities' => array(
					'manage_terms' => 'manage_categories',
					'edit_terms' => 'manage_categories',
					'delete_terms' => '',
					'assign_terms' => 'edit_posts'
				),
				'label' => 'Test',
				'labels' => array(
					'name' => 'test',
					'add_new_item' => 'Add New test',
					'new_item_name' => "Add New test"
				),
				'public' => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud' => false,
				'show_ui' => true,
				'hierarchical' => true
			));*/
		}

	}
}