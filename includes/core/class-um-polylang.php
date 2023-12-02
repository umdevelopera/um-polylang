<?php
/**
 * Init the extension.
 *
 * @package um_ext\um_polylang\core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The "Ultimate Member - Polylang" extension initialization.
 *
 * @package um_ext\um_polylang\core
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
	 * Class UM_Polylang constructor.
	 */
	public function __construct() {
		$this->mail();
		$this->permalinks();

		if( UM()->is_ajax() ) {

		} elseif ( UM()->is_request( 'admin' ) ) {
			$this->admin();
		} elseif ( UM()->is_request( 'frontend' ) ) {
			$this->fields();
			$this->form();
		}

		add_action( 'plugins_loaded', array( $this, 'textdomain' ), 9 );
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
			require_once um_polylang_path . 'includes/admin/class-admin.php';
			UM()->classes['um_polylang_admin'] = new um_ext\um_polylang\admin\Admin();
		}
		return UM()->classes['um_polylang_admin'];
	}


	/**
	 * Subclass that translates form fields.
	 *
	 * @return um_ext\um_polylang\core\Fields()
	 */
	public function fields() {
		if ( empty( UM()->classes['um_polylang_fields'] ) ) {
			require_once um_polylang_path . 'includes/core/class-fields.php';
			UM()->classes['um_polylang_fields'] = new um_ext\um_polylang\core\Fields();
		}
		return UM()->classes['um_polylang_fields'];
	}


	/**
	 * Subclass that translates form.
	 *
	 * @return um_ext\um_polylang\core\Form()
	 */
	public function form() {
		if ( empty( UM()->classes['um_polylang_form'] ) ) {
			require_once um_polylang_path . 'includes/core/class-form.php';
			UM()->classes['um_polylang_form'] = new um_ext\um_polylang\core\Form();
		}
		return UM()->classes['um_polylang_form'];
	}


	/**
	 * Subclass that translates email templates.
	 *
	 * @return um_ext\um_polylang\core\Mail()
	 */
	public function mail() {
		if ( empty( UM()->classes['um_polylang_mail'] ) ) {
			require_once um_polylang_path . 'includes/core/class-mail.php';
			UM()->classes['um_polylang_mail'] = new um_ext\um_polylang\core\Mail();
		}
		return UM()->classes['um_polylang_mail'];
	}


	/**
	 * Subclass that localizes permalinks.
	 *
	 * @return um_ext\um_polylang\core\Permalinks()
	 */
	public function permalinks() {
		if ( empty( UM()->classes['um_polylang_permalinks'] ) ) {
			require_once um_polylang_path . 'includes/core/class-permalinks.php';
			UM()->classes['um_polylang_permalinks'] = new um_ext\um_polylang\core\Permalinks();
		}
		return UM()->classes['um_polylang_permalinks'];
	}


	/**
	 * Subclass that setup pages and forms.
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
	 * Loads a plugin's translated strings.
	 */
	public function textdomain() {
		$locale = get_locale() ? get_locale() : 'en_US';
		load_textdomain( um_polylang_textdomain, WP_LANG_DIR . '/plugins/' . um_polylang_textdomain . '-' . $locale . '.mo' );
		load_plugin_textdomain( um_polylang_textdomain, false, dirname( um_polylang_plugin ) . '/languages/' );
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
			$lang = substr( get_locale(), 0, 2 );
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
