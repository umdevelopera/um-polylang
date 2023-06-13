<?php
/**
	Plugin Name: Ultimate Member - Polylang
	Plugin URI:  https://github.com/umdevelopera/um-polylang
	Description: Integrates Ultimate Member with Polylang.
	Version:     1.0.0
	Author:      umdevelopera
	Author URI:  https://github.com/umdevelopera
	Text Domain: um-polylang
	Domain Path: /languages
	UM version:  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin_data = get_plugin_data( __FILE__ );

define( 'um_polylang_url', plugin_dir_url( __FILE__ ) );
define( 'um_polylang_path', plugin_dir_path( __FILE__ ) );
define( 'um_polylang_plugin', plugin_basename( __FILE__ ) );
define( 'um_polylang_extension', $plugin_data['Name'] );
define( 'um_polylang_version', $plugin_data['Version'] );
define( 'um_polylang_textdomain', 'um-polylang' );
define( 'um_polylang_requires', '2.6.2' );

// Activation script.
if ( ! function_exists( 'um_polylang_activation_hook' ) ) {
	function um_polylang_activation_hook() {
		$version = get_option( 'um_polylang_version' );
		if ( ! $version ) {
			update_option( 'um_polylang_last_version_upgrade', um_polylang_version );
		}
		if ( um_polylang_version !== $version ) {
			update_option( 'um_polylang_version', um_polylang_version );
		}
	}
}
register_activation_hook( um_polylang_plugin, 'um_polylang_activation_hook' );

// Check dependencies.
if ( ! function_exists( 'um_polylang_check_dependencies' ) ) {
	function um_polylang_check_dependencies() {
		if ( ! defined( 'um_path' ) || ! function_exists( 'UM' ) || ! UM()->dependencies()->ultimatemember_active_check() ) {
			// UM is not active.
			add_action(
				'admin_notices',
				function () {
					// translators: %s - plugin name.
					echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-polylang' ), um_polylang_extension ) ) . '</p></div>';
				}
			);
		} else {
			require_once um_polylang_path . 'includes/core/class-um-polylang.php';

			function um_polylang_init() {
				if ( function_exists( 'UM' ) ) {
					UM()->set_class( 'Polylang', true );
				}
			}
			add_action( 'plugins_loaded', 'um_polylang_init', 2, 1 );
		}
	}
}
add_action( 'plugins_loaded', 'um_polylang_check_dependencies', -20 );
