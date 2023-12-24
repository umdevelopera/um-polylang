# Ultimate Member - Polylang

Integrates the **Ultimate Member** plugin with the **Polylang** plugin. Makes Ultimate Member multilingual.

## Key features

- Localized permalinks for the Account and User (profile) pages.
- Ability to duplicate Ultimate Member forms for all languages in one click.
- Ability to duplicate Ultimate Member pages for all languages in one click.
- Ability to translate email templates.
- Ability to translate bio (description) field in profile.

## Installation

__Note:__ This plugin requires the [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) and [Polylang](https://wordpress.org/plugins/polylang/) plugins to be installed first.

### How to install from GitHub

Open git bash, navigate to the **plugins** folder and execute this command:

`git clone --branch=main git@github.com:umdevelopera/um-polylang.git um-polylang`

Once the plugin is cloned, enter your site admin dashboard and go to _wp-admin > Plugins > Installed Plugins_. Find the **Ultimate Member - Polylang** plugin and click the **Activate** link.

### How to install from ZIP archive

You can install this plugin from the [ZIP archive](https://drive.google.com/file/d/17n3Kr05_B_edsGY7An7xu85TZ7H5gbOT/view?usp=sharing) as any other plugin. Follow [this instruction](https://wordpress.org/support/article/managing-plugins/#upload-via-wordpress-admin).

## How to use

### How to translate pages

Go to *wp-admin > Pages* to translate Ultimate Member pages. Click the **Create Pages** button in the notice to duplicate Ultimate Member pages for all languages. Or click the "+" icon unter the flag to duplicate each page manually.

Image - Translate pages.
![Pages](https://github.com/umdevelopera/um-polylang/assets/113178913/40543c1b-d428-4832-9090-d2cc9166b616)

Go to *wp-admin > Settings > Permalinks* and click the **Save Changes** button if you need to update rewrite rules for the Account and User page permalinks.
Note: The "Post name" permalink structure is recommended.

Image - Permalink settings.
![WP Settings, Permalink (default)](https://github.com/umdevelopera/um-polylang/assets/113178913/69be91c9-12dd-490c-9145-b163c5beb26d)

### How to translate forms

Go to *wp-admin > Ultimate Member > Forms* to translate Ultimate Member forms. Click the **Create Forms** button in the notice to duplicate Ultimate Member forms for all languages. Or click the "+" icon unter the flag to duplicate each form manually.

Image - Translate forms.
![Forms](https://github.com/umdevelopera/um-polylang/assets/113178913/76763122-a774-4778-ab2c-748b3e983779)

Once forms for languages are created you can open these forms and translate fields. You have to translate a **Label** for custom fields. You also can translate **Placeholder** and **Help Text** if needed.

**Choices** are not translatable, this is necessary for the directory search to work correctly. Don't try to translate choices in the field settings! You can use custom functions to translate choices.
See examples:
- [How to translate choices of the Checkbox field](https://gist.github.com/umdevelopera/f7b0e07d5db870c9ce9fc1e513224e45)
- [How to translate choices of the Dropdown field](https://gist.github.com/umdevelopera/bcc8c882ead5914845b489ece73b612d)

![UM Forms, Edit Form, Edit Field - Dropdown (use Choices Callback to translate values)+](https://github.com/umdevelopera/um-polylang/assets/113178913/4e58118e-a9b4-430a-ba02-cb766ec72c6a)

### How to translate E-mails

Go to *wp-admin > Ultimate Member > Settings > Email* to translate email templates. Click the "+" icon unter the flag to translate a template for the language. The plugin saves translated email templates to locale subfolders in the theme, see [Email Templates](https://docs.ultimatemember.com/article/1335-email-templates).

Image - Translate emails.
![Email](https://github.com/umdevelopera/um-polylang/assets/113178913/17167ba5-8564-4fe9-ba33-ef69bfb67f57)

## Support

This is a free extension created for the community. The Ultimate Member team does not provide support for this extension.
Open new [issue](https://github.com/umdevelopera/um-polylang/issues) if you are facing a problem or have a suggestion.

## Related links

Ultimate Member home page: https://ultimatemember.com/

Ultimate Member documentation: https://docs.ultimatemember.com/

Ultimate Member on wordpress.org: https://wordpress.org/plugins/ultimate-member/

Article: [How to translate plugin](https://docs.ultimatemember.com/article/1449-how-to-translate-plugin#switch).
