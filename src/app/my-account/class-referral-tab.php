<?php
/**
 * Easy Referral for WooCommerce - Referral Tab
 *
 * @version 1.0.5
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
			add_action( 'woocommerce_after_account_navigation', array( $this, 'load_dashicons' ) );
			add_action( 'woocommerce_after_account_navigation', array( $this, 'add_custom_js' ) );
			add_action( 'erwc_referral_tab_content', array( $this, 'add_referral_sections' ), 10 );
			add_action( 'erwc_referral_tab_content', array( $this, 'add_sections_content' ), 15 );
		}

		/**
		 * add_sections_content.
		 *
		 * @version 1.0.5
		 * @since   1.0.5
		 */
		function load_dashicons(){
			wp_enqueue_style( 'dashicons' );
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
		 * add_custom_js.
		 *
		 * @version 1.0.5
		 * @since   1.0.5
		 */
		function add_custom_js() {
			?>
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					var clipboard = {
						copy: function (text) {
							if (window.clipboardData && window.clipboardData.setData) {
								// IE specific code path to prevent textarea being shown while dialog is visible.
								return clipboardData.setData("Text", text);
							} else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
								var textarea = document.createElement("textarea");
								textarea.textContent = text;
								textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
								document.body.appendChild(textarea);
								textarea.select();
								try {
									return document.execCommand("copy");  // Security exception may be thrown by some browsers.
								} catch (ex) {
									console.warn("Copy to clipboard failed.", ex);
									return false;
								} finally {
									document.body.removeChild(textarea);
								}
							}
						}
					};
					[].forEach.call(document.querySelectorAll('.erwc-copy-link-btn'), function (el) {
						el.addEventListener('click', function (e) {
							var link = e.currentTarget.getAttribute('data-url');
							var target = e.currentTarget;
							target.classList.add('active');
							clipboard.copy(link);
							setTimeout(function () {
								target.classList.remove('active');
							}, 2000);
						})
					})
				});
			</script>
			<?php
		}

		/**
		 * add_custom_css.
		 *
		 * @version 1.0.5
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
				.erwc-referral-codes-table td{
					vertical-align: middle;
				}
				.erwc-referral-codes-table td *{
					vertical-align: middle;
				}
				.erwc-action-btn{
					font-size:10px;
					vertical-align: middle;
					border-radius: 3px;
					padding:0px;
					border:none;
					/*border:1px solid #ccc;*/
					background:#fff;
					width:22px;
					height:22px;
					position:relative;
				}
				.erwc-action-btn:focus{
					outline:none;
				}
				.erwc-action-btn:hover{
					border:none;
					background:none;
					/*border:1px solid #999;*/
				}
				.erwc-action-btn:hover i:before{
					color:#666
				}
				.erwc-action-btn i{
					position:absolute;
					vertical-align: middle;
					line-height:17px;
					left: 0;
					top:0;
					transition:opacity 0.2s ease-in-out;
				}
				.erwc-action-btn i.clipboard{
					font-size:18px;
					top:1px;
					left:0px;
					opacity: 1;
				}
				.erwc-action-btn i.yes{
					opacity: 0;
					font-size: 30px;
					padding: 0;
					top: -1px;
					left: -6px;
				}
				.erwc-action-btn.active i.yes{
					opacity:1;
				}
				.erwc-action-btn.active i.clipboard{
					opacity:0;
				}
				.erwc-action-btn i:before{
					vertical-align: middle;
					color:#000;
				}
			</style>
			<?php
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