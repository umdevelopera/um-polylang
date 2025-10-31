<?php
/*
 * Integration with the "Profile tabs" extension.
 *
 * @package um_ext\um_polylang\extensions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The "Create Tabs" button handler.
 */
function um_polylang_profile_tabs_create() {
	$args = array(
		'fields'      => 'ids',
		'nopaging'    => true,
		'post_status' => 'publish',
		'post_type'   => 'um_profile_tabs',
	);
	$posts = get_posts( $args );

	UM()->Polylang()->posts()->create_posts( $posts, 'um_profile_tabs' );

	$url = add_query_arg( 'update', 'um_pll_create_profile_tabs', admin_url( 'edit.php?post_type=um_profile_tabs' ) );
	exit( wp_safe_redirect( $url ) );
}
add_action( 'um_admin_do_action__um_pll_create_profile_tabs', 'um_polylang_profile_tabs_create' );


/**
 * Update the "Custom Profile Form" setting in the translated tab.
 *
 * @see um_ext\um_polylang\core\Posts::create_posts()
 *
 * @param int    $tr_id     Translated post ID.
 * @param int    $post_id   Original post ID.
 * @param string $lang      Translated language slug.
 * @param string $post_type Post type.
 */
function um_polylang_profile_tabs_create_posts( $tr_id, $post_id, $lang, $post_type ) {
	if ( 'um_profile_tabs' === $post_type ) {
		$um_form = get_post_meta( $post_id, 'um_form', true );
		if ( ! empty( $um_form ) ) {
			$tr_um_form = pll_get_post( $um_form, $lang );
			if ( ! empty( $tr_um_form ) ) {
				update_post_meta( $tr_id, 'um_form', $tr_um_form );
			}
		}
	}
}
add_action( 'um_polylang_create_posts', 'um_polylang_profile_tabs_create_posts', 10, 4 );


/**
 * Display a notice with the "Create Tabs" button.
 *
 * @return void
 */
function um_polylang_profile_tabs_notice() {
	$screen = get_current_screen();
	if ( ! is_object( $screen ) || 'edit-um_profile_tabs' !== $screen->id ) {
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
		'post_type'   => 'um_profile_tabs',
	);
	$posts = get_posts( $args );
	if ( empty( $posts ) ) {
		return;
	}

	$need_translations = array();
	foreach ( $posts as $post_id ) {
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
			'um_adm_action'	 => 'um_pll_create_profile_tabs',
			'_wpnonce'			 => wp_create_nonce( 'um_pll_create_profile_tabs' ),
		);

		$url = add_query_arg( $url_params );

		ob_start();
		?>
		<p>
			<?php
			// translators: %1$s - a list of tabs.
			echo esc_html(
				sprintf(
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
			'class'				 => 'notice-warning',
			'message'			 => $message,
			'dismissible'	 => true,
		);

		UM()->admin()->notices()->add_notice( 'um_pll_create_profile_tabs', $notice_data, 20 );
	}
}
add_action( 'in_admin_header', 'um_polylang_profile_tabs_notice' );


/**
 * Display a notice after um_adm_action.
 *
 * @param array  $messages Admin notice messages.
 * @param string $update   Update action key.
 * @return array
 */
function um_polylang_profile_tabs_notice_update( $messages, $update ) {
	if ( 'um_pll_create_profile_tabs' === $update ) {
		$messages[0]['content'] = __( 'Tabs have been duplicated successfully.', 'um-polylang' );
	}
	return $messages;
}
add_filter( 'um_adm_action_custom_notice_update', 'um_polylang_profile_tabs_notice_update', 10, 2 );
add_filter( 'um_adm_action_custom_update_notice', 'um_polylang_profile_tabs_notice_update', 10, 2 );


/**
 * Add custom profile tabs.
 *
 * @param array $tabs Profile tabs.
 * @return array
 */
function um_polylang_profile_tabs_add_tabs( $tabs ) {
	$custom_tabs = um_polylang_profile_tabs_get_tabs();
	if ( $custom_tabs ) {
		foreach ( $custom_tabs as $tab ) {
			$tabs[ $tab['tabid'] ] = array(
				'ID'              => $tab['id'],
				'name'            => $tab['title'],
				'icon'            => $tab['icon'],
				'is_custom_added' => true,
			);
		}
	}
	return $tabs;
}
add_filter( 'um_profile_tabs', 'um_polylang_profile_tabs_add_tabs', 9999, 1 );
remove_filter( 'um_profile_tabs', array( um_polylang_profile_tabs_get_class_profile(), 'add_tabs' ), 9999, 1 );
remove_filter( 'um_profile_tabs', array( um_polylang_profile_tabs_get_class_profile(), 'predefine_tabs' ), 1, 1 );


/**
 * Initialize custom profile tabs before adding them to the profile menu.
 *
 * @staticvar boolean $custom_tabs
 * @return array Localized profile tabs array.
 */
function um_polylang_profile_tabs_get_tabs() {
	static $custom_tabs = false;

	if ( false === $custom_tabs ) {
		$custom_tabs = array();

		$args = array(
			'numberposts' => -1,
			'post_status' => 'publish',
			'post_type'   => 'um_profile_tabs',
		);
		$tabs = get_posts( $args );

		if ( $tabs && is_array( $tabs ) ) {

			// Localize custom profile tabs.
			$current = UM()->Polylang()->get_current();
			$default = UM()->Polylang()->get_default();
			foreach( $tabs as $i => $tab ) {
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

			// Build tabs.
			foreach ( $tabs as $tab ) {
				$slug = $tab->um_tab_slug ? $tab->um_tab_slug : $tab->post_name;
				if ( isset( $custom_tabs[ $slug ] ) ) {
					continue;
				}

				$link_type = (int) $tab->um_link_type;
				$tab_link  = ! $link_type ? '' : $tab->um_tab_link;

				$tab = array(
					'tabid'     => $slug,
					'id'        => $tab->ID,
					'title'     => $tab->post_title,
					'content'   => $tab->post_content,
					'form'      => $tab->um_form,
					'icon'      => $tab->um_icon ? $tab->um_icon : 'fas fa-check',
					'link_type' => $link_type,
					'tab_link'  => $tab_link,
				);

				$custom_tabs[ $slug ] = $tab;

				if ( empty( $tab_link ) ) {
					// Show content.
					add_action(
						"um_profile_content_{$slug}",
						function( $args ) use ( $tab ) {
							$content     = wpautop( $tab['content'] );
							$tab_content = um_convert_tags( $content, array(), false );

							if ( ! empty( $tab['form'] ) ) {
								add_filter( 'um_force_shortcode_render', array( um_polylang_profile_tabs_get_class_profile(), 'force_break_form_shortcode' ) );
							}
							if ( class_exists( '\Elementor\Plugin' ) ) {
								\Elementor\Plugin::instance()->frontend->remove_content_filter();
							}

							echo apply_filters( 'the_content', $tab_content );

							if ( class_exists( '\Elementor\Plugin' ) ) {
								\Elementor\Plugin::instance()->frontend->add_content_filter();
							}
							if ( ! empty( $tab['form'] ) ) {
								remove_filter( 'um_force_shortcode_render', array( um_polylang_profile_tabs_get_class_profile(), 'force_break_form_shortcode' ) );
								echo '<div class="um-clear"></div>';
								echo um_polylang_profile_tabs_get_class_profile()->um_custom_tab_form( $tab['tabid'], $tab['form'] );
							}
						}
					);
				} else {
					// Show remote link.
					add_filter(
						"um_profile_menu_link_{$slug}",
						function( $nav_link ) use ( $tab_link ) {
							return $tab_link;
						}
					);
					add_filter(
						"um_profile_menu_link_{$slug}_attrs",
						function( $profile_nav_attrs ) {
							return $profile_nav_attrs . ' target="_blank"';
						}
					);
				}
			}
		}
	}

	return $custom_tabs;
}


/**
 * Get class Profile.
 *
 * @since 1.2.3
 *
 * @return um_ext\um_profile_tabs\core\Profile
 */
function um_polylang_profile_tabs_get_class_profile() {
  return method_exists( UM()->Profile_Tabs(), 'profile' ) ? UM()->Profile_Tabs()->profile() : UM()->Profile_Tabs()->common()->profile();
}
