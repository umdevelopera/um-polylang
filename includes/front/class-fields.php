<?php
namespace um_ext\um_polylang\front;

defined( 'ABSPATH' ) || exit;

/**
 * Translate form fields.
 *
 * Get an instance this way: UM()->Polylang()->front()->fields()
 *
 * @package um_ext\um_polylang\front
 */
class Fields {


	/**
	 * Class Fields constructor.
	 */
	public function __construct() {
		add_action( 'um_after_user_updated', array( &$this, 'profile_bio_update' ), 20, 2 );
		add_filter( 'um_field_value', array( &$this, 'profile_bio_value' ), 20, 3 );
		add_filter( 'um_profile_field_filter_hook__description', array( &$this, 'profile_bio_value' ), 20, 2 );
		add_filter( 'um_profile_bio_key', array( &$this, 'profile_bio_key' ), 20, 2 );
	}


	/**
	 * Get translated biography key
	 *
	 * @since  2.1.7
	 * @hook   um_profile_bio_key
	 *
	 * @param  string $key  Field Key.
	 * @param  array  $args Form Data.
	 * @return string
	 */
	public function profile_bio_key( $key, $args ) {
		if ( 'description' === $key ) {
			$curlang_slug = UM()->Polylang()->get_current();
			$curlang_key  = 'description_' . $curlang_slug;
			if ( um_profile( $curlang_key ) || UM()->fields()->editing ) {
				$key = $curlang_key;
			}
		}
		return $key;
	}


	/**
	 * Get translated biography value
	 *
	 * @since  2.1.7
	 * @hook   um_field_value
	 * @hook   um_profile_field_filter_hook__description
	 *
	 * @param  string       $value Field Value.
	 * @param  array|string $data  Default value or field data.
	 * @param  string|null  $key   Field Key.
	 * @return string
	 */
	public function profile_bio_value( $value, $data, $key = null ) {
		if ( is_null( $key ) && is_array( $data ) ) {
			$key = $data['metakey'];
		}
		if ( 'description' === $key ) {
			$curlang_slug = UM()->Polylang()->get_current();
			$curlang_key  = 'description_' . $curlang_slug;
			if ( um_profile( $curlang_key ) ) {
				$value = um_profile( $curlang_key );
			}
		} elseif ( empty( $value ) && 0 === strpos( $key, 'description_' ) ) {
			$value = um_filtered_value( 'description', $data );
		}
		return $value;
	}


	/**
	 * Save translated biography
	 *
	 * @since 1.0.0
	 * @hook  um_after_user_updated
	 *
	 * @param integer $user_id User ID.
	 * @param array   $args    Form Data.
	 */
	public function profile_bio_update( $user_id, $args ) {
		$curlang_slug = UM()->Polylang()->get_current();
		$curlang_key  = 'description_' . $curlang_slug;
		if ( isset( $args[ $curlang_key ] ) ) {
			update_user_meta( $user_id, $curlang_key, $args[ $curlang_key ] );
			if ( UM()->Polylang()->is_default() ) {
				update_user_meta( $user_id, 'description', $args[ $curlang_key ] );
			}
		} elseif ( isset( $args['description'] ) ) {
			update_user_meta( $user_id, $curlang_key, $args['description'] );
		}
	}

}
