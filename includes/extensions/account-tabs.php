<?php
/**
 * Integration with the "Account tabs" extension.
 *
 * @package um_ext\um_polylang\extensions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The "Create Tabs" button handler.
 */
function um_polylang_account_tabs_create() {
	$args = array(
		'fields'      => 'ids',
		'nopaging'    => true,
		'post_status' => 'publish',
		'post_type'   => 'um_account_tabs',
	);

	$posts = get_posts( $args );

	UM()->Polylang()->posts()->create_posts( $posts, 'um_account_tabs' );

	$url = add_query_arg( 'update', 'um_pll_create_account_tabs', admin_url( 'edit.php?post_type=um_account_tabs' ) );
	wp_safe_redirect( $url );
	exit;
}
add_action( 'um_admin_do_action__um_pll_create_account_tabs', 'um_polylang_account_tabs_create' );


/**
 * Update the "Embed a profile form" setting in the translated tab.
 *
 * @see um_ext\um_polylang\core\Posts::create_posts()
 *
 * @param int    $tr_id     Translated post ID.
 * @param int    $post_id   Original post ID.
 * @param string $lang      Translated language slug.
 * @param string $post_type Post type.
 */
function um_polylang_account_tabs_create_posts( $tr_id, $post_id, $lang, $post_type ) {
	if ( 'um_account_tabs' === $post_type ) {
		$um_form = get_post_meta( $post_id, '_um_form', true );
		if ( ! empty( $um_form ) ) {
			$tr_um_form = pll_get_post( $um_form, $lang );
			if ( ! empty( $tr_um_form ) ) {
				update_post_meta( $tr_id, '_um_form', $tr_um_form );
			}
		}
	}
}
add_action( 'um_polylang_create_posts', 'um_polylang_account_tabs_create_posts', 10, 4 );


/**
 * Display a notice with the "Create Tabs" button.
 *
 * @return void
 */
function um_polylang_account_tabs_notice() {
	$screen = get_current_screen();
	if ( ! is_object( $screen ) || 'edit-um_account_tabs' !== $screen->id ) {
		return;
	}

	$languages = pll_languages_list();
	if ( empty( $languages ) ) {
		return;
	}
	$def_lang = pll_default_language();

	$args = array(
		'fields'      => 'ids',
		'nopaging'    => true,
		'post_status' => 'publish',
		'post_type'   => 'um_account_tabs',
	);

	$posts = get_posts( $args );
	if ( empty( $posts ) ) {
		return;
	}

	$need_translations = array();
	foreach ( $posts as $post => $post_id ) {
		$cur_lang = pll_get_post_language( $post_id );
		if ( false === $cur_lang ) {
			pll_set_post_language( $post_id, PLL()->pref_lang );
		} elseif ( $def_lang !== $cur_lang ) {
			continue;
		}
		$post_translations = pll_get_post_translations( $post_id );
		if ( array_diff( $languages, array_keys( $post_translations ) ) ) {
			$need_translations[ $post_id ] = get_the_title( $post_id );
		}
	}

	if ( $need_translations ) {
		$url_params = array(
			'um_adm_action' => 'um_pll_create_account_tabs',
			'_wpnonce'      => wp_create_nonce( 'um_pll_create_account_tabs' ),
		);

		$url = add_query_arg( $url_params );

		ob_start();
		?>
		<p>
			<?php
			echo esc_html(
				sprintf(
					// translators: %1$s - Comma separated list of custom account tabs.
					__( 'Extension needs to create tabs for every language to function correctly. Tabs that need translation: %1$s', 'um-polylang' ),
					implode( ', ', $need_translations )
				)
			);
			?>
		</p>
		<p>
			<a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'Create Tabs', 'um-polylang' ); ?></a>
			<a href="javascript:void(0);" class="button-secondary um_secondary_dismiss"><?php esc_html_e( 'No thanks', 'um-polylang' ); ?></a>
		</p>
		<?php
		$message = ob_get_clean();

		$notice_data = array(
			'class'       => 'notice-warning',
			'message'     => $message,
			'dismissible' => true,
		);

		UM()->admin()->notices()->add_notice( 'um_pll_create_account_tabs', $notice_data, 20 );
	}
}
add_action( 'in_admin_header', 'um_polylang_account_tabs_notice' );


/**
 * Display a notice after um_adm_action.
 *
 * @param array  $messages Admin notice messages.
 * @param string $update   Update action key.
 * @return array
 */
function um_polylang_account_tabs_notice_update( $messages, $update ) {
	if ( 'um_pll_create_account_tabs' === $update ) {
		$messages[0]['content'] = __( 'Tabs have been duplicated successfully.', 'um-polylang' );
	}
	return $messages;
}
add_filter( 'um_adm_action_custom_notice_update', 'um_polylang_account_tabs_notice_update', 10, 2 );
add_filter( 'um_adm_action_custom_update_notice', 'um_polylang_account_tabs_notice_update', 10, 2 );


/**
 * Localize custom account tabs.
 *
 * @param array $tabs All custom account tabs like posts array.
 * @return array Custom account tabs for the current language. Uses default language tab if no tab for the current.
 */
function um_polylang_account_tabs_get_tabs( $tabs ) {
	if ( is_array( $tabs ) ) {
		$current = UM()->Polylang()->get_current();
		$default = UM()->Polylang()->get_default();
		foreach ( $tabs as $i => $tab ) {
			$post_id   = is_object( $tab ) ? $tab->ID : absint( $tab );
			$post_lang = pll_get_post_language( $post_id );
			if ( $post_lang === $current ) {
				continue;
			}
			if ( $post_lang === $default && ! pll_get_post( $post_id, $current ) ) {
				continue;
			}
			unset( $tabs[ $i ] );
		}
	}
	return $tabs;
}
add_filter( 'um_account_tabs_get_tabs', 'um_polylang_account_tabs_get_tabs', 10, 1 );
