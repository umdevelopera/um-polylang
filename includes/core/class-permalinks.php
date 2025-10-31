<?php
namespace um_ext\um_polylang\core;

defined( 'ABSPATH' ) || exit;

/**
 * Localize links.
 *
 * Get an instance this way: UM()->Polylang()->core()->permalinks()
 *
 * @version 1.1.0 static method `update_core_pages` removed.
 * @version 1.2.2 public method `localize_reset_url` added.
 *
 * @package um_ext\um_polylang\core
 */
class Permalinks {

	public $is_switcher = false;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Add rewrite rules for the Account and User page.
		add_filter( 'rewrite_rules_array', array( &$this, 'add_rewrite_rules' ), 10, 1 );

		// Links in emails.
		add_action( 'um_before_email_notification_sending', array( $this, 'before_email' ) );

		// Pages.
		add_filter( 'um_get_core_page_filter', array( &$this, 'localize_core_page_url' ), 10, 3 );
		add_filter( 'um_profile_permalink', array( $this, 'localize_profile_permalink' ), 10, 3 );
		add_filter( 'page_link', array( $this, 'localize_core_page_link' ), 10, 2 );

		// Detect PLL shitcher.
		add_filter( 'pll_the_languages_args', function( $args ) {
			$this->is_switcher = true;
			return $args;
		} );
		add_filter( 'pll_the_languages', function( $html ) {
			$this->is_switcher = false;
			return $html;
		} );

		// Filter links in the language switcher.
		add_filter( 'pll_the_language_link', array( &$this, 'filter_pll_switcher_link' ), 10, 3 );

		// Fix conflict with WooCommerce in Account.
		add_filter( 'woocommerce_account_endpoint_page_not_found', array( &$this, 'filter_woocommerce_not_found' ) );

		// Logout.
		add_action( 'template_redirect', array( $this, 'localize_logout_page' ), 9990 );

		// Buttons.
		add_filter( 'um_login_form_button_two_url', array( &$this, 'localize_page_url' ), 10, 2 );
		add_filter( 'um_register_form_button_two_url', array( &$this, 'localize_page_url' ), 10, 2 );
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
	 * Before email notification sending.
	 *
	 * @since 1.2.2
	 */
	public function before_email() {

		// Localize {account_activation_link}.
		add_filter( 'um_activate_url', array( $this, 'localize_activate_url' ), 10, 1 );

		// Localize {password_reset_link}.
		add_filter( 'um_get_core_page_filter', array( $this, 'localize_reset_url' ), 10, 3 );
	}


	/**
	 * Filter links in the language switcher.
	 *
	 * @since   1.0.1
	 * @version 1.1.0 static method `update_core_pages` removed.
	 *
	 * @param string|null $url    The link, null if no translation was found.
	 * @param string      $slug   The language code.
	 * @param string      $locale The language locale.
	 */
	public function filter_pll_switcher_link( $url, $slug, $locale ) {
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
		if ( $url && um_is_core_page( 'user' ) && um_get_requested_user() ) {
			$user_id        = um_profile_id();
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
	 * @version 1.2.2 static variable $cache added.
	 *
	 * @staticvar array $cache Cached URLs.
	 * @param integer $post_id The post/page ID.
	 * @param string  $lang    Slug or locale of the queried language.
	 * @return string|false
	 */
	public function get_page_url_for_language( $post_id, $lang = '' ) {
		if ( is_string( $lang ) && strlen( $lang ) > 2 ) {
			$lang = current( explode( '_', $lang ) );
		} elseif ( is_string( $lang ) ) {
			$lang = trim( $lang );
		}

		static $cache = array();
		if ( ! array_key_exists( "$post_id:$lang", $cache ) ) {
			$lang_post_id              = pll_get_post( $post_id, $lang );
			$cache[ "$post_id:$lang" ] = $lang_post_id ? get_permalink( $lang_post_id ) : get_permalink( $post_id );
		}

		return $cache[ "$post_id:$lang" ];
	}


	/**
	 * Filter account activation link.
	 * Hook: um_activate_url
	 *
	 * @see \um\core\Permalinks
	 *
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
    global $wp_current_filter;
    
    if ( $this->is_switcher ) { // Do not localize links in the PLL language switcher.
      return $link;
    }

    static $is_loop = false;
    if ( $is_loop || count( array_keys( $wp_current_filter, 'page_link' ) ) > 1 ) {
      // Avoid getting stuck in loops.

    } elseif ( is_array( UM()->config()->permalinks ) && in_array( $post_id, UM()->config()->permalinks, true ) ) {
      // Localize links only for the Ultimate Member pages.

      if ( pll_get_post_language( $post_id ) !== pll_current_language() ) {
        // Skip already localized links.
        
        $is_loop = true;
        $url     = $this->get_page_url_for_language( $post_id, UM()->Polylang()->get_current() );
        $link    = ( $link !== $url ) ? $url : add_query_arg( 'lang', pll_current_language(), $url );
      }
      $is_loop = false;
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
	 * Hook: um_profile_permalink - 10
	 *
	 * @since 1.0.0
	 * @version 1.2.2 parameter $slug added.
	 *
	 * @param string  $profile_url Default profile URL.
	 * @param integer $page_id     The page ID.
	 * @param string  $slug        User profile slug.
	 * @return string
	 */
	public function localize_profile_permalink( $profile_url, $page_id, $slug ) {
		$url = $this->get_page_url_for_language( $page_id );
		if ( UM()->is_permalinks ) {
			$profile_url = trailingslashit( $url ) . trailingslashit( strtolower( $slug ) );
		} else {
			$profile_url = add_query_arg( 'um_user', strtolower( $slug ), $url );
		}
		return $profile_url;
	}


	/**
	 * Localize password reset link - {password_reset_link}.
	 * Hook: um_get_core_page_filter
	 *
	 * @see \um\core\Password
	 *
	 * @since 1.2.2
	 *
	 * @param string $url     UM Page URL.
	 * @param string $slug    UM Page slug.
	 * @param bool   $updated Additional parameter.
	 * @return string Password reset page URL.
	 */
	public function localize_reset_url( $url, $slug, $updated ) {
		if ( 'password-reset' === $slug && false === $updated ) {
			if ( ! UM()->Polylang()->is_default() ) {
				$url = add_query_arg( 'lang', UM()->Polylang()->get_current(), $url );
			}
		}
		return $url;
	}

}
