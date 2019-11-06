<?php
/**
 * Easy Referral for WooCommerce - Notices
 *
 * @version 1.0.3
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Admin_Notices' ) ) {

	class Admin_Notices {

		/**
		 * init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function init() {
			add_action( 'admin_notices', array( $this, 'show_free_version_notice' ),999 );
		}

		/**
		 * can_display_free_version_notice.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @return bool
		 */
		function can_display_free_version_notice() {
			return
				true !== apply_filters( 'erwc_is_free_version', true, 'show_free_version_notice' ) ||
				! isset( $_REQUEST['tab'] ) ||
				! isset( $_REQUEST['page'] ) ||
				$_REQUEST['tab'] != 'erwc' ||
				$_REQUEST['page'] != 'wc-settings';
		}

		/**
		 * show_free_version_notice.
		 *
		 * @version 1.0.3
		 * @since   1.0.0
		 */
		function show_free_version_notice() {
			if ( $this->can_display_free_version_notice() ) {
				return;
			}
			$chance = mt_rand( 0, 1 );
			?>

			<?php if ( $chance == 0 ) : ?>
				<div class="notice notice-info">
					<h3 class="title" style="margin-bottom:0"><span style="vertical-align: middle"><img style="position:relative;top:-2px;margin:0 7px 0 0;width:20px;height:20px" src="https://ps.w.org/easy-referral-for-woocommerce/assets/icon-256x256.png?rev=2179699" /></span><?php _e( 'Easy Referral for WooCommerce - Feedback', 'easy-referral-for-woocommerce' ) ?></h3>
					<p style="margin-bottom:15px;">
						Enjoying it? Please consider <a
							href="https://wordpress.org/support/plugin/easy-referral-for-woocommerce/reviews/#new-post"
							target="_blank">writing a review</a>. It's really important :)
						<br>
						Feel free to submit your <a
							href="https://wordpress.org/support/plugin/easy-referral-for-woocommerce/" target="_blank">ideas / suggestions / bugs</a> too.
						<br />
						<a style="display:inline-block;margin:15px 0 5px 0" href="https://wordpress.org/support/plugin/easy-referral-for-woocommerce/reviews/#new-post" target="_blank" class="button button-primary">Add a Review <i style="position:relative;top:2px;" class="dashicons-before dashicons-star-filled"></i></a>
					</p>
				</div>
			<?php else: ?>
				<div class="notice notice-info">
					<h3 class="title"><span style="vertical-align: middle"><img style="position:relative;top:-2px;margin:0 7px 0 0;width:20px;height:20px" src="https://ps.w.org/easy-referral-for-woocommerce/assets/icon-256x256.png?rev=2179699" /></span><?php _e( 'Easy Referral for WooCommerce - Pro', 'easy-referral-for-woocommerce' ) ?></h3>
					<p>Do you like the free version of this plugin?<br>Did you know We also have a <a href="https://wpfactory.com/item/easy-referral-for-woocommerce/" target="_blank">Pro version</a>?<br></p><h4>Check some of its features for now:</h4>
					<ul style="list-style:disc inside;">
						<li>Unlimited Referral Codes</li>
						<li>Apply Coupong Code automatically giving a discount to Referees</li>
						<li>Customizable Order Status for Referral Creation</li>
						<li>IP Comparing</li>
						<li>Cookie Searching</li>
						<li>Filter Referrals by Period</li>
						<li>Support</li>
						<a style="display:inline-block;margin:15px 0 8px 0" target="_blank" class="button-primary" href="https://wpfactory.com/item/easy-referral-for-woocommerce/">Upgrade to Pro version <i style="position:relative;top:3px;" class="dashicons-before dashicons-awards"></i></a>
					</ul>
				</div>
			<?php endif; ?>
			<?php
		}
	}
}