<?php
namespace um_ext\um_polylang\admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin features.
 *
 * Get an instance this way: UM()->Polylang()->admin()
 *
 * @package um_ext\um_polylang\admin
 */
class Init {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Translate forms.
		$this->forms();

		// Settings Email tab.
		$this->mail();

		// Translate pages.
		$this->pages();

		// Polylang settings.
		$this->pll_settings();

		// scripts & styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}


	/**
	 * Admin scripts & styles.
	 */
	public function enqueue() {

		wp_register_style(
			'um_polylang_admin',
			um_polylang_url . 'assets/css/um-polylang-admin.css',
			array(),
			um_polylang_version
		);

		$screen = get_current_screen();
		if ( ! is_object( $screen ) || 'edit-um_form' === $screen->id ) {
			wp_enqueue_style( 'um_polylang_admin' );
		}
	}


	/**
	 * Translate forms.
	 *
	 * @return Forms
	 */
	public function forms() {
		if ( empty( UM()->classes['um_polylang_admin_forms'] ) ) {
			require_once 'class-forms.php';
			UM()->classes['um_polylang_admin_forms'] = new Forms();
		}
		return UM()->classes['um_polylang_admin_forms'];
	}


	/**
	 * Extend settings Email tab.
	 *
	 * @return Mail
	 */
	public function mail() {
		if ( empty( UM()->classes['um_polylang_admin_mail'] ) ) {
			require_once 'class-mail.php';
			UM()->classes['um_polylang_admin_mail'] = new Mail();
		}
		return UM()->classes['um_polylang_admin_mail'];
	}


	/**
	 * Translate pages.
	 *
	 * @return Pages
	 */
	public function pages() {
		if ( empty( UM()->classes['um_polylang_admin_pages'] ) ) {
			require_once 'class-pages.php';
			UM()->classes['um_polylang_admin_pages'] = new Pages();
		}
		return UM()->classes['um_polylang_admin_pages'];
	}


	/**
	 * Polylang settings.
	 *
	 * @return PLL_Settings
	 */
	public function pll_settings() {
		if ( empty( UM()->classes['um_polylang_admin_pll_settings'] ) ) {
			require_once 'class-pll.php';
			UM()->classes['um_polylang_admin_pll_settings'] = new PLL_Settings();
		}
		return UM()->classes['um_polylang_admin_pll_settings'];
	}

}
