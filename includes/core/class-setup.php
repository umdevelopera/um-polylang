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
	 * Create pages for languages.
	 *
	 * @return array Information about pages.
	 */
	public function create_pages() {

		$languages = pll_languages_list();
		$pages		 = UM()->config()->permalinks;
		if ( empty( $languages ) || empty( $pages ) ) {
			return;
		}

		$pages_translations = array();
		foreach( $pages as $page => $page_id ) {
			$cur_lang = PLL()->model->post->get_language( $page_id );
			if ( false === $cur_lang ) {
				PLL()->model->post->set_language( $page_id, PLL()->pref_lang );
			}

			$translations = pll_get_post_translations( $page_id );
			$untranslated = array_diff( $languages, array_keys( $translations ) );

			if ( $untranslated ) {
				$postdata = get_post( $page_id, ARRAY_A );

				foreach ( $languages as $lang ) {
					if ( array_key_exists( $lang, $translations ) ) {
						$lang_post_id = $translations[ $lang ];
					} else {
						$postarr                  = $postdata;
						$postarr['ID']            = null;
						$postarr['post_date']     = null;
						$postarr['post_date_gmt'] = null;
						$postarr['post_title']    = $postarr['post_title'] . " ($lang)";

						$_GET['new_lang'] = $lang;
						$lang_post_id     = wp_insert_post( $postarr );
						unset( $_GET['new_lang'] );

						update_post_meta( $lang_post_id, '_um_wpml_' . $page, 1 );
						update_post_meta( $lang_post_id, '_icl_lang_duplicate_of', $page_id );
					}

					$translations[ $lang ] = $lang_post_id;
				}
				PLL()->model->post->save_translations( $page_id, $translations );
			}

			$pages_translations[ $page ] = $translations;
		}

		return $pages_translations;
	}


	/**
	 * Update core pages.
	 * Removes rewrite rules and then recreate rewrite rules.
	 */
	public function refresh_rewrite_rules() {
		require_once 'class-permalinks.php';
		UM()->classes['um_polylang_permalinks'] = new Permalinks();
		flush_rewrite_rules();
	}


	/**
	 * Run on plugin activation.
	 */
	public function run() {
		$this->refresh_rewrite_rules();
	}

}
