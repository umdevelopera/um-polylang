<?php
/**
 * Class um_ext\um_polylang\frontend\Shortcodes
 *
 * @package um_ext\um_polylang\frontend
 */

namespace um_ext\um_polylang\frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Add shortcodes.
 *
 * Get an instance this way: UM()->Polylang()->frontend()->shortcodes()
 *
 * @package um_ext\um_polylang\frontend
 */
class Shortcodes {
	const DEFAULTS = array(
		'dropdown'               => 0, // Display as list and not as dropdown.
		'show_flags'             => 1, // Don't show flags.
		'show_names'             => 1, // Show language names.
		'display_names_as'       => 'name', // Display the language name.
		'item_display'           => 'inline',   // CSS "display" property for list items.
		'item_spacing'           => 'preserve', // Preserve whitespace between list items.
		'force_home'             => 0, // Tries to find a translation.
		'hide_current'           => 0, // Don't hide the current language.
		'hide_if_no_translation' => 0, // Don't hide the link if there is no translation.
		'admin_render'           => 0, // Make the switcher in a frontend context.
		'echo'                   => 0, // Echoes the list.
		'raw'                    => 0, // Build the language switcher.
	);

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_shortcode( 'um_pll_switcher', array( $this, 'um_pll_switcher' ) );
	}

	/**
	 * Displays a language switcher.
	 *
	 * Shortcode: [um_pll_switcher]
	 *
	 * @param array $atts {
	 *   Optional array of arguments.
	 *
	 *   @type int    $dropdown               The list is displayed as dropdown if set, defaults to 0.
	 *   @type int    $show_flags             Displays flags if set to 1, defaults to 1.
	 *   @type int    $show_names             Shows language names if set to 1, defaults to 1.
	 *   @type string $display_names_as       Whether to display the language name or its slug, valid options are 'slug' and 'name', defaults to 'name'.
	 *   @type string $item_display           Optional CSS "display" property for list items. Valid options are 'list-item' and 'inline', defaults to 'inline'.
	 *   @type string $item_spacing           Whether to preserve or discard whitespace between list items, valid options are 'preserve' and 'discard', defaults to 'preserve'.
	 *   @type int    $hide_current           Hides the current language if set to 1, defaults to 0.
	 *   @type int    $hide_if_no_translation Hides the link if there is no translation if set to 1, defaults to 0.
	 * }
	 * @return string|array either the html markup of the switcher or the raw elements to build a custom language switcher
	 */
	public function um_pll_switcher( $atts ) {
		$args = shortcode_atts( self::DEFAULTS, $atts );

		// Map the shortcode attributes onto the switcher arguments.
		return pll_the_languages(
			array(
				'layout'                 => ! empty( $args['dropdown'] ) ? 'dropdown' : ( 'inline' === $args['item_display'] ? 'horizontal' : 'vertical' ),
				'show_flags'             => ! empty( $args['show_flags'] ),
				'show_labels'            => empty( $args['show_names'] ) ? '' : ( 'slug' === $args['display_names_as'] ? 'codes' : 'names' ),
				'preserve_spacing'       => 'discard' !== $args['item_spacing'],
				'hide_current'           => ! empty( $args['hide_current'] ),
				'hide_if_no_translation' => ! empty( $args['hide_if_no_translation'] ),
				'force_home'             => ! empty( $args['force_home'] ),
				'wrapper_classes'        => array( 'um-pll-switcher' ),
				'show_wrapper'           => true,
				'echo'                   => false,
				'raw'                    => ! empty( $args['raw'] ),
			)
		);
	}
}
