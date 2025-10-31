<?php
/**
 * Plugin Name: Ultimate Member - Polylang
 * Plugin URI:  https://github.com/umdevelopera/um-polylang
 * Description: Integrates Ultimate Member with Polylang.
 * Author:      umdevelopera
 * Author URI:  https://github.com/umdevelopera
 * Text Domain: um-polylang
 * Domain Path: /languages
 *
 * Requires Plugins: ultimate-member, polylang
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * UM version: 2.10.6
 * Version: 1.2.3-alpha
 *
 * @package um_ext\um_polylang
 */

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin_data = get_plugin_data( __FILE__, true, false );

define( 'um_polylang_url', plugin_dir_url( __FILE__ ) );
define( 'um_polylang_path', plugin_dir_path( __FILE__ ) );
define( 'um_polylang_plugin', plugin_basename( __FILE__ ) );
define( 'um_polylang_extension', $plugin_data['Name'] );
define( 'um_polylang_version', $plugin_data['Version'] );
define( 'um_polylang_textdomain', 'um-polylang' );


// Activation script.
if ( ! function_exists( 'um_polylang_activation_hook' ) ) {
	function um_polylang_activation_hook() {
		if ( function_exists( 'UM' ) && function_exists( 'pll_languages_list' ) ) {
			require_once 'includes/admin/class-pll.php';
			require_once 'includes/core/class-setup.php';
			if ( class_exists( 'um_ext\um_polylang\core\Setup' ) ) {
				$setup = new um_ext\um_polylang\core\Setup();
				$setup->run();
			}
		}
	}
}
register_activation_hook( um_polylang_plugin, 'um_polylang_activation_hook' );


// Check dependencies.
if ( ! function_exists( 'um_polylang_check_dependencies' ) ) {
	function um_polylang_check_dependencies() {
		if ( ! defined( 'um_path' ) || ! function_exists( 'UM' ) || ! UM()->dependencies()->ultimatemember_active_check() ) {
			// Ultimate Member is not active.
			add_action(
				'admin_notices',
				function () {
					// translators: %s - plugin name.
					echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-polylang' ), um_polylang_extension ) ) . '</p></div>';
				}
			);
		} elseif ( ! defined( 'POLYLANG_VERSION' ) ) {
			// Polylang is not active.
			add_action(
				'admin_notices',
				function () {
					// translators: %s - plugin name.
					echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'The <strong>%s</strong> extension requires the Polylang plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/polylang/">here</a>', 'um-polylang' ), um_polylang_extension ) ) . '</p></div>';
				}
			);
		} else {
			require_once 'includes/class-um-polylang.php';
			UM()->set_class( 'Polylang', true );
		}
	}
}
add_action( 'init', 'um_polylang_check_dependencies', 1 );
