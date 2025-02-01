<?php
namespace um_ext\um_polylang\front;

defined( 'ABSPATH' ) || exit;

/**
 * Front-end functionality.
 *
 * Get an instance this way: UM()->Polylang()->front()
 *
 * @package um_ext\um_polylang\front
 */
class Init {


	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->fields();
		$this->form();
		$this->shortcodes();

		// Set current language cookie and update user locale.
		add_action( 'wp', array( $this, 'set_user_lang' ) );
	}


	/**
	 * Subclass that translates form fields.
	 *
	 * @return Fields
	 */
	public function fields() {
		if ( empty( UM()->classes['um_polylang_fields'] ) ) {
			require_once um_polylang_path . 'includes/front/class-fields.php';
			UM()->classes['um_polylang_fields'] = new Fields();
		}
		return UM()->classes['um_polylang_fields'];
	}


	/**
	 * Subclass that translates form.
	 *
	 * @return Form
	 */
	public function form() {
		if ( empty( UM()->classes['um_polylang_form'] ) ) {
			require_once um_polylang_path . 'includes/front/class-form.php';
			UM()->classes['um_polylang_form'] = new Form();
		}
		return UM()->classes['um_polylang_form'];
	}


	/**
	 * Subclass that add shortcodes.
	 *
	 * @since 1.2.0
	 *
	 * @return Shortcodes
	 */
	public function shortcodes() {
		if ( empty( UM()->classes['um_polylang_shortcodes'] ) ) {
			require_once um_polylang_path . 'includes/front/class-shortcodes.php';
			UM()->classes['um_polylang_shortcodes'] = new Shortcodes();
		}
		return UM()->classes['um_polylang_shortcodes'];
	}


	/**
	 * Set current language cookie and update user locale.
	 *
	 * Hook: wp - 10
	 *
	 * @since 1.2.2
	 *
	 * @global \WP_User $current_user
	 */
	public function set_user_lang() {
		if ( ! headers_sent() && isset( $_GET['lang'] ) ) {
			$lang = sanitize_key( $_GET['lang'] );
			setcookie( 'pll_language', $lang, time() + HOUR_IN_SECONDS, '/' );
		}

		/**
		 * Hook: um_polylang_update_user_locale
		 * Type: filter
		 * Description: Turn on/off updating the user locale. Default on.
		 *
		 * @since 1.2.2
		 *
		 * @param bool $update Update locale or not.
		 */
		$update = apply_filters( 'um_polylang_update_user_locale', true );
		if ( $update && is_singular() && is_user_logged_in() ) {
			global $current_user;
			$locale = UM()->Polylang()->get_current( 'locale' );
			if ( $current_user->locale !== $locale ) {
				update_user_meta( $current_user->ID, 'locale', $locale );
			}
		}
	}

}
