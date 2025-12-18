<?php
/**
 * Class um_ext\um_polylang\core\Init
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

defined( 'ABSPATH' ) || exit;

/**
 * Common functionality.
 *
 * Get an instance this way: UM()->Polylang()->core()
 *
 * @package um_ext\um_polylang\core
 */
class Init {


	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->mail();
		$this->permalinks();
		$this->translations();
	}


	/**
	 * Subclass that translates email templates.
	 *
	 * @return Mail
	 */
	public function mail() {
		if ( empty( UM()->classes['um_polylang_mail'] ) ) {
			require_once UM_POLYLANG_PATH . 'includes/core/class-mail.php';
			UM()->classes['um_polylang_mail'] = new Mail();
		}
		return UM()->classes['um_polylang_mail'];
	}


	/**
	 * Subclass that localizes permalinks.
	 *
	 * @return Permalinks
	 */
	public function permalinks() {
		if ( empty( UM()->classes['um_polylang_permalinks'] ) ) {
			require_once UM_POLYLANG_PATH . 'includes/core/class-permalinks.php';
			UM()->classes['um_polylang_permalinks'] = new Permalinks();
		}
		return UM()->classes['um_polylang_permalinks'];
	}


	/**
	 * Subclass that translates fields.
	 *
	 * @return Translations
	 */
	public function translations() {
		if ( empty( UM()->classes['um_polylang_translations'] ) ) {
			require_once UM_POLYLANG_PATH . 'includes/core/class-translations.php';
			UM()->classes['um_polylang_translations'] = new Translations();
		}
		return UM()->classes['um_polylang_translations'];
	}
}
