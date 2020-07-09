<?php
/**
 * Easy Referral for WooCommerce - Functions
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Functions' ) ) {

	class Functions {
		static function format_template_variables( $vars, $args = null ) {
			$args = wp_parse_args( $args, array(
				'text_before' => __( 'Template variables:', 'easy-referral-for-woocommerce' )
			) );
			$vars_modified = array_map( function ( $item ) {
				return '<code>{{' . $item . '}}</code>';
			}, $vars );
			return '<p class="description">'.$args['text_before'] . ' ' . implode( ", ", $vars_modified ).'</p>';
		}
	}
}