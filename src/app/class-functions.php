<?php
/**
 * Easy Referral for WooCommerce - Functions
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;

use ThanksToIT\ERWC\Admin\Admin_Settings_Authenticity;
use ThanksToIT\ERWC\Admin\Admin_Settings_Codes;
use ThanksToIT\ERWC\Admin\Admin_Settings_General;
use ThanksToIT\ERWC\Admin\Admin_Settings_Interface;
use ThanksToIT\ERWC\Admin\Referral_Code_Admin_Settings;
use ThanksToIT\ERWC\Referral\Referral_CPT;
use ThanksToIT\ERWC\My_Account\Referral_Codes_Tab;
use ThanksToIT\ERWC\My_Account\Referral_Tab;
use ThanksToIT\ERWC\Referral\Referral_Authenticity_Tax;
use ThanksToIT\ERWC\Referral\Referral_Checking_Tax;
use ThanksToIT\ERWC\Referral\Referral_Email;
use ThanksToIT\ERWC\Referral\Referral_Meta;
use ThanksToIT\ERWC\Referral\Referral_Status_Tax;

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
			return $args['text_before'] . ' ' . implode( ", ", $vars_modified );
		}
	}
}