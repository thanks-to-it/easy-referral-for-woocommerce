<?php
/**
 * Easy Referral for WooCommerce - Referral_Email
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Referral;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Referral\Referral_Email' ) ) {

	class Referral_Email {

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			add_action( 'erwc_creating_or_updating_referral', array( $this, 'send_email_after_creating_or_updating_referral' ), 10, 4 );
		}

		/**
		 * send_email_woocommerce_style.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $email
		 * @param $subject
		 * @param $heading
		 * @param $message
		 */
		function send_email_woocommerce_style( $email, $subject, $heading, $message ) {
			// Get woocommerce mailer from instance
			$mailer = WC()->mailer();
			// Wrap message using woocommerce html email template
			$wrapped_message = $mailer->wrap_message( $heading, $message );
			// Create new WC_Email instance
			$wc_email = new \WC_Email;
			// Style the wrapped message with woocommerce inline styles
			$html_message = $wc_email->style_inline( $wrapped_message );
			// Send the email using wordpress mail function
			wp_mail( $email, $subject, $html_message, 'Content-Type: text/html; charset=UTF-8' );
		}

		/**
		 * send_email_after_creating_or_updating_referral.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $referral_id
		 * @param $referrer_code
		 * @param $order_id
		 * @param $creating
		 *
		 * @throws \ReflectionException
		 */
		function send_email_after_creating_or_updating_referral( $referral_id, $referrer_code, $order_id, $creating ) {
			if ( ! $creating ) {
				return;
			}
			$code_meta     = ERWC()->factory->get_referral_code_manager()->get_referral_code_meta( $referral_id );
			$referral_post = get_post( $referral_id );
			$referrer      = get_user_by( 'ID', $code_meta->referrer_id );
			$message       = '<p>' . sprintf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $referrer->display_name ) ) . '</p>';
			$message       .= '<p>' . esc_html__( 'You have a new Referral.', 'easy-referral-for-woocommerce' ) . '</p>';
			$message       .= '<h2>' . wp_kses_post( sprintf( __( '[Referral #%s]', 'easy-referral-for-woocommerce' ) . ' (<time datetime="%s">%s</time>)', $referral_id, get_the_date( get_option( 'c' ), $referral_post ), get_the_date( get_option( 'date_format' ), $referral_post ) ) ) . '</h2>';
			$message       .= do_shortcode( '[erwc_referrals_table period_filter="false" cols="code,reward,status" referrer_id="'.$code_meta->referrer_id.'" table_style="email" referral_id="' . $referral_id . '"]' );
			$message       .= '<div style="margin-bottom: 40px;"></div>';
			$message       .= '<p>' . sprintf( __( 'Please access your <a href="%s">Referrals page</a> to see more details.', 'easy-referral-for-woocommerce' ), ERWC()->factory->get_referral_tab()->get_endpoint_url() ) . '</p>';
			$subject       = __( 'You Have a new Referral', 'easy-referral-for-woocommerce' ) . ' (' . get_the_date( get_option( 'date_format' ), $referral_post ) . ')';
			$this->send_email_woocommerce_style( $referrer->user_email, $subject, 'You Have a new Referral', $message );
		}
	}
}