<?php
/**
 * Class um_ext\um_polylang\core\Setup
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

use um_ext\um_polylang\admin\PLL_Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Actions on installation.
 *
 * Get an instance this way: UM()->Polylang()->setup()
 *
 * @package um_ext\um_polylang\core
 */
class Setup {

	const NOTICES = array(
		'um_pll_create_forms',
		'um_pll_create_pages',
		'um_pll_create_account_tabs',
		'um_pll_create_profile_tabs',
	);


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
			$hidden_notices = array_diff( $hidden_notices, self::NOTICES );
			update_option( 'um_hidden_admin_notices', $hidden_notices );
		}
	}


	/**
	 * Set default Polylang settings.
	 *
	 * @since 1.2.2
	 */
	public function set_default_settings() {
		if ( is_array( PLL()->options ) ) {
			$um_post_types               = (array) apply_filters( 'um_pll_get_post_types', PLL_Settings::POST_TYPES, false );
			PLL()->options['post_types'] = empty( PLL()->options['post_types'] ) ? $um_post_types : array_merge( PLL()->options['post_types'], $um_post_types );
			update_option( 'polylang', PLL()->options );
		}
	}


	/**
	 * Run on plugin activation.
	 */
	public function run() {
		$this->flush_rewrite_rules();
		$this->reset_admin_notices();
		$this->set_default_settings();
	}
}
