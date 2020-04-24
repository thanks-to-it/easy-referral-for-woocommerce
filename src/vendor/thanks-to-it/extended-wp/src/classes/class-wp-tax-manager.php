<?php
/**
 * Easy Referral for WooCommerce - WP_Tax_Manager
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ExtendedWP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ExtendedWP\WP_Tax_Manager' ) ) {

	class WP_Tax_Manager {

		/**
		 * create_terms.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param array $args
		 */
		function create_terms( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'tax_id'      => '',
				'terms'       => array(),
				'check_option' => $args['tax_id'] . '_' . 'terms',
				'save_option' => '',
				'save_as' => 'array', // array | single
				'only_once'   => true
			) );
			$tax_id      = $args['tax_id'];
			$terms       = $args['terms'];
			$check_option = $args['check_option'];
			$save_option = !empty($args['save_option']) ? $args['save_option'] : $check_option;
			if ( $args['only_once'] && !empty( get_option( $check_option, array() ) ) ) {
				return;
			}
			register_taxonomy( $args['tax_id'], '', array() );
			$terms_ids = array();
			foreach ( $terms as $term ) {
				$response = $this->add_term( $term, $tax_id );
				if ( ! is_wp_error( $response ) ) {
					$terms_ids[] = $response['term_id'];
				}
			}

			if ( $args['only_once'] ) {			
				if('array'===$args['save_as']){
					update_option( $save_option, $terms_ids );
				}else{
					update_option( $save_option, $terms_ids[0] );
				}
			}
		}

		/**
		 * add_term.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $term
		 * @param $tax_id
		 *
		 * @return array|\WP_Error
		 */
		function add_term( $term, $tax_id ) {
			return wp_insert_term(
				$term['label'],
				$tax_id,
				array(
					'slug'        => $term['slug'],
					'description' => isset ( $term['description'] ) ? $term['description'] : ''
				)
			);
		}

	}
}