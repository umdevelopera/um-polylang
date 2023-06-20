# Ultimate Member - Polylang
Integrates the **Ultimate Member** community plugin with the **Polylang** multilingual plugin.

__Note:__ This is a free extension created for the community. The Ultimate Member team does not provide any support for this extension.

## Key features
- Ability to translate email templates.
- Ability to translate bio (description) field in profile.
- Proper permalinks for the Account and User (profile) pages.

## Installation

__Note:__ This plugin requires the [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) and [Polylang](https://uk.wordpress.org/plugins/polylang/) plugins to be installed first.

### Clone from GitHub
Open git bash, navigate to the **plugins** folder and execute this command:

`git clone --branch=main git@github.com:umdevelopera/um-polylang.git um-polylang`

Once the plugin is cloned, enter your site admin dashboard and go to _wp-admin > Plugins > Installed Plugins_. Find the "Ultimate Member - Polylang" plugin and click the "Activate" link.

### Install from ZIP archive
You can install this plugin from the [ZIP archive](https://drive.google.com/file/d/1pP84L2syUXzNKlwzBSKVBqdmBsPtge--/view?usp=sharing) as any other plugin. Follow [this instruction](https://wordpress.org/support/article/managing-plugins/#upload-via-wordpress-admin).

## How to use
Go to *wp-admin > Ultimate Member > Settings > Email* to translate email templates. Click the "+" icon unter the flag to translate a template for the needed language. The plugin saves translated email templates to locale subfolders in the theme. See details [here](https://docs.ultimatemember.com/article/1335-email-templates).

Go to *wp-admin > Pages* to translate pages Account, Login, Members, Password Reset, Register, User. Click the "+" icon unter the flag to translate a page for the needed language. See details [here](https://docs.ultimatemember.com/article/1449-how-to-translate-plugin#forms).

Go to *wp-admin > Settings > Permalinks* and click the "Save Changes" button to update rewrite rules for the Account and Profile permalinks.

__Note:__ The "Post name" permalink structure is recommended.

### Screenshots:

Image - Translate email templates.
![UM Settings, Email (polylang)](https://github.com/umdevelopera/um-polylang/assets/113178913/65d14995-257d-4311-a93a-8f944ea12ba9)

Image - Translate pages.
![WP Pages (polylang)](https://github.com/umdevelopera/um-polylang/assets/113178913/1329f025-a464-4c52-bf9f-99261fb5e242)

Image - Permalink settings.
![WP Settings, Permalink (default)](https://github.com/umdevelopera/um-polylang/assets/113178913/69be91c9-12dd-490c-9145-b163c5beb26d)

## Related links:
Ultimate Member home page: https://ultimatemember.com/

Ultimate Member documentation: https://docs.ultimatemember.com/

Ultimate Member on wordpress.org: https://wordpress.org/plugins/ultimate-member/

Article: [How to translate plugin](https://docs.ultimatemember.com/article/1449-how-to-translate-plugin#switch)
