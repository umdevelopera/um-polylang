<?php
/**
 * Actions on installation.
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup extension
 *
 * @package um_ext\um_polylang\core
 */
class Setup {

	/**
	 * Updates core pages.
	 * Removes rewrite rules and then recreate rewrite rules.
	 *
	 * @since 1.1.0
	 */
	public function flush_rewrite_rules() {
		require_once 'class-permalinks.php';
		UM()->classes['um_polylang_permalinks'] = new Permalinks();
		flush_rewrite_rules();
	}


	/**
	 * Run on plugin activation.
	 */
	public function run() {
		$this->flush_rewrite_rules();
	}

}
