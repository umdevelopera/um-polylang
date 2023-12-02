<?php
/**
 * Translate form.
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translate form.
 *
 * @package um_ext\um_polylang\core
 */
class Form {


	/**
	 * Class Form constructor.
	 */
	public function __construct() {
		add_filter( 'shortcode_atts_ultimatemember', array( &$this, 'shortcode_atts_ultimatemember' ), 20, 1 );
		add_filter( 'um_pre_args_setup', array( &$this, 'pre_args_setup' ), 20, 1 );
	}


	/**
	 * Filters shortcode attributes.
	 * Replaces 'form_id' attribute if translated form exists.
	 *
	 * @since 1.1.0
	 *
	 * @link https://developer.wordpress.org/reference/hooks/shortcode_atts_shortcode/
	 *
	 * @param array $args Shortcode arguments.
	 *
	 * @return array Shortcode arguments.
	 */
	public function shortcode_atts_ultimatemember( $args ){
		if ( isset( $args['form_id'] ) ) {
			$form_id            = absint( $args['form_id'] );
			$translated_form_id = pll_get_post( $form_id, pll_current_language() );

			if ( $translated_form_id && $translated_form_id !== $form_id ) {
				$args['form_id'] = $translated_form_id;
			}
		}
		return $args;
	}


	/**
	 * Gets data from the original form if the translated form doesn't have this data.
	 *
	 * @hook um_pre_args_setup
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args Arguments.
	 * @return array
	 */
	public function pre_args_setup( $args ) {
		if ( isset( $args['form_id'] ) ) {
			$form_id          = absint( $args['form_id'] );
			$original_form_id = pll_get_post( $form_id, pll_default_language() );

			if ( $original_form_id && $original_form_id !== $form_id ) {
				$original_post_data = UM()->query()->post_data( $original_form_id );

				foreach ( $original_post_data as $key => $value ) {
					if ( ! array_key_exists( $key, $args ) ) {
						$args[ $key ] = $value;
					}
				}
			}
		}

		return $args;
	}

}
