<?php
/**
 * Class um_ext\um_polylang\admin\PLL_Settings
 *
 * @package um_ext\um_polylang\admin
 */

namespace um_ext\um_polylang\admin;

defined( 'ABSPATH' ) || exit;

/**
 * Extend Polylang settings.
 *
 * Get an instance this way: UM()->Polylang()->admin()->pll_settings()
 *
 * @package um_ext\um_polylang\admin
 */
class PLL_Settings {

	const POST_TYPES = array(
		'um_form'         => 'um_form',
		'um_account_tabs' => 'um_account_tabs',
		'um_profile_tabs' => 'um_profile_tabs',
	);


	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Translatable post types.
		add_filter( 'pll_get_post_types', array( $this, 'pll_get_post_types' ), 10, 2 );
		add_filter( 'pre_update_option_polylang', array( $this, 'pll_update_option_post_types' ) );

		// Translate strings.
		add_action( 'load-languages_page_mlang_strings', array( $this, 'register_strings' ), 10 );
	}


	/**
	 * Filters the list of post types available for translation.
	 *
	 * @param string[] $post_types  List of post type names (as array keys and values).
	 * @param bool     $is_settings True when displaying the list of custom post types in Polylang settings.
	 *
	 * @return string[] List of post type names.
	 */
	public function pll_get_post_types( $post_types, $is_settings ) {
		$um_post_types = (array) apply_filters( 'um_pll_get_post_types', self::POST_TYPES, $is_settings );
		return array_merge( $post_types, $um_post_types );
	}


	/**
	 * Filters the 'polylang' option before its value is updated.
	 *
	 * @since 1.2.2
	 *
	 * @param mixed $value The new, unserialized option value.
	 */
	public function pll_update_option_post_types( $value ) {
		$um_post_types       = (array) apply_filters( 'um_pll_get_post_types', self::POST_TYPES, false );
		$value['post_types'] = empty( $value['post_types'] ) ? $um_post_types : array_merge( $value['post_types'], $um_post_types );
		return $value;
	}


	/**
	 * Translate field labels.
	 *
	 * @link https://polylang.pro/documentation/support/guides/strings-translation/
	 * @link https://polylang.pro/documentation/support/developers/function-reference/#pll_register_string
	 *
	 * @since 1.3.0
	 */
	public function register_strings() {
		$fields = UM()->builtin()->get_all_user_fields();
		foreach ( $fields as $metakey => $field ) {
			if ( ! empty( $field['label'] ) ) {
				$name   = $metakey . '-label';
				$string = sanitize_text_field( $field['label'] );
				pll_register_string( $name, $string, 'ultimate-member' );
			}
			if ( ! empty( $field['help'] ) ) {
				$name   = $metakey . '-help';
				$string = sanitize_text_field( $field['help'] );
				pll_register_string( $name, $string, 'ultimate-member' );
			}
			if ( ! empty( $field['placeholder'] ) ) {
				$name   = $metakey . '-placeholder';
				$string = sanitize_text_field( $field['placeholder'] );
				pll_register_string( $name, $string, 'ultimate-member' );
			}
			if ( ! empty( $field['options'] ) && is_array( $field['options'] ) && empty( $field['custom_dropdown_options_source'] ) ) {
				foreach ( $field['options'] as $ok => $ov ) {
					$name   = $metakey . '-option-' . sanitize_key( $ok );
					$string = sanitize_text_field( $ov );
					pll_register_string( $name, $string, 'ultimate-member' );
				}
			}
		}
	}
}
