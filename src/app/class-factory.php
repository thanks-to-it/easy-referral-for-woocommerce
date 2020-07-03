<?php
/**
 * Easy Referral for WooCommerce - Factory Class
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

if ( ! class_exists( 'ThanksToIT\ERWC\Factory' ) ) {

	class Factory extends \ThanksToIT\DPWP\Factory {

		/**
		 * get_referral_code.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Code
		 * @throws \ReflectionException
		 */
		function get_referral_code( $params ) {
			return $this->get_instance( 'Referral_Code', $params, true );
		}

		/**
		 * get_referral_code_admin_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Code_Admin_Settings
		 * @throws \ReflectionException
		 */
		function get_referral_code_admin_settings() {
			return $this->get_instance( 'Admin\Referral_Code_Admin_Settings' );
		}

		/**
		 * get_referrals_tab.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Tab
		 * @throws \ReflectionException
		 */
		function get_referral_tab() {
			return $this->get_instance( 'My_Account\Referral_Tab' );
		}

		/**
		 * get_session.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Session
		 * @throws \ReflectionException
		 */
		function get_session() {
			return $this->get_instance( 'Session' );
		}

		/**
		 * get_referrer.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referrer
		 * @throws \ReflectionException
		 */
		/*function get_referrer() {
			return $this->get_instance( 'Referrer' );
		}*/

		

		/**
		 * get_referrer_meta.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referrer_Meta
		 * @throws \ReflectionException
		 */
		function get_referrer_meta() {
			return $this->get_instance( 'Referrer_Meta' );
		}

		/**
		 * get_wc_order.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return WC_Order
		 * @throws \ReflectionException
		 */
		function get_wc_order() {
			return $this->get_instance( 'WC_Order' );
		}

		/**
		 * get_referral.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_CPT
		 * @throws \ReflectionException
		 */
		function get_referral_cpt() {
			return $this->get_instance( 'Referral\Referral_CPT' );
		}

		/**
		 * get_referral_meta.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Meta
		 * @throws \ReflectionException
		 */
		function get_referral_meta() {
			return $this->get_instance( 'Referral\Referral_Meta' );
		}

		/**
		 * get_referral_status.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Status_Tax
		 * @throws \ReflectionException
		 */
		function get_referral_status_tax() {
			return $this->get_instance( 'Referral\Referral_Status_Tax' );
		}

		/**
		 * get_referral_checking.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Checking_Tax
		 * @throws \ReflectionException
		 */
		function get_referral_checking_tax() {
			return $this->get_instance( 'Referral\Referral_Checking_Tax' );
		}

		/**
		 * get_referral_email.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Email
		 * @throws \ReflectionException
		 */
		function get_referral_email() {
			return $this->get_instance( 'Referral\Referral_Email' );
		}

		/**
		 * get_referral_authenticity.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Authenticity_Tax
		 * @throws \ReflectionException
		 */
		function get_referral_authenticity_tax() {
			return $this->get_instance( 'Referral\Referral_Authenticity_Tax' );
		}

		/**
		 * get_authenticity_checking.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Authenticity_Checking
		 * @throws \ReflectionException
		 */
		function get_authenticity_checking() {
			return $this->get_instance( 'Authenticity_Checking' );
		}

		/**
		 * get_admin_settings_general.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Admin_Settings_General
		 * @throws \ReflectionException
		 */
		function get_admin_settings_general() {
			return $this->get_instance( 'Admin\Admin_Settings_General' );
		}

		/**
		 * get_admin_settings_authenticity.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Admin_Settings_Authenticity
		 * @throws \ReflectionException
		 */
		function get_admin_settings_authenticity() {
			return $this->get_instance( 'Admin\Admin_Settings_Authenticity' );
		}

		/**
		 * get_admin_settings_codes.
		 *
		 * @version 1.0.6
		 * @since   1.0.0
		 *
		 * @return Admin_Settings_Codes
		 * @throws \ReflectionException
		 */
		function get_admin_settings_codes() {
			return $this->get_instance( 'Admin\Admin_Settings_Codes' );
		}

		/**
		 * get_admin_settings_interface.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Admin_Settings_Interface
		 * @throws \ReflectionException
		 */
		function get_admin_settings_interface() {
			return $this->get_instance( 'Admin\Admin_Settings_Interface' );
		}

		/**
		 * get_sharer.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @return Sharer
		 * @throws \ReflectionException
		 */
		function get_sharer() {
			return $this->get_instance( 'Sharer' );
		}

		/**
		 * get_scripts.
		 *
		 * @version 1.0.6
		 * @since   1.0.6
		 *
		 * @return Scripts
		 * @throws \ReflectionException
		 */
		function get_scripts() {
			return $this->get_instance( 'Scripts' );
		}

		/**
		 * get_referral_code_manager.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return Referral_Code_Manager
		 * @throws \ReflectionException
		 */
		function get_referral_code_manager() {
			return $this->get_instance( 'Referral_Code_Manager' );
		}

	}
}