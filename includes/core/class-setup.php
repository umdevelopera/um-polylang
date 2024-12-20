<?php
/**
 * Actions on installation.
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;
use um_ext\um_polylang\admin\Admin;

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
	 * Reset hidden admin notices.
	 *
	 * @since 1.2.0
	 */
	public function reset_admin_notices() {
		$hidden_notices = get_option( 'um_hidden_admin_notices', array() );
		if ( $hidden_notices && is_array( $hidden_notices ) ) {
			$hidden_notices = array_diff( $hidden_notices, Admin::NOTICES );
			update_option( 'um_hidden_admin_notices', $hidden_notices );
		}
	}


	/**
	 * Run on plugin activation.
	 */
	public function run() {
		$this->flush_rewrite_rules();
		$this->reset_admin_notices();
	}

}
