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
		add_filter( 'um_pre_args_setup', array( &$this, 'pre_args_setup' ), 20, 1 );
	}


	/**
	 * Get arguments from original form if translated form doesn't have this data.
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
					if ( ! isset( $args[ $key ] ) ) {
						$args[ $key ] = $value;
					}
				}
			}
		}

		return $args;
	}

}
