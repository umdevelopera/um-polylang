<?php
/**
 * Localize permalinks.
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Localize permalinks.
 *
 * @package um_ext\um_polylang\core
 */
class Permalinks {


	/**
	 * Class Permalinks constructor.
	 */
	public function __construct() {
		add_filter( 'rewrite_rules_array', array( &$this, 'add_rewrite_rules' ), 10, 1 );

		add_filter( 'um_is_core_page', array( &$this, 'is_core_page' ), 10, 2 );
		add_filter( 'um_get_core_page_filter', array( &$this, 'localize_core_page_url' ), 10, 3 );
		add_filter( 'um_localize_permalink_filter', array( &$this, 'localize_profile_permalink' ), 10, 2 );

		add_filter( 'um_login_form_button_two_url', array( &$this, 'localize_page_url' ), 10, 2 );
		add_filter( 'um_register_form_button_two_url', array( &$this, 'localize_page_url' ), 10, 2 );
	}


	/**
	 * Add UM rewrite rules for the Account page and Profile page.
	 *
	 * @hook rewrite_rules_array
	 *
	 * @since 1.0.0
	 *
	 * @global object $polylang The Polylang instance.
	 * @param  array $rules Rewrite rules.
	 * @return array
	 */
	public function add_rewrite_rules( $rules ) {
		global $polylang;

		$active_languages = pll_languages_list();

		// Account.
		if ( isset( UM()->config()->permalinks['account'] ) ) {
			$account_page_id = UM()->config()->permalinks['account'];
			$account         = get_post( $account_page_id );

			$newrules = array();
			foreach ( $active_languages as $language_code ) {
				if ( pll_default_language() === $language_code && $polylang->options['hide_default'] ) {
					continue;
				}
				$lang_post_id  = pll_get_post( $account_page_id, $language_code );
				$lang_post_obj = get_post( $lang_post_id );

				if ( isset( $account->post_name ) && isset( $lang_post_obj->post_name ) ) {
					$lang_page_slug = $lang_post_obj->post_name;

					if ( 1 === $polylang->options['force_lang'] ) {
						$newrules[ $language_code . '/' . $lang_page_slug . '/([^/]+)?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_tab=$matches[1]&lang=' . $language_code;
					}

					$newrules[ $lang_page_slug . '/([^/]+)?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_tab=$matches[1]&lang=' . $language_code;
				}
			}
			$rules = $newrules + $rules;
		}

		// Profile.
		if ( isset( UM()->config()->permalinks['user'] ) ) {
			$user_page_id = UM()->config()->permalinks['user'];
			$user         = get_post( $user_page_id );

			$newrules = array();
			foreach ( $active_languages as $language_code ) {
				if ( pll_default_language() === $language_code && $polylang->options['hide_default'] ) {
					continue;
				}
				$lang_post_id  = pll_get_post( $user_page_id, $language_code );
				$lang_post_obj = get_post( $lang_post_id );

				if ( isset( $user->post_name ) && isset( $lang_post_obj->post_name ) ) {
					$lang_page_slug = $lang_post_obj->post_name;

					if ( 1 === $polylang->options['force_lang'] ) {
						$newrules[ $language_code . '/' . $lang_page_slug . '/([^/]+)/?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_user=$matches[1]&lang=' . $language_code;
					}

					$newrules[ $lang_page_slug . '/([^/]+)/?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_user=$matches[1]&lang=' . $language_code;
				}
			}
			$rules = $newrules + $rules;
		}

		return $rules;
	}


	/**
	 * Get translated page URL.
	 *
	 * @since 1.0.0
	 *
	 * @param  integer $post_id  The post/page ID.
	 * @param  string  $language Slug or locale of the queried language.
	 * @return string|false
	 */
	public function get_page_url_for_language( $post_id, $language = '' ) {
		$lang = '';
		if ( is_string( $language ) && strlen( $language ) > 2 ) {
			$lang = current( explode( '_', $language ) );
		} elseif ( is_string( $language ) ) {
			$lang = trim( $language );
		}

		$lang_post_id = pll_get_post( $post_id, $lang );

		if ( $lang_post_id && is_numeric( $lang_post_id ) ) {
			$url = get_permalink( $lang_post_id );
		} else {
			$url = get_permalink( $post_id );
		}

		return $url;
	}


	/**
	 * Check if the current page is a UM Core Page or not.
	 *
	 * @hook   um_is_core_page
	 *
	 * @since 1.0.0
	 *
	 * @global \WP_Post $post
	 * @param  boolean $is_core_page Is core page.
	 * @param  string  $page         Page key.
	 * @return boolean
	 */
	public function is_core_page( $is_core_page, $page ) {
		global $post;

		$lang_post_id = absint( pll_get_post( $post->ID, pll_default_language() ) );
		$um_page_id   = isset( UM()->config()->permalinks[ $page ] ) ? absint( UM()->config()->permalinks[ $page ] ) : 0;
		if ( $um_page_id && $um_page_id === $lang_post_id ) {
			$is_core_page = true;
		}

		return $is_core_page;
	}


	/**
	 * Filter core page URL.
	 *
	 * @hook um_get_core_page_filter
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url     Default page URL.
	 * @param  string $slug    Core page slug.
	 * @param  string $updated Additional parameter 'updated' value.
	 * @return string
	 */
	public function localize_core_page_url( $url, $slug, $updated = '' ) {
		if ( ! UM()->Polylang()->is_default() ) {
			$page_id = UM()->config()->permalinks[ $slug ];
			$url     = $this->get_page_url_for_language( $page_id, UM()->Polylang()->get_current() );
			if ( $updated ) {
				$url = add_query_arg( 'updated', esc_attr( $updated ), $url );
			}
		}
		return $url;
	}


	/**
	 * Filter page URL on buttons.
	 *
	 * @hook um_login_form_button_two_url
	 * @hook um_register_form_button_two_url
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url  Page URL or slug.
	 * @param  array  $args Additional data.
	 * @return string
	 */
	public function localize_page_url( $url, $args = array() ) {
		$page = get_page_by_path( trim( $url, "/ \n\r\t\v\0" ) );
		if ( $page && is_a( $page, '\WP_Post' ) && ! UM()->Polylang()->is_default() ) {
			$url = $this->get_page_url_for_language( $page->ID, UM()->Polylang()->get_current() );
		}
		return $url;
	}


	/**
	 * Filter profile page URL.
	 *
	 * @hook um_localize_permalink_filter
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $profile_url Default profile URL.
	 * @param  integer $page_id     The page ID.
	 * @return string
	 */
	public function localize_profile_permalink( $profile_url, $page_id ) {
		return $this->get_page_url_for_language( $page_id );
	}

}
