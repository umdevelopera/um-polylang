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
	 * Create posts for languages.
	 *
	 * @since 1.1.0
	 *
	 * @return array Information about posts.
	 */
	public function create_posts( $posts, $post_type ) {

		$languages = pll_languages_list();
		if ( empty( $languages ) || empty( $posts ) ) {
			return;
		}

		$posts_translations = array();
		foreach( $posts as $post => $post_id ) {
			$cur_lang = PLL()->model->post->get_language( $post_id );
			if ( false === $cur_lang ) {
				PLL()->model->post->set_language( $post_id, PLL()->pref_lang );
			}

			$translations = pll_get_post_translations( $post_id );
			$untranslated = array_diff( $languages, array_keys( $translations ) );

			if ( $untranslated ) {
				$postdata = get_post( $post_id, ARRAY_A );
				$postmeta = get_post_meta( $post_id );

				foreach ( $untranslated as $lang ) {
					$postarr                  = $postdata;
					$postarr['ID']            = null;
					$postarr['post_date']     = null;
					$postarr['post_date_gmt'] = null;
					$postarr['post_title']    = $postarr['post_title'] . " ($lang)";

					// Polylang need the 'new_lang' parameter to set a proper language.
					$_GET['new_lang'] = $lang;
					$lang_post_id     = wp_insert_post( $postarr );
					unset( $_GET['new_lang'] );

					// Duplicate postmeta.
					foreach ( $postmeta as $key => $value ) {
						if ( '_um_core' === $key ) {
							continue;
						}
						$meta_value = maybe_unserialize( $value[0] );
						update_post_meta( $lang_post_id, $key, $meta_value );
					}
					update_post_meta( $lang_post_id, '_icl_lang_duplicate_of', $post_id );

					$translations[ $lang ] = $lang_post_id;
					do_action( 'um_polylang_create_posts', $lang_post_id, $post_id, $lang, $post_type );
				}
				PLL()->model->post->save_translations( $post_id, $translations );
			}

			$posts_translations[ $post ] = $translations;
		}

		return $posts_translations;
	}


	/**
	 * Updates core pages.
	 * Removes rewrite rules and then recreate rewrite rules.
	 *
	 * @since 1.1.0
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
