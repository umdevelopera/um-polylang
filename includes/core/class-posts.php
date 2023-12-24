<?php
/**
 * Create translated posts and forms
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create translated pages and forms
 *
 * @package um_ext\um_polylang\core
 */
class Posts {

	public function __construct() {
		add_action( 'pll_save_post', array( $this, 'pll_save_post' ), 20, 3 );
	}


	/**
	 * Create pages and forms for languages.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $posts     An array of posts to duplicate.
	 * @param string $post_type Post type.
	 *
	 * @return array Information about posts.
	 */
	public function create_posts( $posts, $post_type ) {

		$languages = pll_languages_list();
		if ( empty( $languages ) || empty( $posts ) ) {
			return;
		}
		$def_lang = pll_default_language();

		$posts_translations = array();
		foreach( $posts as $post => $post_id ) {
			$cur_lang = PLL()->model->post->get_language( $post_id );
			if ( false === $cur_lang ) {
				PLL()->model->post->set_language( $post_id, PLL()->pref_lang );
			}
			if ( $def_lang !== $cur_lang->get_prop( 'slug' ) ) {
				continue;
			}

			$translations = pll_get_post_translations( $post_id );
			$untranslated = array_diff( $languages, array_keys( $translations ) );

			if ( $untranslated ) {
				$postdata = get_post( $post_id, ARRAY_A );
				$postmeta = get_post_meta( $post_id );

				foreach ( $untranslated as $lang ) {
					$tr_arr                  = $postdata;
					$tr_arr['ID']            = null;
					$tr_arr['post_date']     = null;
					$tr_arr['post_date_gmt'] = null;
					$tr_arr['post_name']     = $tr_arr['post_name'] . '-' . $lang;
					$tr_arr['post_title']    = $tr_arr['post_title'] . " ($lang)";

					// Polylang need the 'new_lang' parameter to set a proper language.
					$_GET['new_lang'] = $lang;
					$tr_id            = wp_insert_post( $tr_arr );
					unset( $_GET['new_lang'] );

					// Duplicate postmeta.
					foreach ( $postmeta as $key => $value ) {
						if ( '_um_core' === $key ) {
							continue;
						}
						$meta_value = maybe_unserialize( $value[0] );
						update_post_meta( $tr_id, $key, $meta_value );
					}
					update_post_meta( $tr_id, '_icl_lang_duplicate_of', $post_id );

					$translations[ $lang ] = $tr_id;
					do_action( 'um_polylang_create_posts', $tr_id, $post_id, $lang, $post_type );
				}
				PLL()->model->post->save_translations( $post_id, $translations );
			}

			$posts_translations[ $post ] = $translations;
		}
		UM()->rewrite()->reset_rules();

		return $posts_translations;
	}


	/**
	 * Synchronizes post fields in the page or form translation.
	 *
	 * Hook `pll_save_post` fires after the post language and translations are saved.
	 *
	 * @see \PLL_CRUD_Posts::save_post()
	 *
	 * @since 1.1.1
	 *
	 * @param int     $tr_id        Post id.
	 * @param WP_Post $post         Post object.
	 * @param int[]   $translations Post translations.
	 *
	 * @return void
	 */
	public function pll_save_post( $tr_id, $post, $translations ) {
		global $wpdb;

		if (
			is_a( $post, 'WP_Post' ) && 'auto-draft' === $post->post_status
			&& PLL()->model->post->current_user_can_synchronize( $tr_id )
			&& isset( $GLOBALS['pagenow'], $_GET['from_post'], $_GET['new_lang'] ) && 'post-new.php' === $GLOBALS['pagenow']
		) {
			check_admin_referer( 'new-post-translation' );

			$lang     = sanitize_key( $_GET['new_lang'] );
			$post_id  = absint( $_GET['from_post'] );
			$original = get_post( $post_id );

			if ( empty( $original ) ) {
				return;
			}

			if ( 'um_form' === $original->post_type
				|| ( 'page' === $original->post_type && in_array( $post_id, UM()->config()->permalinks, true ) )
			) {

				// Duplicate content.
				$wpdb->update(
					$wpdb->posts,
					array(
						'post_content' => $original->post_content,
						'post_title'   => $original->post_title . " ($lang)",
					),
					array(
						'ID' => $tr_id,
					)
				);

				// Duplicate postmeta.
				$postmeta = get_post_meta( $post_id );
				foreach ( $postmeta as $key => $value ) {
					if ( '_um_core' === $key ) {
						continue;
					}
					$meta_value = maybe_unserialize( $value[0] );
					update_post_meta( $tr_id, $key, $meta_value );
				}
				update_post_meta( $tr_id, '_icl_lang_duplicate_of', $post_id );
			}
		}
	}

}
