=== Ultimate Member - Polylang ===

Author: umdevelopera
Author URI: https://github.com/umdevelopera
Plugin URI: https://github.com/umdevelopera/um-polylang
Tags: ultimate member, polylang, multilingual
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Requires at least: 6.5
Tested up to: 6.7.1
Requires UM core at least: 2.6.8
Tested UM core up to: 2.9.2
Stable tag: 1.2.2

== Description ==

Integrates the "Ultimate Member" plugin with the "Polylang" plugin. Makes UM multilingual.

= Key Features =

* Language switcher shortcode. Displays the Polylang language switcher where you need it.
* Localize links for Ultimate Member pages.
* Ability to duplicate Ultimate Member pages for all languages in one click.
* Ability to duplicate Ultimate Member forms for all languages in one click.
* Ability to translate email templates.
* Ability to translate bio (description) field in profile.
* Integration with the Account tabs extension. Makes custom account tabs translatable.
* Integration with the Profile tabs extension. Makes custom profile tabs translatable.

== Installation ==

Note: This plugin requires the "Ultimate Member" and "Polylang" plugins to be installed first.
Note: This plugin is designed to integrate with the "Polylang" plugin, do not use it with the "Polylang PRO" plugin.

You can install this plugin from the ZIP file as any other plugin.
Download ZIP file from GitHub or Google Drive. You can find download links here: https://github.com/umdevelopera/um-polylang
Follow this setup guide: https://wordpress.org/support/article/managing-plugins/#upload-via-wordpress-admin

= Documentation & Support =

This is a free extension created for the community. The Ultimate Member team does not provide support for this extension.
Open new issue in the GitHub repository if you are facing a problem or have a suggestion: https://github.com/umdevelopera/um-polylang/issues
Documentation is the README section in the GitHub repository: https://github.com/umdevelopera/um-polylang

== Changelog ==

= 1.2.2: February 9, 2025 =

* Enhancements:

	- Added support for a new "Custom post types and Taxonomies" logic.
	- Localized the account activation link {account_activation_link}.
	- Localized the password reset link {account_activation_link}.
	- Set a user locale by the Polylang language switcher.
	- Use a user locale (if set) to select an email language.
	- Updated the "Email notifications" table in settings.

= 1.2.1: December 18, 2024 =

* Enhancements:

	- Tweak: The core page links are localized using the `page_link` hook.
	- Tweak: The logout redirect URL is localized.

= 1.2.0: November 10, 2024 =

* Enhancements:

	- Added: Language switcher shortcode [um_pll_switcher].
	- Added: Integration with the Account tabs extension. Custom account tabs are translatable now.
	- Added: Integration with the Profile tabs extension. Custom profile tabs are translatable now.

= 1.1.1: December 24, 2023 =

	- Tweak: Displays forms/posts that need translation in the notice.
	- Tweak: Copy fields to the draft when translating a form manually.
	- Tweak: Automatically flush rewrite rules after bulk pages creation.

= 1.1.0: December 4, 2023 =

	- Added: Polylang integration for Ultimate Member forms.
	- Added: The "Create Forms" notice and button.
	- Added: The "Create Pages" notice and button.
	- Added: Translation template (.pot file).
	- Fixed: Classes autoloader issue: Class "um_ext\um_polylang\core\Fields" not found.

= 1.0.2: July 20, 2023 =

	- Fixed: Translated description field value

= 1.0.1: June 21, 2023 =

	- Added: A notice that Polylang plugin is required.
	- Tweak: Flush rewrite rules on activation.
	- Fixed: Account and profile links in the language switcher.
	- Fixed: Conflict with WooCommerce in Account.

= 1.0.0: June 14, 2023 =

	- Initial release.