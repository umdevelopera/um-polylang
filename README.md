# Ultimate Member - Polylang

Integrates the **Ultimate Member** community plugin with the **Polylang** multilingual plugin.

## Key features

- Localized permalinks for the Account and User (profile) pages.
- Ability to duplicate Ultimate Member forms for all languages in one click.
- Ability to duplicate Ultimate Member pages for all languages in one click.
- Ability to translate email templates.
- Ability to translate bio (description) field in profile.

## Installation

__Note:__ This plugin requires the [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) and [Polylang](https://uk.wordpress.org/plugins/polylang/) plugins to be installed first.

### How to install from GitHub

Open git bash, navigate to the **plugins** folder and execute this command:

`git clone --branch=main git@github.com:umdevelopera/um-polylang.git um-polylang`

Once the plugin is cloned, enter your site admin dashboard and go to _wp-admin > Plugins > Installed Plugins_. Find the "Ultimate Member - Polylang" plugin and click the "Activate" link.

### How to install from ZIP archive

You can install this plugin from the [ZIP archive](https://drive.google.com/file/d/1Lpgu5b-6CLkjK0Ik24CBB836aIcRcmaj/view) as any other plugin. Follow [this instruction](https://wordpress.org/support/article/managing-plugins/#upload-via-wordpress-admin).

## How to use

Go to *wp-admin > Pages* to translate Ultimate Member pages. Click the **Create Pages** button in the notice to duplicate Ultimate Member pages for all languages. Or click the "+" icon unter the flag to duplicate each page manually.

Image - Translate pages.
![Pages](https://github.com/umdevelopera/um-polylang/assets/113178913/40543c1b-d428-4832-9090-d2cc9166b616)

Go to *wp-admin > Ultimate Member > Forms* to translate Ultimate Member forms. Click the **Create Forms** button in the notice to duplicate Ultimate Member forms for all languages. Or click the "+" icon unter the flag to duplicate each form manually.

Once forms for languages are created you can open these forms and translate fields. You have to translate a **Label** for custom fields. You also can translate **Placeholder** and **Help Text** if needed.

Image - Translate forms.
![Forms](https://github.com/umdevelopera/um-polylang/assets/113178913/76763122-a774-4778-ab2c-748b3e983779)

Go to *wp-admin > Ultimate Member > Settings > Email* to translate email templates. Click the "+" icon unter the flag to translate a template for the language. The plugin saves translated email templates to locale subfolders in the theme, see [Email Templates](https://docs.ultimatemember.com/article/1335-email-templates).

Image - Translate emails.
![Email](https://github.com/umdevelopera/um-polylang/assets/113178913/17167ba5-8564-4fe9-ba33-ef69bfb67f57)

Go to *wp-admin > Settings > Permalinks* and click the "Save Changes" button if you need to update rewrite rules for the Account and Profile permalinks.

Image - Permalink settings.
![WP Settings, Permalink (default)](https://github.com/umdevelopera/um-polylang/assets/113178913/69be91c9-12dd-490c-9145-b163c5beb26d)

__Note:__ The "Post name" permalink structure is recommended.

## Support

This is a free extension created for the community. The Ultimate Member team does not provide support for this extension. Open new [issue](https://github.com/umdevelopera/um-polylang/issues) if you face a problem.

## Related links

Ultimate Member home page: https://ultimatemember.com/

Ultimate Member documentation: https://docs.ultimatemember.com/

Ultimate Member on wordpress.org: https://wordpress.org/plugins/ultimate-member/

Article: [How to translate plugin](https://docs.ultimatemember.com/article/1449-how-to-translate-plugin#switch).
