<?php
/**
 * Admin features.
 *
 * @package um_ext\um_polylang\admin
 */

namespace um_ext\um_polylang\admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'um_ext\um_polylang\admin\Admin' ) ) {


	/**
	 * Class Admin.
	 *
	 * @package um_ext\um_polylang\admin
	 */
	class Admin {

		const NOTICES = array(
			'um_pll_create_forms',
			'um_pll_create_pages',
			'um_pll_create_account_tabs',
			'um_pll_create_profile_tabs',
		);

		const POST_TYPES = array(
			'um_form'         => 'um_form',
			'um_account_tabs' => 'um_account_tabs',
			'um_profile_tabs' => 'um_profile_tabs',
		);

		/**
		 * Admin constructor.
		 */
		public function __construct() {
			// Notices.
			add_action( 'in_admin_header', array( $this, 'notice_create_forms' ) );
			add_action( 'in_admin_header', array( $this, 'notice_create_pages' ) );
			add_filter( 'um_adm_action_custom_notice_update', array( $this, 'notice_update' ), 10, 2 );
			add_filter( 'um_adm_action_custom_update_notice', array( $this, 'notice_update' ), 10, 2 );

			// Create Forms.
			add_action( 'um_admin_do_action__um_pll_create_forms', array( $this, 'action_create_forms' ) );

			// Create Pages.
			add_action( 'um_admin_do_action__um_pll_create_pages', array( $this, 'action_create_pages' ) );

			// Translatable post types.
			add_filter( 'pll_get_post_types', array( $this, 'pll_get_post_types' ), 10, 2 );

			// Settings, Email tab.
			$this->settings_email_tab();

			// Forms table styles.
			add_action( 'admin_footer', array( $this, 'styles' ) );
		}


		/**
		 * The "Create Forms" button handler.
		 */
		public function action_create_forms() {
			$args = array(
				'fields'      => 'ids',
				'nopaging'    => true,
				'post_status' => 'publish',
				'post_type'   => 'um_form',
			);
			$posts = get_posts( $args );

			UM()->Polylang()->posts()->create_posts( $posts, 'um_form' );

			$url = add_query_arg( 'update', 'um_pll_create_forms', admin_url( 'edit.php?post_type=um_form' ) );
			exit( wp_safe_redirect( $url ) );
		}


		/**
		 * The "Create Pages" button handler.
		 */
		public function action_create_pages() {
			$posts = UM()->config()->permalinks;

			UM()->Polylang()->posts()->create_posts( $posts, 'page' );

			$url = add_query_arg( 'update', 'um_pll_create_pages', admin_url( 'edit.php?post_type=page' ) );
			exit( wp_safe_redirect( $url ) );
		}


		/**
		 * Display a notice with the "Create Forms" button.
		 *
		 * @return void
		 */
		public function notice_create_forms() {
			$screen = get_current_screen();
			if ( ! is_object( $screen ) || 'edit-um_form' !== $screen->id ) {
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
				'post_type'   => 'um_form',
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
					'um_adm_action'	 => 'um_pll_create_forms',
					'_wpnonce'			 => wp_create_nonce( 'um_pll_create_forms' ),
				);

				$url = add_query_arg( $url_params );

				ob_start();
				?>
				<p>
					<?php
					// translators: %1$s - plugin name, %2$s - a list of forms.
					echo esc_html(
						sprintf(
							__( '%1$s needs to create required forms for every language to function correctly. Forms that need translation: %2$s', 'um-polylang' ),
							UM_PLUGIN_NAME,
							implode( ', ', $need_translations )
						)
					);
					?>
				</p>
				<p>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'Create Forms', 'um-polylang' ); ?></a>
					<a href="javascript:void(0);" class="button-secondary um_secondary_dismiss"><?php esc_html_e( 'No thanks', 'um-polylang' ); ?></a>
				</p>
				<?php
				$message = ob_get_clean();

				$notice_data = array(
					'class'				 => 'notice-warning',
					'message'			 => $message,
					'dismissible'	 => true,
				);

				UM()->admin()->notices()->add_notice( 'um_pll_create_forms', $notice_data, 20 );
			}
		}


		/**
		 * Display a notice with the "Create Pages" button.
		 *
		 * @return void
		 */
		public function notice_create_pages() {
			$screen = get_current_screen();
			if ( ! is_object( $screen ) || 'edit-page' !== $screen->id ) {
				return;
			}

			$languages = pll_languages_list();
			if ( empty( $languages ) ) {
				return;
			}
			$def_lang = pll_default_language();

			$posts = UM()->config()->permalinks;
			if ( empty( $posts ) ) {
				return;
			}

			$need_translations = array();
			foreach ( $posts as $post => $post_id ) {
				if ( $def_lang !== pll_get_post_language( $post_id ) ) {
					continue;
				}
				$post_translations = pll_get_post_translations( $post_id );
				if ( array_diff( $languages, array_keys( $post_translations ) ) ) {
					$need_translations[ $post_id ] = get_the_title( $post_id );
				}
			}

			if ( $need_translations ) {
				$url_params = array(
					'um_adm_action'	 => 'um_pll_create_pages',
					'_wpnonce'			 => wp_create_nonce( 'um_pll_create_pages' ),
				);

				$url = add_query_arg( $url_params );

				ob_start();
				?>
				<p>
					<?php
					// translators: %1$s - plugin name, %2$s - a list of pages.
					echo esc_html(
						sprintf(
							__( '%1$s needs to create required pages for every language to function correctly. Pages that need translation: %2$s', 'um-polylang' ),
							UM_PLUGIN_NAME,
							implode( ', ', $need_translations )
						)
					);
					?>
				</p>
				<p>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'Create Pages', 'um-polylang' ); ?></a>
					<a href="javascript:void(0);" class="button-secondary um_secondary_dismiss"><?php esc_html_e( 'No thanks', 'um-polylang' ); ?></a>
				</p>
				<?php
				$message = ob_get_clean();

				$notice_data = array(
					'class'				 => 'notice-warning',
					'message'			 => $message,
					'dismissible'	 => true,
				);

				UM()->admin()->notices()->add_notice( 'um_pll_create_pages', $notice_data, 20 );
			}
		}


		/**
		 * Display a notice after um_adm_action.
		 *
		 * @param array  $messages Admin notice messages.
		 * @param string $update   Update action key.
		 *
		 * @return array
		 */
		public function notice_update( $messages, $update ) {

			switch ( $update ) {

				case 'um_pll_create_forms':
					$messages[0]['content'] = __( 'Forms have been duplicated successfully.', 'um-polylang' );
					break;

				case 'um_pll_create_pages':
					$messages[0]['content'] = __( 'Pages have been duplicated successfully.', 'um-polylang' );
					break;
			}

			return $messages;
		}


		/**
		 * Filters the list of post types available for translation.
		 *
		 * @param string[] $post_types  List of post type names (as array keys and values).
		 * @param bool     $is_settings True when displaying the list of custom post types in Polylang settings.
		 *
		 * @return string[] List of post type names.
		 */
		public function pll_get_post_types( $post_types, $is_settings ) {
			$um_post_types = (array) apply_filters( 'um_pll_get_post_types', self::POST_TYPES, $is_settings );
			return array_merge( $post_types, $um_post_types );
		}


		/**
		 * Extend settings Email tab.
		 *
		 * @return um_ext\um_polylang\admin\Mail()
		 */
		public function settings_email_tab() {
			if ( empty( UM()->classes['um_polylang_admin_mail'] ) ) {
				require_once 'class-mail.php';
				UM()->classes['um_polylang_admin_mail'] = new Mail();
			}
			return UM()->classes['um_polylang_admin_mail'];
		}


		/**
		 * Fix column width in the "Forms" table.
		 */
		public function styles() {
			$screen = get_current_screen();
			if ( ! is_object( $screen ) || 'edit-um_form' === $screen->id ) {
				?>
<style type="text/css">
	@media screen and (max-width: 1200px) {
		.um-admin.post-type-um_form tr > * { padding-left: 5px; padding-right: 5px; }
		.um-admin.post-type-um_form .manage-column.column-title { width: 180px; }
		.um-admin.post-type-um_form .manage-column.column-id { width: 40px; }
		.um-admin.post-type-um_form .manage-column.column-mode { width: 60px; }
		.um-admin.post-type-um_form .manage-column.column-is_default { width: 50px; }
		.um-admin.post-type-um_form .manage-column.column-shortcode { width: 120px; }
	}
</style><?php
			}
		}
	}

}
