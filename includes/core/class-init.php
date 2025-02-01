<?php
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
	}


	/**
	 * Subclass that translates email templates.
	 *
	 * @return Mail
	 */
	public function mail() {
		if ( empty( UM()->classes['um_polylang_mail'] ) ) {
			require_once um_polylang_path . 'includes/core/class-mail.php';
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
			require_once um_polylang_path . 'includes/core/class-permalinks.php';
			UM()->classes['um_polylang_permalinks'] = new Permalinks();
		}
		return UM()->classes['um_polylang_permalinks'];
	}

}
