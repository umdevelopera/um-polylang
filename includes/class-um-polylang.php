<?php
defined( 'ABSPATH' ) || exit;

/**
 * The "Ultimate Member - Polylang" extension initialization.
 *
 * Get an instance this way: UM()->Polylang()
 *
 * @package um_ext\um_polylang
 */
class UM_Polylang {


	/**
	 * An instance of the class.
	 *
	 * @var UM_Polylang
	 */
	private static $instance;


	/**
	 * Creates an instance of the class.
	 *
	 * @return UM_Polylang
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor.
	 */
	public function __construct() {

		$this->core();
		if ( UM()->is_request( 'admin' ) ) {
			$this->admin();
			$this->posts();
		} elseif ( UM()->is_request( 'frontend' ) ) {
			$this->front();
		}

		// Extensions.
		if ( defined( 'um_account_tabs_version' ) ) {
			require_once um_polylang_path . 'includes/extensions/account-tabs.php';
		}
		if ( defined( 'UM_PROFILE_TABS_VERSION' ) && is_callable( array( UM(), 'Profile_Tabs' ) ) ) {
			require_once um_polylang_path . 'includes/extensions/profile-tabs.php';
		}
	}


	/**
	 * Subclass that extends wp-admin features.
	 *
	 * @since 1.1.0
	 *
	 * @return um_ext\um_polylang\admin\Admin()
	 */
	public function admin() {
		if ( empty( UM()->classes['um_polylang_admin'] ) ) {
			require_once um_polylang_path . 'includes/admin/class-init.php';
			UM()->classes['um_polylang_admin'] = new um_ext\um_polylang\admin\Init();
		}
		return UM()->classes['um_polylang_admin'];
	}


	/**
	 * Common functionality.
	 *
	 * @since 1.2.2
	 *
	 * @return um_ext\um_polylang\core\Init()
	 */
	public function core() {
		if ( empty( UM()->classes['um_polylang_core'] ) ) {
			require_once um_polylang_path . 'includes/core/class-init.php';
			UM()->classes['um_polylang_core'] = new um_ext\um_polylang\core\Init();
		}
		return UM()->classes['um_polylang_core'];
	}


	/**
	 * Front-end functionality.
	 *
	 * @since 1.2.2
	 *
	 * @return um_ext\um_polylang\front\Init()
	 */
	public function front() {
		if ( empty( UM()->classes['um_polylang_front'] ) ) {
			require_once um_polylang_path . 'includes/front/class-init.php';
			UM()->classes['um_polylang_front'] = new um_ext\um_polylang\front\Init();
		}
		return UM()->classes['um_polylang_front'];
	}


	/**
	 * Subclass that creates translated posts and forms.
	 *
	 * @since 1.1.1
	 *
	 * @return um_ext\um_polylang\core\Posts()
	 */
	public function posts() {
		if ( empty( UM()->classes['um_polylang_posts'] ) ) {
			require_once um_polylang_path . 'includes/core/class-posts.php';
			UM()->classes['um_polylang_posts'] = new um_ext\um_polylang\core\Posts();
		}
		return UM()->classes['um_polylang_posts'];
	}


	/**
	 * Subclass that do actions on installation.
	 *
	 * @since 1.1.0
	 *
	 * @return um_ext\um_polylang\core\Setup()
	 */
	public function setup() {
		if ( empty( UM()->classes['um_polylang_setup'] ) ) {
			require_once um_polylang_path . 'includes/core/class-setup.php';
			UM()->classes['um_polylang_setup'] = new um_ext\um_polylang\core\Setup();
		}
		return UM()->classes['um_polylang_setup'];
	}


	/**
	 * Returns the current language.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $field Optional, the language field to return (@see PLL_Language), defaults to `'slug'`.
	 * @return string|int|bool|string[]|PLL_Language The requested field or object for the current language, `false` if the field isn't set.
	 */
	public function get_current( $field = 'slug' ) {

		$lang = pll_current_language();
		if ( isset( $_GET['lang'] ) ) {
			$lang = sanitize_key( $_GET['lang'] );
		}
		if ( empty( $lang ) || 'all' === $lang ) {
			$locale = determine_locale();
			$lang   = substr( $locale, 0, 2 );
		}
		$language = PLL()->model->get_language( $lang );

		return is_object( $language ) ? $language->get_prop( $field ) : $lang;
	}


	/**
	 * Returns the default language.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $field Optional, the language field to return (@see PLL_Language), defaults to `'slug'`.
	 * @return string|int|bool|string[]|PLL_Language The requested field or object for the default language, `false` if the field isn't set.
	 */
	public function get_default( $field = 'slug' ) {
		return pll_default_language( $field );
	}


	/**
	 * Returns the list of available languages.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_languages_list() {
		return pll_languages_list();
	}


	/**
	 * Returns an object with the language details.
	 *
	 * @since 1.2.2
	 *
	 * @param string $lang Language code.
	 * @return object Language info.
	 */
	public function get_translation( $lang ) {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		$translations = wp_get_available_translations();

		switch( $lang ) {
			case 'ca': $locale = 'en_CA'; break;
			case 'en': $locale = 'en_GB'; break;
			case 'us': $locale = 'en_US'; break;
			case 'ar': $locale = 'es_AR'; break;
			case 'co': $locale = 'es_CO'; break;
			case 'mx': $locale = 'es_MX'; break;
			case 'br': $locale = 'pt_BR'; break;
			default: $locale = $lang . '_' . strtoupper( $lang ); break;
		}

		if ( array_key_exists( $lang, $translations ) ) {
			$translation = $translations[ $lang ];
		} elseif ( array_key_exists( $locale, $translations ) ) {
			$translation = $translations[ $locale ];
		} else {
			foreach ( $translations as $t ) {
				if ( in_array( $lang, $t['iso'], true ) ) {
					$translation = $t;
					break;
				}
			}
		}
		return empty( $translation ) ? false : (object) $translation;
	}


	/**
	 * Check if Polylang is active.
	 *
	 * @since   1.0.0
	 * @version 1.1.0 Check for the PLL function.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return defined( 'POLYLANG_VERSION' ) && function_exists( 'PLL' );
	}


	/**
	 * Check if the default language is chosen.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_default() {
		return $this->get_current() === $this->get_default();
	}

}
