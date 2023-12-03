<?php
/**
 * Translate email templates.
 *
 * @package um_ext\um_polylang\admin
 */

namespace um_ext\um_polylang\admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translate email templates.
 *
 * @package um_ext\um_polylang\admin
 */
class Mail {


	/**
	 * Class Mail constructor.
	 */
	public function __construct() {
		// Email table.
		add_filter( 'um_email_templates_columns', array( &$this, 'email_table_columns' ), 10, 1 );
		add_filter( 'um_email_notifications', array( &$this, 'email_table_items' ), 10, 1 );

		// Email settings.
		add_filter( 'um_admin_settings_email_section_fields', array( &$this, 'admin_settings_email_section_fields' ), 10, 2 );
		add_filter( 'um_change_settings_before_save', array( &$this, 'create_email_template_file' ), 8, 1 );
	}


	/**
	 * Adding locale suffix to the "Subject Line" field.
	 *
	 * Example: change 'welcome_email_sub' to 'welcome_email_sub_de_DE'
	 *
	 * @since 1.1.0
	 *
	 * @param  array  $section_fields The email template fields.
	 * @param  string $email_key      The email template slug.
	 * @return array
	 */
	public function admin_settings_email_section_fields( $section_fields, $email_key ) {
		$locale        = UM()->Polylang()->is_default() ? '' : '_' . UM()->Polylang()->get_current( 'locale' );
		$value         = UM()->options()->get( $email_key . '_sub' . $locale );
		$value_default = UM()->options()->get( $email_key . '_sub' );

		$section_fields[2]['id']    = $email_key . '_sub' . $locale;
		$section_fields[2]['value'] = empty( $value ) ? $value_default : $value;

		return $section_fields;
	}


	/**
	 * Create email template file in the theme folder.
	 *
	 * @since 1.1.0
	 *
	 * @param  array $settings Input data.
	 * @return array
	 */
	public function create_email_template_file( $settings ) {
		if ( isset( $settings['um_email_template'] ) ) {
			$template      = $settings['um_email_template'];
			$template_path = UM()->mail()->get_template_file( 'theme', $template );

			if ( ! file_exists( $template_path ) ) {
				$template_dir = dirname( $template_path );

				if ( wp_mkdir_p( $template_dir ) ) {
					file_put_contents( $template_path, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
				}
			}
		}
		return $settings;
	}


	/**
	 * Add header for the column 'translations' in the Email table.
	 *
	 * @since 1.1.0
	 *
	 * @param  array $columns The Email table headers.
	 * @return array
	 */
	public function email_table_columns( $columns ) {

		$languages = pll_languages_list();
		if ( count( $languages ) > 0 ) {

			$flags = '';
			foreach ( $languages as $language ) {
				$language = PLL()->model->get_language( $language );
				$flag     = is_object( $language ) ? $language->flag : $language;
				$flags   .= '<span class="um-flag" style="margin:2px">' . $flag . '</span>';
			}

			$new_columns = array();
			foreach ( $columns as $column_key => $column_content ) {
				$new_columns[ $column_key ] = $column_content;
				if ( 'email' === $column_key && ! isset( $new_columns['pll_translations'] ) ) {
					$new_columns['pll_translations'] = $flags;
				}
			}

			$columns = $new_columns;
		}

		return $columns;
	}


	/**
	 * Add cell for the column 'translations' in the Email table.
	 *
	 * @since 1.1.0
	 *
	 * @param  array $email_notifications Email templates data.
	 * @return string
	 */
	public function email_table_items( $email_notifications ) {
		$languages = pll_languages_list();

		foreach ( $email_notifications as &$email_notification ) {
			$email_notification['pll_translations'] = '';
			foreach ( $languages as $language ) {
				$email_notification['pll_translations'] .= $this->email_table_link( $email_notification['key'], $language );
			}
		}

		return $email_notifications;
	}


	/**
	 * Get a link to Add/Edit email template for a certain language.
	 *
	 * @since 1.1.0
	 *
	 * @param  string $template The email template slug.
	 * @param  string $code     Slug or locale of the queried language.
	 * @return string
	 */
	public function email_table_link( $template, $code ) {

		$language = PLL()->model->get_language( $code );
		$default  = pll_default_language();
		$locale   = $code === $default ? '' : trailingslashit( $language->get_prop( 'locale' ) );

		// theme location.
		$template_path = trailingslashit( get_stylesheet_directory() . '/ultimate-member/email/' . $locale ) . $template . '.php';

		// plugin location for default language.
		if ( empty( $locale ) && ! file_exists( $template_path ) ) {
			$template_path = UM()->mail()->get_template_file( 'plugin', $template );
		}

		$link = add_query_arg(
			array(
				'email' => $template,
				'lang'  => $code,
			)
		);

		if ( file_exists( $template_path ) ) {

			// translators: %s - language name.
			$hint      = sprintf( __( 'Edit the translation in %s', 'polylang' ), $language->get_prop( 'name' ) );
			$icon_html = sprintf(
				'<a href="%1$s" title="%2$s" class="pll_icon_edit"><span class="screen-reader-text">%3$s</span></a>',
				esc_url( $link ),
				esc_html( $hint ),
				esc_html( $hint )
			);
		} else {

			// translators: %s - language name.
			$hint      = sprintf( __( 'Add a translation in %s', 'polylang' ), $language->get_prop( 'name' ) );
			$icon_html = sprintf(
				'<a href="%1$s" title="%2$s" class="pll_icon_add"><span class="screen-reader-text">%3$s</span></a>',
				esc_url( $link ),
				esc_attr( $hint ),
				esc_html( $hint )
			);
		}

		return $icon_html;
	}

}
