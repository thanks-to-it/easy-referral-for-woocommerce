<?php
/**
 * Easy Referral for WooCommerce - Shortcodes
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC;

use ThanksToIT\ERWC\Admin\Referral_Code_Admin_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Shortcodes' ) ) {

	class Shortcodes {

		function init() {
			add_shortcode( 'erwc_referral_codes_table', array( $this, 'erwc_referral_codes_table' ) );
			add_shortcode( 'erwc_referrals_table', array( $this, 'erwc_referrals_table' ) );
			add_shortcode( 'erwc_referrals_sum_table', array( $this, 'erwc_referrals_sum_table' ) );			
			add_shortcode( 'erwc_referral_sections', array( $this, 'erwc_referral_sections' ) );
			add_shortcode( 'erwc_referral_sections_content', array( $this, 'erwc_referral_sections_content' ) );
			add_shortcode( 'erwc_referrer_details', array( $this, 'erwc_referrer_details' ) );
			add_filter( 'erwc_referrals_table_columns', array( $this, 'add_erwc_referrals_table_columns' ), 10, 2 );
			add_filter( 'erwc_referrals_table_column_value', array( $this, 'get_erwc_referrals_table_column_value' ), 10, 3 );
			add_filter( 'erwc_referral_sections_content', array( $this, 'get_referral_sections_content' ), 10, 2 );
		}

		/**
		 * erwc_referrer_details.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param null $args
		 *
		 * @throws \ReflectionException
		 */
		function erwc_referrer_details( $args = null ) {
			echo ERWC()->factory->get_referrer_meta()->get_referrer_details_frontend_form();
		}

		/**
		 * get_referral_sections_content.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $content
		 * @param $section
		 *
		 * @return string
		 */
		function get_referral_sections_content( $content, $section ) {
			switch ( $section ) {
				case 'referrals':
					$content = do_shortcode( '[erwc_referrals_sum_table]' );
					$content .= do_shortcode( '[erwc_referrals_table]' );
					break;
				case 'referral_codes':
					$content = do_shortcode( '[erwc_referral_codes_table]' );
					break;
				case 'referrer_details':
					$content = do_shortcode( '[erwc_referrer_details]' );
					break;
			}
			return $content;
		}

		/**
		 * get_referral_sections.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return array
		 */
		function get_referral_sections() {
			$sections = array(
				'referrals'        => __( 'Referrals', 'easy-referral-for-woocommerce' ),
				'referral_codes'   => __( 'Referral Codes', 'easy-referral-for-woocommerce' ),
				'referrer_details' => __( 'Referrer Details', 'easy-referral-for-woocommerce' ),
			);
			return $sections;
		}

		/**
		 * erwc_referral_sections_content.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param null $args
		 *
		 * @return mixed|void
		 */
		function erwc_referral_sections_content( $args = null ) {
			$sections = $this->get_referral_sections();
			reset( $sections );
			$first_key            = key( $sections );
			$query_string_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : $first_key;
			$content              = apply_filters( 'erwc_referral_sections_content', '', $query_string_section );
			return $content;
		}

		/**
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param null $args
		 *
		 * @return false|string
		 * @throws \ReflectionException
		 */
		function erwc_referral_sections( $args = null ) {
			$sections = $this->get_referral_sections();
			reset( $sections );
			$first_key            = key( $sections );
			$query_string_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : $first_key;
			ob_start();
			?>
			<div class="erwc-referrals-tab-sections erwc-vseparator-wrapper">
				<?php foreach ( $sections as $key => $section ) : ?>
					<?php $url = add_query_arg( array( 'section' => $key ), ERWC()->factory->get_referral_tab()->get_endpoint_url() ); ?>
					<?php $active_class = ! empty( $query_string_section ) && $key === $query_string_section ? 'active' : '' ?>
					<span class="erwc-vseparator erwc-referrals-tab-section <?php echo $active_class; ?>">
						<a href="<?php echo esc_html( $url ) ?>"><?php esc_html_e( $section ) ?></a>
					</span>
				<?php endforeach; ?>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * get_erwc_referrals_table_column_value.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $value
		 * @param $column
		 * @param $referral_id
		 *
		 * @return false|mixed|string
		 * @throws \ReflectionException
		 */
		function get_erwc_referrals_table_column_value( $value, $column, $referral_id ) {
			switch ( $column ) {
				case 'code':
					$value = get_post_meta( $referral_id, '_erwc_referrer_code', true );
					break;
				case 'reward':
					$value = wc_price( get_post_meta( $referral_id, '_erwc_total_reward_value', true ) );
					break;
				case 'order':
					$value = get_post_meta( $referral_id, '_erwc_order_id', true );
					break;
				case 'status':
					$status_terms = wp_get_post_terms( $referral_id, ERWC()->factory->get_referral_status_tax()->tax_id, array( 'fields' => 'names' ) );
					$value        = implode( ', ', $status_terms );
					break;
				case 'authenticity':
					$auth_terms = wp_get_post_terms( $referral_id, ERWC()->factory->get_referral_authenticity_tax()->tax_id, array( 'fields' => 'names' ) );
					$value      = implode( ', ', $auth_terms );
					break;
				case 'date':
					$value = get_the_date( 'Y/m/d', $referral_id );
					break;
			}
			return $value;
		}

		/**
		 * add_erwc_referrals_table_columns.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $columns
		 * @param $shortcode_atts
		 *
		 * @todo Remove some colums for email
		 *
		 * @return array
		 */
		function add_erwc_referrals_table_columns( $columns, $shortcode_atts ) {
			$new_cols = array(
				'code'         => __( 'Code', 'easy-referral-for-woocommerce' ),
				'reward'       => __( 'Reward', 'easy-referral-for-woocommerce' ),
				'order'        => __( 'Order', 'easy-referral-for-woocommerce' ),
				'status'       => __( 'Status', 'easy-referral-for-woocommerce' ),
				'authenticity' => __( 'Authenticity', 'easy-referral-for-woocommerce' ),
				'date'         => __( 'Date', 'easy-referral-for-woocommerce' ),
			);

			if ( isset( $shortcode_atts['cols'] ) && ! empty( $shortcode_atts['cols'] ) ) {
				$sc_cols        = array_map( 'trim', explode( ',', $shortcode_atts['cols'] ) );
				$invalid_values = array_diff( array_keys( $new_cols ), $sc_cols );
				$new_cols       = array_diff_key( $new_cols, array_flip( $invalid_values ) );
			}
			return array_merge( $new_cols, $columns );
		}

		/**
		 * erwc_referrals_sum_table.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $atts
		 *
		 * @return false|string|void
		 * @throws \ReflectionException
		 */
		function erwc_referrals_sum_table( $atts ) {
			if ( ! apply_filters( 'erwc_referrer_valid', true, get_current_user_id() ) ) {
				return;
			}
			$atts      = shortcode_atts(
				array(
					'display_empty_message' => true,
					'empty_message'         => ERWC()->factory->get_referral_cpt()->get_empty_referrals_message()
				), $atts, 'erwc_referrals_sum_table'
			);
			$the_query = ERWC()->factory->get_referral_cpt()->get_referrals_from_user_id( get_current_user_id() );
			ob_start();
			?>
			<?php if ( $the_query->have_posts() ) : ?>
				<table class="erwc-referrals-sum-table my_account_orders shop_table">
					<tr>
						<thead>
						<th>Status</span></th>
						<th>Sum</th>
						</thead>
					</tr>
					<tbody>
					<?php foreach ( ERWC()->factory->get_referral_status_tax()->get_registered_terms() as $status_term ) : ?>
						<tr>
							<td><?php echo $status_term->name; ?></td>
							<td>
								<?php echo ERWC()->factory->get_referral_cpt()->get_total_sum_by_referral_status( $status_term, array(
									'user_id'      => get_current_user_id(),
									'format_price' => true
								) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php else: ?>
				<?php if ( $atts['display_empty_message'] ): ?>
					<?php echo apply_filters( 'the_content', $atts['empty_message'] ); ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php
			return ob_get_clean();
		}

		/**
		 * erwc_referrals_table.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $atts
		 *
		 * @return false|string|void
		 * @throws \ReflectionException
		 */
		function erwc_referrals_table( $atts ) {
			if ( ! apply_filters( 'erwc_referrer_valid', true, get_current_user_id() ) ) {
				return;
			}

			// Shortcode atts
			$atts = shortcode_atts( array(
				'cols'          => '',
				'referrer_id'   => get_current_user_id(),
				'table_style'   => 'default', // default | email
				'referral_id'   => - 1,
			), $atts, 'erwc_referrals_table' );

			// Get specific Referral
			$query_args = array();
			if ( - 1 != $atts['referral_id'] ) {
				$query_args['p'] = $atts['referral_id'];
			}

			// Table style
			$table_params = 'email' === $atts['table_style'] ? 'cellspacing="0" cellpadding="6" style="width: 100%; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;" border="1"' : '';

			// Referrals Query
			$the_query = ERWC()->factory->get_referral_cpt()->get_referrals_from_user_id( $atts['referrer_id'], $query_args );
			ob_start();
			?>
			<?php if ( $the_query->have_posts() ) : ?>
				<table <?php echo $table_params ?> class="td erwc-referrals-table my_account_orders shop_table">
					<thead>
					<tr>
						<?php $columns = apply_filters( 'erwc_referrals_table_columns', array(), $atts ); ?>
						<?php foreach ( $columns as $column ): ?>
							<th class="td" scope="col"><?php echo esc_html( $column ) ?></span></th>
						<?php endforeach; ?>
					</tr>
					</thead>
					<tbody>
					<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<tr>
							<?php $columns = apply_filters( 'erwc_referrals_table_columns', array(), $atts ); ?>
							<?php foreach ( $columns as $column_key => $column ): ?>
								<td class="td"><?php echo apply_filters( 'erwc_referrals_table_column_value', '', $column_key, get_the_ID() ); ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endwhile; ?>
					</tbody>
					<?php wp_reset_postdata(); ?>
				</table>
			<?php else : ?>

			<?php endif; ?>
			<?php
			return ob_get_clean();
		}

		/**
		 * get_referral_codes_table.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return false|string
		 * @throws \ReflectionException
		 */
		function erwc_referral_codes_table( $atts ) {
			$atts = shortcode_atts( array(
				'referrer_id' => - 1,
			), $atts, 'erwc_referral_codes_table' );

			ob_start();
			$code_manager = ERWC()->factory->get_referral_code_manager();
			$codes        = $code_manager->get_referral_codes();
			?>
			<table class="erwc-referral-codes-table my_account_orders shop_table">
				<thead>
				<tr>
					<th width='10%'><?php _e( 'Code', 'easy-referral-for-woocommerce' ) ?></th>
					<th><?php _e( 'Referral Code URL', 'easy-referral-for-woocommerce' ) ?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $codes as $code_id => $code ): ?>
					<?php if ( ! $code->enabled ) { ?>
						<?php continue; ?>
					<?php } ?>
					<tr>
						<td>
							<?php echo esc_html( $referrer_code = $code_manager->encode( $code_id, get_current_user_id() ) ) ?>
						</td>
						<td>
							<?php echo esc_html( $code_manager->get_referrer_code_url( $referrer_code ) ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php
			return ob_get_clean();
		}
	}
}