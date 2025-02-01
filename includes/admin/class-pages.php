<?php
namespace um_ext\um_polylang\admin;

defined( 'ABSPATH' ) || exit;

/**
 * Translate pages.
 *
 * Get an instance this way: UM()->Polylang()->admin()->pages()
 *
 * @package um_ext\um_polylang\admin
 */
class Pages {


	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Notices.
		add_action( 'in_admin_header', array( $this, 'notice_create_pages' ) );
		add_filter( 'um_adm_action_custom_notice_update', array( $this, 'notice_update' ), 10, 2 );
		add_filter( 'um_adm_action_custom_update_notice', array( $this, 'notice_update' ), 10, 2 );

		// Create Pages.
		add_action( 'um_admin_do_action__um_pll_create_pages', array( $this, 'action_create_pages' ) );
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
	 * @return array
	 */
	public function notice_update( $messages, $update ) {
		if ( 'um_pll_create_pages' === $update ) {
			$messages[0]['content'] = __( 'Pages have been duplicated successfully.', 'um-polylang' );
		}
		return $messages;
	}

}
