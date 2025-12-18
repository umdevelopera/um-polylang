<?php
/**
 * Class um_ext\um_polylang\front\Shortcodes
 *
 * @package um_ext\um_polylang\front
 */

namespace um_ext\um_polylang\front;

use PLL_Switcher;

defined( 'ABSPATH' ) || exit;

/**
 * Add shortcodes.
 *
 * Get an instance this way: UM()->Polylang()->front()->shortcodes()
 *
 * @package um_ext\um_polylang\front
 */
class Shortcodes extends PLL_Switcher {
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

		$output = $this->the_languages( PLL()->links, $args );
		if ( empty( $args['dropdown'] ) ) {
			if ( ! empty( $args['item_display'] ) ) {
				$search = '<li';
				$style  = ' style="display: ' . $args['item_display'] . ';" ';
				$output = str_replace( $search, $search . $style, $output );
			}
			$output = '<ul class="um-pll-switcher">' . $output . '</ul>';
		} else {
			if ( ! empty( $args['show_flags'] ) ) {
				$lang     = UM()->Polylang()->get_current();
				$language = PLL()->model->get_language( $lang );
				if ( is_object( $language ) ) {
					$output = $language->flag . ' ' . $output;
				}
			}
			$output = '<div class="um-pll-switcher">' . $output . '</div>';
		}

		return $output;
	}
}
