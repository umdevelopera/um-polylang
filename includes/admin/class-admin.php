<?php
/**
 * Extends wp-admin features.
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


		/**
		 * Admin constructor.
		 */
		public function __construct() {
			add_action( 'in_admin_header', array( $this, 'create_pages_notice' ) );

			add_action( 'um_admin_do_action__um_pll_create_pages', array( $this, 'create_pages' ) );
		}


		public function create_pages() {
			UM()->Polylang()->setup()->create_pages();
			wp_safe_redirect( admin_url( 'edit.php?post_type=page' ) );
			exit;
		}


		public function create_pages_notice() {
			$screen = get_current_screen();
			if ( ! is_object( $screen ) || 'edit-page' !== $screen->id ) {
				return;
			}

			$languages = pll_languages_list();
			$pages		 = UM()->config()->permalinks;
			if ( empty( $languages ) || empty( $pages ) ) {
				return;
			}

			$need_translations = array();

			foreach ( $pages as $page => $page_id ) {
				$page_translations = pll_get_post_translations( $page_id );
				if ( array_diff( $languages, array_keys( $page_translations ) ) ) {
					$need_translations[] = $page;
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
						// translators: %s: Plugin name.
						echo wp_kses(
							sprintf( __( '%s needs to create required pages for every language to function correctly.', 'um_polylang' ), UM_PLUGIN_NAME ),
							UM()->get_allowed_html( 'admin_notice' )
						);
						?>
					</p>
					<p>
						<a href="<?php echo esc_url( $url ); ?>" class="button button-primary"><?php esc_html_e( 'Create Pages', 'um_polylang' ); ?></a>
						<a href="javascript:void(0);" class="button-secondary um_secondary_dismiss"><?php esc_html_e( 'No thanks', 'um_polylang' ); ?></a>
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
	}

}
