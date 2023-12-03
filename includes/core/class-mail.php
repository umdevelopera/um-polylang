<?php
/**
 * Translate email templates.
 *
 * @package um_ext\um_polylang\core
 */

namespace um_ext\um_polylang\core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translate email templates.
 *
 * @package um_ext\um_polylang\core
 */
class Mail {


	/**
	 * Class Mail constructor.
	 */
	public function __construct() {

		add_filter( 'um_email_send_subject', array( &$this, 'localize_email_subject' ), 10, 2 );
		add_filter( 'um_change_email_template_file', array( &$this, 'change_email_template_file' ), 10, 1 );
		add_filter( 'um_locate_email_template', array( &$this, 'locate_email_template' ), 10, 2 );
	}


	/**
	 * Change email template for searching in the theme folder.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template The email template slug.
	 * @return string
	 */
	public function change_email_template_file( $template ) {
		if ( ! UM()->Polylang()->is_default() ) {
			$template = UM()->Polylang()->get_current( 'locale' ) . '/' . $template;
		}
		return $template;
	}


	/**
	 * Replace email Subject with translated value on email send.
	 * Example: change 'welcome_email_sub' to 'welcome_email_sub_de_DE'
	 *
	 * @since 1.0.0
	 *
	 * @param  string $subject  Default subject.
	 * @param  string $template The email template slug.
	 * @return string
	 */
	public function localize_email_subject( $subject, $template ) {
		$locale        = UM()->Polylang()->is_default() ? '' : '_' . UM()->Polylang()->get_current( 'locale' );
		$value         = UM()->options()->get( $template . '_sub' . $locale );
		$value_default = UM()->options()->get( $template . '_sub' );
		return empty( $value ) ? $value_default : $value;
	}


	/**
	 * Change email template path.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template      The email template path.
	 * @param  string $template_name The email template slug.
	 * @return string
	 */
	public function locate_email_template( $template, $template_name ) {
		$blog_id = is_multisite() ? trailingslashit( get_current_blog_id() ) : '';
		$locale  = UM()->Polylang()->is_default() ? '' : trailingslashit( UM()->Polylang()->get_current( 'locale' ) );

		// check if there is a template in the theme folder.
		$template = locate_template(
			array(
				trailingslashit( 'ultimate-member/email' ) . $blog_id . $locale . $template_name . '.php',
				trailingslashit( 'ultimate-member/email' ) . $blog_id . $template_name . '.php',
				trailingslashit( 'ultimate-member/email' ) . $template_name . '.php',
			)
		);

		// if there isn't template at theme folder get template file from plugin dir.
		if ( ! $template ) {
			$path     = empty( UM()->mail()->path_by_slug[ $template_name ] ) ? um_path . 'templates/email' : UM()->mail()->path_by_slug[ $template_name ];
			$template = trailingslashit( $path ) . $template_name . '.php';
		}

		return wp_normalize_path( $template );
	}

}
