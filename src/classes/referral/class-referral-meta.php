<?php
/**
 * Easy Referral for WooCommerce - Referral Meta
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Referral;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Referral\Referral_Meta' ) ) {

	class Referral_Meta {
		public $cpt_id;

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			if ( is_admin() ) {
				require_once ERWC()->get_plugin_dir() . '/vendor/origgami/cmb2-grid/Cmb2GridPluginLoad.php';
			}
			add_action( 'cmb2_admin_init', array( $this, 'handle_info_fields' ) );
			add_action( 'cmb2_admin_init', array( $this, 'handle_authenticity_checking_fields' ) );
			add_action( 'erwc_referral_cpt_created', array( $this, 'on_referral_cpt_created' ) );
		}

		function handle_authenticity_checking_fields() {
			$cmb = new_cmb2_box( array(
				'id'           => 'erwc_referral_authenticity_metabox',
				'title'        => esc_html__( 'Authenticity Checking Result', 'easy-referral-for-woocommerce' ),
				'object_types' => array( $this->cpt_id ),
			) );

			$field7 = $cmb->add_field( array(
				//'name'          => esc_html__( 'Checking Result', 'easy-referral-for-woocommerce' ),
				'desc'          => esc_html__( 'Authenticity Checking Results', 'easy-referral-for-woocommerce' ),
				'type'          => 'text',
				'id'            => '_erwc_checking_reports',
				'render_row_cb' => array( $this, 'render_checking_results' ),
			) );

			$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb );
			$row      = $cmb2Grid->addRow();
			$row->addColumns(
				array( $field7 )
			);
		}

		/**
		 * on_referral_cpt_created.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $cpt_id
		 */
		function on_referral_cpt_created( $cpt_id ) {
			$this->cpt_id = $cpt_id;
		}

		/**
		 * handle_info_fields.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function handle_info_fields() {
			$current_post_id = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : null;
			$current_post_id = empty( $current_post_id ) && isset( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : $current_post_id;

			$cmb = new_cmb2_box( array(
				'id'           => 'erwc_referral_metabox',
				'cmb_styles'   => false,
				'title'        => esc_html__( 'Info', 'easy-referral-for-woocommerce' ),
				'object_types' => array( $this->cpt_id ),
			) );

			$field1 = $cmb->add_field( array(
				'name'       => esc_html__( 'Referrer', 'easy-referral-for-woocommerce' ),
				'id'         => '_erwc_referrer_id',
				'type'       => 'text',
				'display_cb' => array( $this, 'display_referrer' ),
				'column'     => array(
					'position' => 2,
				),
			) );

			$field2 = $cmb->add_field( array(
				'name'   => esc_html__( 'Referrer Code', 'easy-referral-for-woocommerce' ),
				'desc'   => esc_html__( '', 'easy-referral-for-woocommerce' ),
				'type'   => 'text',
				'id'     => '_erwc_referrer_code',
				'column' => array(
					'position' => 3,
				),
			) );

			$field3 = $cmb->add_field( array(
				'name'   => esc_html__( 'Order', 'easy-referral-for-woocommerce' ),
				'desc'   => esc_html__( 'Related Order ID that created this Referral', 'easy-referral-for-woocommerce' ),
				'type'   => 'text',
				'id'     => '_erwc_order_id',
				'column' => array(
					'position' => 4,
				),
			) );

			$field4 = $cmb->add_field( array(
				'name'    => esc_html__( 'Reward type', 'easy-referral-for-woocommerce' ),
				'id'      => '_erwc_reward_type',
				'type'    => 'select',
				'options' => ERWC()->factory->get_referral_code_manager()->get_reward_types()
			) );

			$field5 = $cmb->add_field( array(
				'name' => esc_html__( 'Reward Value', 'easy-referral-for-woocommerce' ),
				'desc' => esc_html__( 'Reward Value based on Reward Type', 'easy-referral-for-woocommerce' ),
				'id'   => '_erwc_reward_value',
				//'attributes'   => array('style'=>'width:95%'),
				'type' => 'text',
			) );

			$field6 = $cmb->add_field( array(
				'name'       => esc_html__( 'Total Reward Value', 'easy-referral-for-woocommerce' ),
				'desc'       => esc_html__( 'Total Reward Value considering the reward type and after the calculations have been made.', 'easy-referral-for-woocommerce' ),
				'type'       => 'text',
				//'before_field' => get_woocommerce_currency_symbol( get_post_meta( $current_post_id, '_erwc_currency', true ) ),
				'display_cb' => array( $this, 'display_money_field' ),
				'id'         => '_erwc_total_reward_value',
				//'attributes' => array( 'style' => 'width:95%' ),
				/*'display_cb' => function ( $field_args, $field ) {
					?>
					<p><?php echo $field->escaped_value(); ?></p>
					<?php
				},*/
				'column'     => array(
					'position' => 5,

				),
			) );

			$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb );
			$row      = $cmb2Grid->addRow();
			$row->addColumns(
				array( $field1, $field2, $field3 )
			);
			$row = $cmb2Grid->addRow();
			$row->addColumns(
				array( $field4, $field5, $field6 )
			);
		}

		/**
		 * display_money_field.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $field_args
		 * @param $field
		 *
		 * @return string
		 */
		function display_money_field( $field_args, $field ) {
			$currency = get_post_meta( $field->object_id, '_erwc_currency', true );
			return wc_price( $field->escaped_value(), array(
				'currency' => $currency
			) );
		}

		/**
		 * display_referrer.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $field_args
		 * @param $field
		 *
		 * @return string
		 */
		function display_referrer( $field_args, $field ) {
			$user = get_user_by( 'ID', $field->escaped_value() );

			$url = get_edit_user_link( $field->escaped_value() );
			//$email = $user->user_email;
			return '<a href="' . $url . '">' . $user->nickname . '</a>';
		}

		/**
		 * render_checking_results.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $field_args
		 * @param $field
		 *
		 * @return string|void
		 */
		function render_checking_results( $field_args, $field ) {
			$output           = '<dl style="margin:5px 0 0 0">';
			$checking_methods = apply_filters( 'erwc_authenticity_checking_methods', array() );
			if ( is_array( $field->value ) ) {
				foreach ( $field->value as $k => $v ) {
					$method_title = '';
					foreach ( $checking_methods as $method ) {
						if ( $method['id'] == $k ) {
							$method_title = $method['title'];
						}
					}
					$output .= '<dt><strong>' . $method_title . '</strong></dt>';
					if ( ! empty( $method_title ) ) {
						$output .= '<dd>' . $v . '</dd>';
					}
				}
			}
			$output .= '<dl>';

			// If field is requesting to not be shown on the front-end
			if ( ! is_admin() && ! $field->args( 'on_front' ) ) {
				return;
			}

			// If field is requesting to be conditionally shown
			if ( ! $field->should_show() ) {
				return;
			}

			$field->peform_param_callback( 'before_row' );

			// Remove the cmb-row class
			printf( '<div class="cmb-row custom-class %s">', $field->row_classes() );

			if ( ! $field->args( 'show_names' ) ) {

				// If the field is NOT going to show a label output this
				echo '<div class="cmb-td custom-label-class">';
				$field->peform_param_callback( 'label_cb' );

			} else {

				// Otherwise output something different
				if ( $field->get_param_callback_result( 'label_cb', false ) ) {
					echo '<div class="cmb-th custom-label-field-class">', $field->peform_param_callback( 'label_cb' ), '</div>';
				}
				echo '<div class="cmb-td custom-label-field">';
			}

			$field->peform_param_callback( 'before' );

			// The next two lines are key. This is what actually renders the input field
			$field_type = new \CMB2_Types( $field );
			echo $output;
			//echo '<p>as</p>';
			//$field_type->render();

			//$field->peform_param_callback( 'after' );

			echo '</div></div>';

			$field->peform_param_callback( 'after_row' );

			// For chaining
			return $output;
		}
	}
}







