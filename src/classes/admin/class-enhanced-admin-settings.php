<?php
/**
 * Easy Referral for WooCommerce - Enhanced Admin Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Thanks to IT
 */

namespace ThanksToIT\ERWC\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'ThanksToIT\ERWC\Admin\Enhanced_Admin_Settings' ) ) {

	class Enhanced_Admin_Settings {
		public $settings_id = '';
		public $premium_link = '';
		public $premium_section_text = '';
		public $args = array();

		/**
		 * Init.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings_id
		 * @param array $args
		 */
		function init( $settings_id, $args = array() ) {
			$this->args        = wp_parse_args( $args, array(
				'premium_link'             => '',
				'add_premium_section_text' => true,
				'premium_section_text'     => __( 'Unlock it using the <a target="_blank" href="{{premium_link}}">Premium</a> version.', 'easy-referral-for-woocommerce' ),
			) );
			$this->settings_id = $settings_id;
			add_filter( 'woocommerce_get_settings_' . $this->settings_id, array( $this, 'create_premium_fields_from_premium_sections' ), 10 );
			add_filter( 'woocommerce_get_settings_' . $this->settings_id, array( $this, 'setup_premium_fields' ), 11 );
			add_action( 'admin_head', array( $this, 'admin_style' ) );
		}

		/**
		 * admin_style.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function admin_style() {
			if (
				! isset( $_REQUEST['tab'] ) ||
				! isset( $_REQUEST['page'] ) ||
				$_REQUEST['tab'] != $this->settings_id ||
				$_REQUEST['page'] != 'wc-settings'
			) {
				return;
			}
			?>
            <style>
                .eas {
                    background: #e8e8e8;
                    padding: 4px 9px 6px;
                    color: #999;
                    font-size: 13px;
                    vertical-align: middle;
                    /*margin: 0 0 0 10px;*/
                }
            </style>
			<?php
		}

		/**
		 * setup_premium_field.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		function setup_premium_fields( $settings ) {
			$premium_fields = wp_list_filter( $settings, array( 'premium_field' => true ) );
			foreach ( $premium_fields as $key => $field ) {
				$settings[ $key ]['custom_attributes']['disabled'] = 'disabled';
				$settings[ $key ]['custom_attributes']['readonly'] = 'readonly';
				$settings                                          = $this->handle_text_on_field( $settings, $key );
				$settings                                          = $this->handle_premium_section_text_on_field( $settings, $key );
			}

			return $settings;
		}

		/**
		 * replace_template_variables.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $text
		 *
		 * @return mixed
		 */
		function replace_template_variables( $text ) {
			$text = str_replace( "{{premium_link}}", $this->args['premium_link'], $text );
			return $text;
		}

		/**
		 * handle_text_on_field.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings
		 * @param $field_key
		 *
		 * @return mixed
		 */
		function handle_text_on_field( $settings, $field_key ) {
			if ( ! isset( $settings[ $field_key ]['premium_text'] ) || empty( $settings[ $field_key ]['premium_text'] ) ) {
				return $settings;
			}
			$premium_info                   = $this->replace_template_variables( $settings[ $field_key ]['premium_text'] );
			$premium_info                   = $this->add_html_tags( $premium_info, 'field' );
			$settings[ $field_key ]['desc'] = empty( $settings[ $field_key ]['desc'] ) ? $premium_info : $settings[ $field_key ]['desc'] . '  ' . $premium_info;
			return $settings;
		}

		function handle_premium_section_text_on_field( $settings, $field_key ) {
			if ( ! isset( $settings[ $field_key ]['add_premium_section_text'] ) || ! ( $settings[ $field_key ]['add_premium_section_text'] ) ) {
				return $settings;
			}
			return $this->handle_text_on_premium_section( $settings, $field_key );
		}

		/**
		 * handle_text_on_premium_section.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings
		 * @param $field_key
		 *
		 * @return mixed
		 */
		function handle_text_on_premium_section( $settings, $field_key ) {
			if ( ! $this->args['add_premium_section_text'] ) {
				return $settings;
			}
			$premium_info                   = $this->replace_template_variables( $this->args['premium_section_text'] );
			$premium_info                   = $this->add_html_tags( $premium_info );
			$settings[ $field_key ]['desc'] = empty( $settings[ $field_key ]['desc'] ) ? $premium_info : $settings[ $field_key ]['desc'] . '  ' . $premium_info;
			return $settings;
		}

		/**
		 * add_html_tags.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $text
		 * @param string $text_type
		 *
		 * @return string
		 */
		function add_html_tags( $text, $text_type = 'section' ) {
			return '<span class="eas ' . $text_type . '">' . $text . '</span>';
		}

		/**
		 * add_premium_fields_from_premium_sections.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		function create_premium_fields_from_premium_sections( $settings ) {
			$premium_sections = wp_list_filter( $settings, array( 'premium_section' => true ) );
			$indexes          = array();
			foreach ( $premium_sections as $key => $section ) {
				$end = wp_list_filter( $settings, array( 'id' => $premium_sections[ $key ]['id'], 'type' => 'sectionend' ) );
				reset( $end );
				$end_key   = key( $end );
				$indexes[] = array( 'start' => $key, 'end' => $end_key );
				$settings  = $this->handle_text_on_premium_section( $settings, $key );
			}
			foreach ( $indexes as $key => $index ) {
				for ( $i = 0; $i < count( $settings ) - 1; $i ++ ) {
					if ( $i > $index['start'] && $i < $index['end'] ) {
						$settings[ $i ]['premium_field'] = true;
					}
				}
			}
			return $settings;
		}
	}
}