<?php
/**
 * Easy Referral for WooCommerce - Notices
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Admin_Notices' ) ) {

	class Admin_Notices {
		function init() {
			add_action( 'admin_notices', array( $this, 'show_free_version_notice' ),999 );
		}

		function can_display_free_version_notice() {
			return
				true !== apply_filters( 'erwc_is_free_version', true, 'show_free_version_notice' ) ||
				! isset( $_REQUEST['tab'] ) ||
				! isset( $_REQUEST['page'] ) ||
				$_REQUEST['tab'] != 'erwc' ||
				$_REQUEST['page'] != 'wc-settings';
		}

		function show_free_version_notice() {
			if ( $this->can_display_free_version_notice() ) {
				return;
			}
			//$chance = mt_rand( 0, 1 );
			$chance = 0;
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
						<a style="display:inline-block;margin:15px 0 5px 0" href="https://wordpress.org/support/plugin/easy-referral-for-woocommerce/reviews/#new-post" target="_blank" class="button button-primary">Add a Review</a>
					</p>
				</div>
			<?php else: ?>
				<!--<div class="notice notice-info">
					<h3 class="title"><?php _e( 'Easy Referral for WooCommerce - Premium', 'easy-referral-for-woocommerce' ) ?></h3>
					<p>Do you like the free version of this plugin?<br>Did you
						know We also have a <a href="https://wpfactory.com/item/easy-referral-for-woocommerce/"
						                       target="_blank">Premium one</a>?<br></p><h4>Check some of its features
						for
						now:</h4>
					<ul style="list-style:disc inside;">
						<li>Customize Pop-up style using the Customizer including Icons from FontAwesome</li>
						<li>Customize WooCommerce Messages</li>
						<li>Ignore messages</li>
						<li>Avoid repeated messages</li>
						<li>Load the plugin at some specific pages, like cart or checkout for example.</li>
						<li>Play sounds when popup opens or closes</li>
						<li>Hide default notices</li>
						<li>Support</li>
						<p style="margin-top:15px">Buying it will allow you managing and customizing the popup entirely,
							helping maintaining the development of this plugin.<br>And besides you aren't going to see
							these
							annoying messages anymore :)</p><a style="display:inline-block;margin:15px 0 8px 0"
						                                       target="_blank" class="button-primary"
						                                       href="https://wpfactory.com/item/easy-referral-for-woocommerce/">Upgrade
							to Premium version</a></ul>
				</div>-->
			<?php endif; ?>
			<?php
		}
	}
}