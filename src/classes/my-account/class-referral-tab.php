<?php
/**
 * Easy Referral for WooCommerce - Referral Tab
 *
 * @version 1.0.2
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\My_Account;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\My_Account\Referral_Tab' ) ) {

	class Referral_Tab {

		public $id = 'erwc-referral';

		/**
		 * add_sections_content.
		 *
		 * @version 1.0.2
		 * @since   1.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'add_endpoint' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 1 );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_link_my_account' ) );
			add_action( "woocommerce_account_{$this->id}_endpoint", array( $this, 'add_content' ) );
			add_filter( 'the_title', array( $this, 'handle_endpoint_title' ) );
			add_action( 'woocommerce_after_account_navigation', array( $this, 'add_custom_css' ) );
			add_action( 'erwc_referral_tab_content', array( $this, 'add_sections' ), 10 );
			add_action( 'erwc_referral_tab_content', array( $this, 'add_sections_content' ), 15 );
		}

		/**
		 * add_sections_content.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_sections_content() {
			echo do_shortcode( '[erwc_referral_sections_content]' );
		}

		/**
		 * add_referral_sections.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_referral_sections() {
			echo do_shortcode( '[erwc_referral_sections]' );
		}

		/**
		 * get_vertical_separators_style
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_vertical_separators_style() {
			ob_start();
			?>
			<style>
				.erwc-vseparator-wrapper {
					font-size: 0;
					margin: 0 0 20px 0;
				}

				.erwc-vseparator {
					display: inline-block;
					border-left: 1px solid #ccc;
					padding: 0px 7px 0 7px;
				}

				.erwc-vseparator a {
					font-size: 15px;
				}

				.erwc-vseparator:first-child {
					border: none;
					padding-left: 0;
				}

				.erwc-vseparator.active a {
					cursor: default !important;
					pointer-events: none !important;
					text-decoration: none !important;
				}
			</style>
			<?php
			return ob_get_clean();
		}

		/**
		 * add_custom_css.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_custom_css() {
			$my_theme = wp_get_theme( 'storefront' );
			if ( $my_theme->exists() ) { ?>
				<style>
					.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--<?php echo $this->id?> a::before {
						content: "\f074";
					}
				</style>
				<?php
			}
			?>
			<style>
				.form-row .optional {
					display: none;
				}
			</style>
			<?php
			echo $this->get_vertical_separators_style();
		}

		/**
		 * handle_endpoint_title.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $title
		 *
		 * @return string|void
		 */
		function handle_endpoint_title( $title ) {
			global $wp_query;
			$is_endpoint = isset( $wp_query->query_vars[ $this->id ] );
			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				$title = __( 'Referral', 'easy-referral-for-woocommerce' );
			}
			return $title;
		}

		/**
		 * get_endpoint_url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		function get_endpoint_url() {
			return wc_get_account_endpoint_url( $this->id );
		}

		/**
		 * add_endpoint.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_endpoint() {
			add_rewrite_endpoint( $this->id, EP_ROOT | EP_PAGES );
		}

		/**
		 * add_query_vars.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $vars
		 *
		 * @return array
		 */
		function add_query_vars( $vars ) {
			$vars[] = $this->id;
			return $vars;
		}

		/**
		 * add_link_my_account.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $items
		 *
		 * @return mixed
		 */
		function add_link_my_account( $items ) {
			$items[ $this->id ] = __( 'Referral', 'easy-referral-for-woocommerce' );
			return $items;
		}

		/**
		 * add_content.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_content() {
			do_action('erwc_referral_tab_content');

			//echo do_shortcode( '[erwc_referrals_period_filter]' );
           // echo do_shortcode( '[erwc_referrals_sum_table]' );
			//echo do_shortcode( '[erwc_referrals_table]' );
		}

	}
}