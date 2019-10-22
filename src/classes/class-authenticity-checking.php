<?php
/**
 * Easy Referral for WooCommerce - Authenticity Checking
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Authenticity_Checking' ) ) {

	class Authenticity_Checking {
		public $checking_methods = array();

		function init() {
			// Authenticity Checking Methods
			add_filter( 'erwc_authenticity_checking_methods', array( $this, 'add_checking_methods' ) );
			add_filter( 'erwc_authenticity_checking_' . 'email-comparing', array( $this, 'check_email_comparing' ), 10, 3 );

			// Check Authenticity
			add_action( 'erwc_creating_or_updating_referral', array( $this, 'check_authenticity' ), 10, 3 );
		}

		/**
		 * add_checking_methods.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $checking_methods
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		function add_checking_methods( $checking_methods ) {
			if ( 'yes' === get_option( 'erwc_opt_checking_email', 'yes' ) ) {
				$checking_methods[] = array(
					'id'                 => 'email-comparing',
					'title'              => __( 'Email Comparing', 'easy-referral-for-woocommerce' ),
					'checking_status_id' => get_option( 'erwc_opt_checking_email_status', ERWC()->factory->get_referral_checking_tax()->get_probably_email_checking_id() )
				);
			}
			return $checking_methods;
		}

		/**
		 * check_email_comparing.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param array $checking_response
		 * @param $referrer_code
		 * @param $order_id
		 *
		 * @return array
		 * @throws \ReflectionException
		 */
		function check_email_comparing( $checking_response, $referrer_code, $order_id ) {
			$referrer_user          = get_user_by( 'ID', get_post_meta( $order_id, '_erwc_referrer_id', true ) );
			$referrer_email         = $referrer_user->user_email;
			$order                  = wc_get_order( $order_id );
			$customer_billing_email = $order->get_billing_email();
			if (
				$customer_billing_email == $referrer_email ||
				$customer_billing_email == get_user_meta( '_erwc_referrer_id', 'billing_email', true )
			) {
				$checking_response['fraud_detected']  = true;
				$checking_response['checking_report'] = sprintf( __( 'Referrer and Referee emails are identical (%s)', 'easy-referral-for-woocommerce' ), $referrer_email );
			}
			return $checking_response;
		}

		/**
		 * check_authenticity.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $referral_id
		 * @param $referrer_code
		 * @param $order
		 *
		 * @throws \ReflectionException
		 */
		function check_authenticity( $referral_id, $referrer_code, $order ) {
			$this->checking_methods = apply_filters( 'erwc_authenticity_checking_methods', $this->checking_methods );
			$authenticity_status_id = get_option( 'erwc_opt_auth_reliable_status', ERWC()->factory->get_referral_authenticity_tax()->get_probably_reliable_status_id() );

			$referral_checking_statuses = array();
			foreach ( $this->checking_methods as $method ) {
				$checking_response = apply_filters( "erwc_authenticity_checking_{$method['id']}", array( 'fraud_detected' => false, 'checking_report' => '' ), $referrer_code, $order );
				if ( true === $checking_response['fraud_detected'] ) {

					// Referral checking status
					$referral_checking_statuses[] = (int) $this->get_checking_method_by_id( $method['id'] )['checking_status_id'];

					//Set referral authenticity status
					$authenticity_status_id = get_option( 'erwc_opt_auth_not_reliable_status', ERWC()->factory->get_referral_authenticity_tax()->get_probably_not_reliable_status_id() );

					// Set referral checking report
					if ( ! empty( $checking_response['checking_report'] ) ) {
						$reports                  = get_post_meta( $referral_id, '_erwc_checking_reports', true );
						$reports                  = empty( $reports ) ? array() : $reports;
						$reports[ $method['id'] ] = $checking_response['checking_report'];
						update_post_meta( $referral_id, '_erwc_checking_reports', $reports );
					}
				}
			}

			//Set referral checking status
			wp_set_object_terms( $referral_id, $referral_checking_statuses, ERWC()->factory->get_referral_checking_tax()->tax_id );

			//Set referral authenticity status
			wp_set_object_terms( $referral_id, (int) $authenticity_status_id, ERWC()->factory->get_referral_authenticity_tax()->tax_id );
		}

		/**
		 * get_checking_method_by_id.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $id
		 *
		 * @return mixed
		 */
		function get_checking_method_by_id( $id ) {
			$term = wp_list_filter( $this->checking_methods, array( 'id' => $id ) );
			$pos  = reset( $term );
			return $pos;
		}
	}
}