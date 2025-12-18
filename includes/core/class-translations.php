<?php
/**
 * Class um_ext\um_polylang\core\Translations
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

defined( 'ABSPATH' ) || exit;

/**
 * Translate fields and filters using the String Translation feature.
 *
 * Get an instance this way: UM()->Polylang()->core()->translations()
 *
 * @link https://polylang.pro/documentation/support/guides/strings-translation/
 *
 * @package um_ext\um_polylang\core
 *
 * @since 1.3.0
 */
class Translations {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Translate form fields.
		add_filter( 'um_get_form_fields', array( $this, 'translate_form_fields' ), 100, 2 );

		// Translate field label.
		add_filter( 'um_change_field_label', array( $this, 'pll_esc_html' ) );

		// Translate field value.
		add_filter( 'um_profile_field_filter_hook__', array( $this, 'pll_esc_html' ) );
		// account.
		add_action( 'shortcode_atts_ultimatemember_account', array( $this, 'translate_value_on' ) );
		add_action( 'um_after_account_page_load', array( $this, 'translate_value_off' ) );
		// profile.
		add_action( 'um_before_form', array( $this, 'translate_value_on' ) );
		add_action( 'um_after_form', array( $this, 'translate_value_off' ) );

		// Translate filter label.
		add_filter( 'um_search_fields', array( $this, 'translate_filter_label' ) );

		// Translate filter options.
		add_filter( 'um_member_directory_filter_select_options_sorted', array( $this, 'translate_filter_options' ), 10, 2 );
	}

	/**
	 * Translating and escaping for HTML blocks.
	 *
	 * @see esc_html()
	 * @since 1.3.0
	 *
	 * @param array|string $text Text.
	 * @return array|string Translated text.
	 */
	public function pll_esc_html( $text ) {
		if ( is_array( $text ) ) {
			foreach ( $text as &$option ) {
				$option = $this->pll_esc_html( $option );
			}
		} else {
			$translate_text = pll__( $text );
			$text           = _wp_specialchars( $translate_text, ENT_QUOTES );
		}
		return $text;
	}

	/**
	 * Thanslate filter label.
	 *
	 * Hook: um_search_fields - 10.
	 *
	 * @since 1.3.0
	 *
	 * @param array $field Filter data.
	 * @return array Filter data with translated label.
	 */
	public function translate_filter_label( $field ) {
		if ( ! empty( $field['label'] ) ) {
			$field['label'] = pll__( $field['label'] );
		}

		// Maybe translate options.
		if ( ! empty( $field['options'] ) && is_array( $field['options'] )
				&& array_is_list( $field['options'] ) // skip fields with predefined keys.
				&& isset( $field['metakey'] ) // skip fields without mete key.
				&& empty( strstr( $field['metakey'], 'role_' ) ) // skip role fields.
				&& empty( $field['custom_dropdown_options_source'] ) // skip fields with custom options source.
				) {
			$field['options_pll'] = true;
			$field['custom']      = true;
		}

		return $field;
	}

	/**
	 * Translate filter options.
	 *
	 * Hook: um_member_directory_filter_select_options_sorted - 10.
	 *
	 * @since 1.3.0
	 *
	 * @param array $options Filtered options.
	 * @param array $field   Filter data.
	 * @return array Translated filter options.
	 */
	public function translate_filter_options( $options, $field ) {
		if ( empty( $field['options_pll'] ) ) {
			$options = $this->pll_esc_html( $options );
		} else {
			$options = array_combine( $options, $this->pll_esc_html( $options ) );
		}
		asort( $options );
		return $options;
	}

	/**
	 * Translate form fields.
	 *
	 * Hook: um_get_form_fields - 100.
	 *
	 * @since 1.3.0
	 *
	 * @param array $fields  Form fields.
	 * @param int   $form_id Form ID.
	 * @return array Form fields with translated "Label", "Help Text" and "Placeholder".
	 */
	public function translate_form_fields( $fields, $form_id ) {
		if ( is_array( $fields ) && $form_id ) {
			foreach ( $fields as &$field ) {
				if ( ! empty( $field['label'] ) ) {
					$field['label'] = pll__( $field['label'] );
				}
				if ( ! empty( $field['help'] ) ) {
					$field['help'] = pll__( $field['help'] );
				}
				if ( ! empty( $field['placeholder'] ) ) {
					$field['placeholder'] = pll__( $field['placeholder'] );
				}
			}
		}
		return $fields;
	}

	/**
	 * Enable translation of field values.
	 *
	 * Hook: shortcode_atts_ultimatemember_account - 10.
	 * Hook: um_before_form - 10.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $data Filter data.
	 */
	public function translate_value_on( $data = null ) {
		add_filter( 'esc_html', array( $this, 'pll_esc_html' ), 14 );
		return $data;
	}

	/**
	 * Disable translation of field values.
	 *
	 * Hook: um_after_account_page_load - 10.
	 * Hook: um_after_form - 10.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $data Filter data.
	 */
	public function translate_value_off( $data = null ) {
		remove_filter( 'esc_html', array( $this, 'pll_esc_html' ), 14 );
		return $data;
	}
}
