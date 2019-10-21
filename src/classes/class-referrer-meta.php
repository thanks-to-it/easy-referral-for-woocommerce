<?php
/**
 * Easy Referral for WooCommerce - Referrer Meta
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Referrer_Meta' ) ) {

	class Referrer_Meta {

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			add_action( 'init', array( $this, 'save_user_meta' ) );
			add_action( 'show_user_profile', array( $this, 'display_user_admin_fields' ), 30 ); // admin: edit profile
			add_action( 'edit_user_profile', array( $this, 'display_user_admin_fields' ), 30 ); // admin: edit other users
		}

		/**
		 * display_user_admin_fields.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function display_user_admin_fields() {
			$fields  = $this->get_fields();
			$user_id = get_current_user_id();
			?>
			<h2><?php _e( 'Referrer Details', 'easy-referral-for-woocommerce' ); ?></h2>
			<table class="form-table" id="iconic-additional-information">
				<tbody>
				<?php foreach ( $fields as $key => $field_args ) { ?>
					<tr>
						<th>
							<label for="<?php echo $key; ?>"><?php echo $field_args['label']; ?></label>
						</th>
						<td>
							<?php $field_args['label'] = false; ?>
							<?php
							$value = get_user_meta( $user_id, $key, true );
							$value = isset( $field_args['value'] ) ? $field_args['value'] : $value;
							?>
							<?php woocommerce_form_field( $key, $field_args, $value ); ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<?php
		}

		/**
		 * save_user_meta.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function save_user_meta() {
			$customer_id = get_current_user_id();
			$fields      = $this->get_fields();
			foreach ( $fields as $key => $field_args ) {
				$default_sanitize_function = 'textarea' !== $field_args['type'] ? 'wc_clean' : 'wc_sanitize_textarea';
				$sanitize                  = isset( $field_args['sanitize'] ) ? $field_args['sanitize'] : $default_sanitize_function;
				if ( isset( $_POST[ $key ] ) ) {
					$value = isset( $_POST[ $key ] ) ? call_user_func( $sanitize, $_POST[ $key ] ) : '';
					update_user_meta( $customer_id, $key, $value );
				}
			}
		}

		/**
		 * get_referrer_details_frontend_form.
		 *
		 * @see https://iconicwp.com/blog/the-ultimate-guide-to-adding-custom-woocommerce-user-account-fields/
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_referrer_details_frontend_form() {
			ob_start();
			$user_id = get_current_user_id();
			?>
			<form class="woocommerce-referrer-details" action="" method="post">

				<?php
				$fields = $this->get_fields();
				foreach ( $fields as $key => $field_args ) {
					$value = get_user_meta( $user_id, $key, true );
					$value = isset( $field_args['value'] ) ? $field_args['value'] : $value;
					woocommerce_form_field( $key, $field_args, $value );
				}
				?>

				<p>
					<button type="submit" class="woocommerce-Button button" name=""
					        value="<?php _e( 'Save', 'easy-referral-for-woocommerce' ); ?>"><?php _e( 'Save', 'easy-referral-for-woocommerce' ); ?>
					</button>
				</p>
			</form>
			<?php
			return ob_get_clean();
		}

		/**
		 * get_fields.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		function get_fields() {
			return apply_filters( 'erwc_referrer_fields', array(
				'erwc_payment_details' => array(
					'type'        => 'textarea',
					//'description' => __( 'Add any type of payment details', 'easy-referral-for-woocommerce' ),
					'label'       => __( 'Payment Details', 'easy-referral-for-woocommerce' ),
					'placeholder' => __( 'Add here any type of detail you would like to use to receive your payment, e.g. Paypal Email, IBAN, Bank Name...', 'easy-referral-for-woocommerce' ),
				),
				'erwc_personal_id'     => array(
					'type'        => 'text',
					'placeholder' => __( 'Personal ID (or passport number)', 'easy-referral-for-woocommerce' ),
					'label'       => __( 'Personal ID', 'easy-referral-for-woocommerce' ),
				),
				'erwc_company_name'    => array(
					'type'  => 'text',
					'label' => __( 'Company Name', 'easy-referral-for-woocommerce' ),
				),
				'erwc_vat_id'          => array(
					'type'        => 'text',
					'placeholder' => __( 'Company business registration number (or VAT ID)', 'easy-referral-for-woocommerce' ),
					'label'       => __( 'Company business registration number', 'easy-referral-for-woocommerce' ),
				),
			) );
		}

		/*function woocommerce_edit_account_form() {
			$fields = $this->get_account_fields();
			foreach ( $fields as $key => $field_args ) {
				woocommerce_form_field( $key, $field_args );
			}
		}*/
	}
}