<?php
/**
 * Easy Referral for WooCommerce - Referral Code Manager
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Referral;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Referral\Referral_Code_Manager' ) ) {

	class Referral_Code_Manager {

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			// Detect referrer code on query string
			add_action( 'wp_loaded', array( $this, 'detect_referrer_code_on_query_string' ) );

			// Force wc session
			add_action( 'woocommerce_init', array( $this, 'force_non_logged_user_wc_session' ) );

			// Apply Referral Code on Order
			add_action( 'woocommerce_checkout_create_order', array( $this, 'apply_referral_code_on_order' ), 10, 2 );
			add_action( 'erwc_before_apply_referral_code', array( $this, 'remove_referrer_code_from_wc_session' ) );

			// Display Referral Code on Admin Order
			add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'show_referral_code_data_on_admin_order' ), 10, 2 );

			// Validate Referrer code by Referrer Usage Limit
			add_filter( 'erwc_apply_referral_code_validation', array( $this, 'validate_referrer_code_by_referrer_usage_limit' ), 10, 3 );

			// Check if Referrer code is from current user
			add_filter( 'erwc_apply_referral_code_validation', array( $this, 'disallow_referral_from_own_user' ), 10, 3 );

			// Apply coupon code from referrer code
			//add_action( 'woocommerce_before_cart_table', array( $this, 'apply_discount_programmatically' ) );
			//add_action( 'woocommerce_before_checkout_form', array( $this, 'apply_discount_programmatically' ) );

			/*add_action('init',function(){
				$order = wc_get_order(296);
				$code = '2yyuv2';
				$validation =false;
				$this->validate_referrer_code_by_referrer_usage_limit($validation,$code,$order);
			});*/
		}

		/**
		 * disallow_referral_from_own_user
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $valid
		 * @param $referrer_code
		 * @param $order
		 *
		 * @return bool
		 */
		function disallow_referral_from_own_user( $valid, $referrer_code, $order ) {
			$decoded = $this->decode_referrer_code( $referrer_code );
			if (
				! is_user_logged_in() ||
				$decoded['referrer_id'] != get_current_user_id()
			) {
				return $valid;
			}
			$valid = false;
			return $valid;
		}

		/**
		 * force_non_logged_user_wc_session.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function force_non_logged_user_wc_session() {
			if ( is_user_logged_in() || is_admin() ) {
				return;
			}
			if ( isset( WC()->session ) ) {
				if ( ! WC()->session->has_session() ) {
					WC()->session->set_customer_session_cookie( true );
				}
			}
		}

		/**
		 * Validate Referrer code by Referrer Usage Limit.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $valid
		 * @param $referrer_code
		 * @param $order
		 *
		 * @return boolean
		 */
		function validate_referrer_code_by_referrer_usage_limit( $valid, $referrer_code, $order ) {
			global $wpdb;
			$billing_email        = $order->get_billing_email();
			$user_id              = $order->get_customer_id();
			$decoded              = $this->decode_referrer_code( $referrer_code );
			$code                 = $this->get_referral_code( $decoded['referral_code_id'] );
			$usage_limit_per_user = $code->referee_usage_limit;
			if ( empty( $usage_limit_per_user ) ) {
				return $valid;
			}
			$sql          =
				"
				SELECT pm.meta_value AS referrer_code, count(pm2.meta_value) AS email_sum, count(pm3.meta_value) AS user_sum
				FROM {$wpdb->postmeta} AS pm
				LEFT JOIN {$wpdb->postmeta} AS pm2 ON (pm.post_id=pm2.post_id AND pm2.meta_key='_billing_email' AND pm2.meta_value='%s')
				LEFT JOIN {$wpdb->postmeta} AS pm3 ON (pm.post_id=pm3.post_id AND pm3.meta_key='_customer_user' AND pm3.meta_value='%d' AND pm3.meta_value != '0')
				WHERE pm.meta_key = '_erwc_referrer_code' AND pm.meta_value = '%s'
			";
			$prepared_sql = $wpdb->prepare( $sql,
				$billing_email, $user_id, $referrer_code
			);
			$result       = $wpdb->get_row( $prepared_sql );
			if ( $result->email_sum >= $usage_limit_per_user || $result->user_sum >= $usage_limit_per_user ) {
				$valid = false;
			}
			return $valid;
		}

		/**
		 * remove_referrer_code_from_wc_session.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function remove_referrer_code_from_wc_session() {
			ERWC()->factory->get_session()->unset_session_var( 'erwc_referrer_code' );
		}

		/**
		 * show_admin_order_referral_code_data.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param \WC_Order $order
		 *
		 * @throws \ReflectionException
		 */
		function show_referral_code_data_on_admin_order( \WC_Order $order ) {
			$referrer_code = get_post_meta( $order->get_id(), '_erwc_referrer_code', true );
			if ( empty( $referrer_code ) ) {
				return;
			}
			$decoded     = $this->decode_referrer_code( $referrer_code );
			$code        = $this->get_referral_code( $decoded['referral_code_id'] );
			$referrer    = get_user_by( 'id', $decoded['referrer_id'] );
			$referral_id = ERWC()->factory->get_referral_cpt()->get_referral_by_order_id( $order->get_id() );
			?>
			<div class="order_data_column trswc-order-data-column">
				<h3><?php _e( 'Referral info' ); ?></h3>
				<p>
					<strong><?php echo __( 'Referrer', 'referral-system-for-woocommerce' ); ?>:</strong>
					<a href="<?php echo get_edit_user_link( $decoded['referrer_id'] ) ?>"><?php echo esc_html( $referrer->display_name ); ?></a>
				</p>
				<p>
					<strong><?php echo __( 'Referrer Code', 'referral-system-for-woocommerce' ); ?>:</strong>
					<?php echo esc_html( $referrer_code ); ?>
				</p>
				<p>
					<strong><?php echo __( 'Referrer email', 'referral-system-for-woocommerce' ); ?>:</strong>
					<?php echo esc_html( $referrer->user_email ); ?>
				</p>
				<!--<p>
					<strong><?php //echo __( 'Reward Type', 'referral-system-for-woocommerce' ); ?>:</strong>
					<?php //echo esc_html( $this->get_reward_types()[ $code->reward_type ] ); ?>
				</p>
				<p>
					<strong><?php //echo __( 'Reward Value', 'referral-system-for-woocommerce' ); ?>:</strong>
					<?php //echo esc_html( $code->reward_value ); ?>
				</p>
				<p>
					<strong><?php //echo __( 'Total Reward Value', 'referral-system-for-woocommerce' ); ?>:</strong>
					<?php //echo wc_price( $this->calculate_total_reward_value( $referrer_code, $order ) ); ?>
				</p>-->
				<?php if ( false !== $referral_id ) { ?>
					<p>
						<strong><?php echo __( 'Referral', 'referral-system-for-woocommerce' ); ?>:</strong>
						<?php echo '<a href="' . get_edit_post_link( $referral_id ) . '">' . get_post( $referral_id )->post_title . '</a>'; ?>
					</p>
				<?php } ?>
			</div>
			<style>
				.trswc-order-data-column strong {
					display: block;
				}
			</style>
			<?php
		}

		/**
		 * apply_discount_programmatically.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		/*function apply_discount_programmatically() {
			$referrer_code = ERWC()->factory->get_session()->get_session_var( 'erwc_referrer_code' );
			if ( empty( $referrer_code ) ) {
				return;
			}
			$decoded = $this->decode_referrer_code( $referrer_code );
			$code    = $this->get_referral_code( $decoded['referral_code_id'] );
			if ( ! $code->enabled || ! $code->apply_coupon || empty( $code->coupon_code ) ) {
				return;
			}
			$wc_coupon = new \WC_Coupon( $code->coupon_code );
			if (
				! $wc_coupon->get_id() ||
				! apply_filters( 'erwc_apply_discount_validation', true, $wc_coupon, $referrer_code )
			) {
				return;
			}
			if ( ! WC()->cart->has_discount( $code->coupon_code ) ) {
				WC()->cart->add_discount( $code->coupon_code );
			}
		}*/

		/**
		 * apply_referral_code_on_order.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param \WC_Order $order
		 * @param $data
		 *
		 * @throws \ReflectionException
		 */
		function apply_referral_code_on_order( \WC_Order $order, $data ) {
			$referrer_code = ERWC()->factory->get_session()->get_session_var( 'erwc_referrer_code' );
			if ( empty( $referrer_code ) ) {
				return;
			}
			$decoded = $this->decode_referrer_code( $referrer_code );
			$code    = $this->get_referral_code( $decoded['referral_code_id'] );
			if (
				! $code->enabled ||
				! apply_filters( 'erwc_apply_referral_code_validation', true, $referrer_code, $order )
			) {
				return;
			}

			do_action( 'erwc_before_apply_referral_code', $referrer_code, $order, $data );

			// Save referrer id
			$order->update_meta_data( '_erwc_referrer_id', $decoded['referrer_id'] );

			// Save referrer code
			$order->update_meta_data( '_erwc_referrer_code', $referrer_code );

			// Calculate reward value
			/*$total_reward_value = $this->calculate_total_reward_value( $referrer_code, $order, $data );
			$order->update_meta_data( '_erwc_total_reward_value', $total_reward_value );

			// save referral data on order
			foreach ( get_object_vars( $code ) as $key => $value ) {
				$order->update_meta_data( '_erwc_' . $key, $value );
			}

			// Save referrer id
			$order->update_meta_data( '_erwc_referrer_id', $decoded['referrer_id'] );

			// Save referrer code
			$order->update_meta_data( '_erwc_referrer_code', $referrer_code );*/

			do_action( 'erwc_after_apply_referral_code', $referrer_code, $order, $data );
		}

		/**
		 * calculate_total_reward_value.
		 *
		 * @param $referrer_code
		 * @param $order
		 *
		 * @param null $order_data
		 *
		 * @return float
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function calculate_total_reward_value( $referrer_code, $order = null, $order_data = null ) {
			$decoded = $this->decode_referrer_code( $referrer_code );
			$code    = $this->get_referral_code( $decoded['referral_code_id'] );
			switch ( $code->reward_type ) {
				case 'order_total_percentage':
					$total_reward_value = $order->get_total() * ( filter_var( $code->reward_value, FILTER_SANITIZE_NUMBER_FLOAT ) / 100 );
					break;
				default:
					$total_reward_value = $code->reward_value;
					break;
			}

			return apply_filters( 'erwc_total_reward_value', $total_reward_value, $referrer_code, $order, $order_data );
		}

		/**
		 * get_referral_code.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param int $code_id
		 *
		 * @return mixed
		 */
		function get_referral_code( $code_id ) {
			$codes = $this->get_referral_codes();
			return $codes[ $code_id ];
		}

		/**
		 * get_referral_code_meta.
		 *
		 * @param $post_id
		 * @param null $args
		 *
		 * @return array|mixed
		 */
		function get_referral_code_meta( $post_id, $args = null ) {
			$args          = wp_parse_args( $args, array(
				'remove_prefix'     => true,
				'convert_to_object' => true

			) );
			$all_post_meta = get_post_meta( $post_id );
			$post_meta     = array();

			foreach ( $all_post_meta as $meta_key => $meta_value ) {
				if ( preg_match( '/^\_erwc_/', $meta_key ) ) {
					$post_meta[ $meta_key ] = $meta_value[0];
				}
			}

			if ( $args['remove_prefix'] ) {
				$post_meta = array_map( function ( $key, $value ) {
					$new_key = preg_replace( '/^\_erwc_/', '', $key );
					return array( $new_key => $value );
				}, array_keys( $post_meta ), $post_meta );
				$post_meta = call_user_func_array( 'array_merge', $post_meta );
			}

			if ( $args['convert_to_object'] ) {
				$post_meta = (object) $post_meta;
			}

			return $post_meta;
		}

		/**
		 * get_referral_codes.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		function get_referral_codes() {
			$total                    = get_option( 'erwc_opt_codes_total', 1 );
			$enabled                  = get_option( 'erwc_opt_code_enabled', array() );
			$reward_type              = get_option( 'erwc_opt_code_reward_type', array() );
			$reward_value             = get_option( 'erwc_opt_code_reward_value', array() );
			$coupon_code              = get_option( 'erwc_opt_code_coupon', array() );
			$apply_coupon             = get_option( 'erwc_opt_code_coupon_apply', array() );
			$copy_coupon_restrictions = get_option( 'erwc_opt_code_coupon_copy_restrictions', array() );
			$referee_usage_limit      = get_option( 'erwc_opt_referee_usage_limit', array() );
			$codes                    = array();
			for ( $i = 1; $i <= $total; $i ++ ) {
				$code_inf                           = new \stdClass();
				$code_inf->referral_code_id         = $i;
				$code_inf->enabled                  = empty( $enabled[ $i ] ) ? true : filter_var( $enabled[ $i ], FILTER_VALIDATE_BOOLEAN );
				$code_inf->reward_type              = empty( $reward_type[ $i ] ) ? 'order_total_percentage' : $reward_type[ $i ];
				$code_inf->reward_value             = empty( $reward_value[ $i ] ) ? '' : $reward_value[ $i ];
				$code_inf->coupon_code              = empty( $coupon_code[ $i ] ) ? '' : $coupon_code[ $i ];
				$code_inf->apply_coupon             = empty( $apply_coupon[ $i ] ) ? true : filter_var( $apply_coupon[ $i ], FILTER_VALIDATE_BOOLEAN );
				$code_inf->copy_coupon_restrictions = empty( $copy_coupon_restrictions[ $i ] ) ? true : filter_var( $copy_coupon_restrictions[ $i ], FILTER_VALIDATE_BOOLEAN );
				$code_inf->referee_usage_limit      = ! isset( $referee_usage_limit[ $i ] ) ? 1 : $referee_usage_limit[ $i ];
				$codes[ $i ]                        = $code_inf;
			}
			return $codes;
		}

		/**
		 * decode.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $referrer_code
		 *
		 * @return array|bool
		 */
		function decode_referrer_code( $referrer_code ) {
			$hashids = new \Hashids\Hashids( get_option( 'erwc_opt_salt', '' ), 6, $this->get_encode_alphabet() );
			$numbers = $hashids->decode( $referrer_code );
			if ( is_array( $numbers ) && count( $numbers ) == 2 ) {
				return array(
					'referral_code_id' => $numbers[0],
					'referrer_id'      => $numbers[1],
				);
			} else {
				return false;
			}
		}

		/**
		 * encode.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $referral_code_id
		 * @param null $referrer_id
		 *
		 * @return string
		 */
		function encode( $referral_code_id, $referrer_id = null ) {
			$hashids = new \Hashids\Hashids( get_option( 'erwc_opt_salt', '' ), 6, $this->get_encode_alphabet() );
			if ( $referrer_id === null ) {
				$referrer_id = get_current_user_id();
			}
			return $hashids->encode( $referral_code_id, $referrer_id );
		}

		/**
		 * get_referrer_code url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $referrer_code
		 *
		 * @return string
		 */
		function get_referrer_code_url( $referrer_code ) {
			return add_query_arg( array(
				$this->get_query_string_trigger_param() => $referrer_code
			), trailingslashit( get_home_url() ) );
		}

		/**
		 * get_encode_alphabet.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_encode_alphabet() {
			return 'abcdefghijklmnopqrstuvwxyz1234567890';
		}

		/**
		 * get_query_string_trigger_param.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_query_string_trigger_param() {
			return 'erwc_code';
		}

		/**
		 * Detects referral code on Query String.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @throws \ReflectionException
		 */
		function detect_referrer_code_on_query_string() {
			if ( ! isset( $_GET[ $this->get_query_string_trigger_param() ] ) ) {
				return;
			}
			$referrer_code = sanitize_text_field( $_GET[ $this->get_query_string_trigger_param() ] );
			if ( empty( $referrer_code ) ) {
				return;
			}

			ERWC()->factory->get_session()->set_session_var( 'erwc_referrer_code', $referrer_code );
			do_action( 'erwc_referrer_code_detected', $referrer_code );
		}

		/**
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		function get_reward_types() {
			return array(
				'fixed_amount' => __( 'Fixed Amount', 'easy-referral-for-woocommerce' ),
				'order_total_percentage' => __( 'Order Total Percentage', 'easy-referral-for-woocommerce' ),
			);
		}
	}
}