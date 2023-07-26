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
		if ( $this->is_active() ) {
			$this->fields();
			$this->form();
			$this->mail();
			$this->permalinks();
		}
	}


	/**
	 * Subclass that translates form fields.
	 *
	 * @return um_ext\um_polylang\core\Fields()
	 */
	public function fields() {
		if ( empty( UM()->classes['um_polylang_fields'] ) ) {
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
			UM()->classes['um_polylang_permalinks'] = new um_ext\um_polylang\core\Permalinks();
		}
		return UM()->classes['um_polylang_permalinks'];
	}


	/**
	 * Returns the current language.
	 *
	 * @since 1.0.0
	 *
	 * @global object $polylang The Polylang instance.
	 * @param  string $field Optional, the language field to return (@see PLL_Language), defaults to `'slug'`.
	 * @return string|int|bool|string[]|PLL_Language The requested field or object for the current language, `false` if the field isn't set.
	 */
	public function get_current( $field = 'slug' ) {
		global $polylang;

		$lang = pll_current_language();
		if ( isset( $_GET['lang'] ) ) {
			$lang = sanitize_key( $_GET['lang'] );
		}
		if ( empty( $lang ) || 'all' === $lang ) {
			$lang = substr( get_locale(), 0, 2 );
		}
		$language = $polylang->model->get_language( $lang );

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
	 * @since 1.0.3
	 *
	 * @return array
	 */
	public function get_languages_list() {
		return pll_languages_list();
	}


	/**
	 * Check if Polylang is active.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_active() {
		if ( defined( 'POLYLANG_VERSION' ) ) {
			global $polylang;
			return isset( $polylang ) && is_object( $polylang );
		}
		return false;
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
