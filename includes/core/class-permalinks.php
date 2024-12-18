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
 * @version 1.1.0 static method `update_core_pages` removed.
 *
 * @package um_ext\um_polylang\core
 */
class Permalinks {

	/**
	 * Class Permalinks constructor.
	 */
	public function __construct() {

		// Add rewrite rules for the Account and User page.
		add_filter( 'rewrite_rules_array', array( &$this, 'add_rewrite_rules' ), 10, 1 );

		// Links in emails.
		add_filter( 'um_activate_url', array( &$this, 'localize_activate_url' ), 10, 1 );

		// Pages.
		add_filter( 'um_get_core_page_filter', array( &$this, 'localize_core_page_url' ), 10, 3 );
		add_filter( 'um_localize_permalink_filter', array( &$this, 'localize_profile_permalink' ), 10, 2 );
		add_filter( 'page_link', array( $this, 'localize_core_page_link' ), 10, 2 );

		// Logout.
		add_action( 'template_redirect', array( $this, 'localize_logout_page' ), 9990 );

		// Buttons.
		add_filter( 'um_login_form_button_two_url', array( &$this, 'localize_page_url' ), 10, 2 );
		add_filter( 'um_register_form_button_two_url', array( &$this, 'localize_page_url' ), 10, 2 );

		// Filter the link in the language switcher.
		add_filter( 'pll_the_language_link', array( &$this, 'filter_pll_switcher_link' ), 10, 3 );

		// Fix conflict with WooCommerce in Account.
		add_filter( 'woocommerce_account_endpoint_page_not_found', array( &$this, 'filter_woocommerce_not_found' ) );
	}


	/**
	 * Add rewrite rules for the Account and User page.
	 *
	 * @hooked rewrite_rules_array
	 *
	 * @since 1.0.0
	 *
	 * @param  array $rules Rewrite rules.
	 * @return array
	 */
	public function add_rewrite_rules( $rules ) {

		$languages = pll_languages_list();
		$newrules  = array();

		// Account.
		if ( isset( UM()->config()->permalinks['account'] ) ) {
			$account_page_id = UM()->config()->permalinks['account'];
			$account         = get_post( $account_page_id );

			foreach ( $languages as $language ) {
				if ( pll_default_language() === $language && PLL()->options['hide_default'] ) {
					continue;
				}
				$lang_post_id  = pll_get_post( $account_page_id, $language );
				$lang_post_obj = get_post( $lang_post_id );

				if ( isset( $account->post_name ) && isset( $lang_post_obj->post_name ) ) {
					$lang_page_slug = $lang_post_obj->post_name;

					if ( 1 === PLL()->options['force_lang'] ) {
						$newrules[ $language . '/' . $lang_page_slug . '/([^/]+)/?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_tab=$matches[1]&lang=' . $language;
					}

					$newrules[ $lang_page_slug . '/([^/]+)/?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_tab=$matches[1]&lang=' . $language;
				}
			}
		}

		// Profile.
		if ( isset( UM()->config()->permalinks['user'] ) ) {
			$user_page_id = UM()->config()->permalinks['user'];
			$user         = get_post( $user_page_id );

			foreach ( $languages as $language ) {
				if ( pll_default_language() === $language && PLL()->options['hide_default'] ) {
					continue;
				}
				$lang_post_id  = pll_get_post( $user_page_id, $language );
				$lang_post_obj = get_post( $lang_post_id );

				if ( isset( $user->post_name ) && isset( $lang_post_obj->post_name ) ) {
					$lang_page_slug = $lang_post_obj->post_name;

					if ( 1 === PLL()->options['force_lang'] ) {
						$newrules[ $language . '/' . $lang_page_slug . '/([^/]+)/?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_user=$matches[1]&lang=' . $language;
					}

					$newrules[ $lang_page_slug . '/([^/]+)/?$' ] = 'index.php?page_id=' . $lang_post_id . '&um_user=$matches[1]&lang=' . $language;
				}
			}
		}

		return array_merge( $newrules, $rules );
	}


	/**
	 * Filter the link in the language switcher.
	 *
	 * @since   1.0.1
	 * @version 1.1.0 static method `update_core_pages` removed.
	 *
	 * @param string|null $url    The link, null if no translation was found.
	 * @param string      $slug   The language code.
	 * @param string      $locale The language locale.
	 */
	public function filter_pll_switcher_link( $url, $slug, $locale ) {
		$user_id        = um_profile_id();
		$permalink_type = get_option( 'permalink_structure' );

		// Account.
		if ( $url && um_is_core_page( 'account' ) && get_query_var( 'um_tab' ) ) {
			$current_tab = get_query_var( 'um_tab' );

			if ( $permalink_type ) {
				if ( false === strpos( $url, '?' ) ) {
					$account_url = trailingslashit( $url ) . trailingslashit( $current_tab );
				} else {
					$account_url = str_replace( '?', trailingslashit( $current_tab ) . '?', $url );
				}
			} else {
				$account_url = add_query_arg( 'um_tab', strtolower( $current_tab ), $url );
			}
			$url = $account_url;
		}

		// Profile.
		if ( $url && $user_id && um_is_core_page( 'user' ) ) {
			$permalink_base = UM()->options()->get( 'permalink_base' );
			$profile_slug   = strtolower( get_user_meta( $user_id, "um_user_profile_url_slug_{$permalink_base}", true ) );

			if ( $permalink_type ) {
				if ( false === strpos( $url, '?' ) ) {
					$profile_url = trailingslashit( $url ) . trailingslashit( $profile_slug );
				} else {
					$profile_url = str_replace( '?', trailingslashit( $profile_slug ) . '?', $url );
				}
			} else {
				$profile_url = add_query_arg( 'um_user', strtolower( $profile_slug ), $url );
			}
			$url = $profile_url;
		}

		return $url;
	}


	/**
	 * Fix conflict with WooCommerce in Account.
	 *
	 * @global \WP $wp
	 * @param  boolean $not_found Display 404 "Not Found" page or not.
	 * @return boolean
	 */
	public function filter_woocommerce_not_found( $not_found ) {
		global $wp;
		return isset( $wp->query_vars['payment-methods'] ) && um_is_core_page( 'account' ) ? false : $not_found;
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
	 * @hooked um_is_core_page
	 *
	 * @since 1.0.0
	 * @deprecated since version 1.0.1
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
	 * Filter account activation link.
	 *
	 * @hooked um_activate_url
	 *
	 * @see um\core\Permalinks
	 * @since 1.1.0
	 *
	 * @param string $url Account activation link.
	 * @return string Localized account activation link.
	 */
	public function localize_activate_url( $url ){
		if ( ! UM()->Polylang()->is_default() ) {
			$url = add_query_arg( 'lang', UM()->Polylang()->get_current(), $url );
			}
		return $url;
	}


	/**
	 * Filter logout page URL.
	 *
	 * @hooked page_link
	 *
	 * @since version 1.2.1
	 *
	 * @param  string $link    The page's permalink.
	 * @param  int    $post_id The ID of the page.
	 * @return string The page permalink.
	 */
	public function localize_core_page_link( $link, $post_id ) {
		if ( ! UM()->Polylang()->is_default() ) {
			if ( is_array( UM()->config()->permalinks ) && in_array( $post_id, UM()->config()->permalinks, true ) ) {
				$url  = $this->get_page_url_for_language( $post_id, UM()->Polylang()->get_current() );
				$link = ( $link !== $url ) ? $url : add_query_arg( 'lang', pll_current_language(), $url );
			}
		}
		return $link;
	}


	/**
	 * Filter core page URL.
	 *
	 * @hooked um_get_core_page_filter
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
	 * The logout redirect URL.
	 *
	 * @hooked template_redirect - 9990
	 *
	 * @see \um\core\Logout
	 * @since version 1.2.1
	 */
	public function localize_logout_page() {
		if ( is_user_logged_in() && um_is_core_page( 'logout' ) && empty( $_REQUEST['redirect_to'] ) ) {
			$lang = UM()->Polylang()->get_current();

			if ( 'redirect_home' === um_user( 'after_logout' ) ) {
				// if "Action to be taken after logout" is set to "Go to Homepage".

				$link = home_url();
				$url  = PLL()->links->get_home_url( $lang );
				$_REQUEST['redirect_to'] = ( $link !== $url ) ? $url : add_query_arg( 'lang', $lang, $url );

			} elseif( 'redirect_url' === um_user( 'after_logout' ) ) {
				// if "Action to be taken after logout" is set to "Go to Custom URL".

				$redirect_url = apply_filters( 'um_logout_redirect_url', um_user( 'logout_redirect_url' ), um_user( 'ID' ) );
				$page_path    = trim( str_replace( home_url(), '', $redirect_url ), " \t\n\r\0\x0B/\\" );
				$page         = get_page_by_path( $page_path );
				if ( is_object( $page ) ) {
					$redirect_to = $this->get_page_url_for_language( $page->ID, $lang );
					$_REQUEST['redirect_to'] = $redirect_to;
				}
			}
		}
	}


	/**
	 * Filter page URL on buttons.
	 *
	 * @hooked um_login_form_button_two_url
	 * @hooked um_register_form_button_two_url
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
	 * @hooked um_localize_permalink_filter
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
