# Ultimate Member - Polylang

Integrates the **Ultimate Member** plugin with the **Polylang** plugin. Makes Ultimate Member multilingual.

## Key features

- Localize links for Ultimate Member pages.
- Ability to duplicate Ultimate Member pages for all languages in one click.
- Ability to duplicate Ultimate Member forms for all languages in one click.
- Ability to translate the field "Label", "Help Text" and "Edit Choices" using the String Translation feature.
- Ability to translate bio (description) in profile.
- Ability to translate email templates.
- Language switcher shortcode. Display the Polylang language switcher where you need it.
- Integration with the [Account tabs](https://github.com/umdevelopera/um-account-tabs) extension. Makes custom account tabs translatable.
- Integration with the [Profile tabs](https://ultimatemember.com/extensions/profile-tabs/) extension. Makes custom profile tabs translatable.

## Installation

__Note:__ This plugin requires the [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) and [Polylang](https://wordpress.org/plugins/polylang/) plugins to be installed first.

### How to install from GitHub

Open git bash, navigate to the **plugins** folder and execute this command:

`git clone --branch=main git@github.com:umdevelopera/um-polylang.git um-polylang`

Once the plugin is cloned, enter your site admin dashboard and go to _wp-admin > Plugins > Installed Plugins_. Find the **Ultimate Member - Polylang** plugin and click the **Activate** link.

### How to install from ZIP archive

You can install the plugin from this [ZIP file](https://drive.google.com/file/d/1K3Ac3KGAtJ_HrGtZ1MpeIMaqOm3HSZlq/view?usp=sharing) as any other plugin. Follow [this instruction](https://wordpress.org/support/article/managing-plugins/#upload-via-wordpress-admin).

## How to use

### Settings

Go to *wp-admin > Settings > Permalinks* to verify permalink structure. The **Post name** permalink structure is recommended.

![WP Settings, Permalink (default)](https://github.com/umdevelopera/um-polylang/assets/113178913/69be91c9-12dd-490c-9145-b163c5beb26d)

Verivy settings on *wp-admin > Languages > Settings*. See recommended settings below:

![URL modifications](https://github.com/user-attachments/assets/4e5a7627-52f1-42d9-be15-fe524f46e24e)
![Custom post types and Taxonomies](https://github.com/user-attachments/assets/3f660a0c-57b7-41dd-96c6-b95cf9cd19ac)

### How to translate pages

Go to *wp-admin > Pages* to translate Ultimate Member pages. Click the **Create Pages** button in the notice to duplicate Ultimate Member pages for all languages. Or click the "+" icon unter the flag to duplicate each page manually.

Once pages for languages are created you can open and translate them. Note that translated pages **Login**, **Registration**, **User** should contain translated forms. The form language should match the page language.

![WP Pages cr](https://github.com/user-attachments/assets/ef991008-2d5f-4dd7-9514-a5fd0d256dc0)

### How to translate forms

Go to *wp-admin > Ultimate Member > Forms* to translate Ultimate Member forms. Click the **Create Forms** button in the notice to duplicate Ultimate Member forms for all languages.

![UM Forms cr](https://github.com/user-attachments/assets/a6057994-ffd9-41d4-ac47-2436550732ff)

Once forms for languages are created you can open these forms and translate fields. You can translate a **Label**, **Placeholder** and **Help Text** if needed.

<img width="1014" height="897" alt="UM Forms, Edit Form, Edit Field  Label, Help Text, Placeholder" src="https://github.com/user-attachments/assets/28faf36b-ec3c-494b-8e11-44b9c84f3b45" />

#### How to translate field options

Options in the Checkbox, Radio, Dropdown and Multi-Select fields can not be translated in the form builder.
**Don't change choices in the field settings!**
You can use the [String Translation](https://polylang.pro/documentation/support/guides/strings-translation/) feature to translate the field  **Label**, **Help Text** and **Edit Choices** if needed.

**Example:** The field settings
<img width="1015" height="895" alt="UM Forms, Edit field " src="https://github.com/user-attachments/assets/08ea10d6-599d-4ba3-b2e8-95078b225d47" />

**Example:** The "Translations" table on *wp-admin > Languages > Translations*
<img width="1680" height="1035" alt="WP, Languages, Translations" src="https://github.com/user-attachments/assets/30264d38-0097-4b13-9d9d-547c711570f8" />

### How to translate E-mails

Go to *wp-admin > Ultimate Member > Settings > Email* to translate email templates. Click the "+" icon unter the flag to translate a template for the language. The plugin saves translated email templates to locale subfolders in the theme, see [Email Templates](https://docs.ultimatemember.com/article/1335-email-templates).

![UM Settings, Email cr](https://github.com/user-attachments/assets/47901a64-ea93-4bdd-b70c-47f0dd3fea08)

### How to translate custom account tabs

This feature is available if you use the [Account tabs](https://github.com/umdevelopera/um-account-tabs) extension.

Go to *wp-admin > Ultimate Member > Account Tabs*. Click the **Create Tabs** button in the notice to duplicate tabs for all languages. Once the tabs are duplicated for each language, you can edit the tabs for additional languages.

![WP, Ultimate Member, Account Tabs (Create Tabs) cr](https://github.com/user-attachments/assets/bd7e22ae-ca3a-4cd4-96d8-6a9e339b1a33)

### How to translate custom profile tabs

This feature is available if you use the [Profile tabs](https://ultimatemember.com/extensions/profile-tabs/) extension.

Go to *wp-admin > Ultimate Member > Profile Tabs*. Click the **Create Tabs** button in the notice to duplicate tabs for all languages. Once the tabs are duplicated for each language, you can edit the tabs for additional languages.

![WP, Ultimate Member, ProfileTabs (Create Tabs) cr](https://github.com/user-attachments/assets/5db162cd-9c53-4d7f-8081-63f8e31d8105)

### How to display language switcher

Add language switcher to the header menu if your theme supports classic menus, see [Add a language switcher in a menu](https://polylang.pro/doc/the-language-switcher/#ls-in-menu).

Add the **[um_pll_switcher]** shortcode to the header template if your theme does not support classic menus.

Shortcode attributes:
- int    `dropdown`               Displays languages into a dropdown if set to 1. Defaults to 0.
- int    `show_flags`             Displays flags if set to 1. Defaults to 1.
- int    `show_names`             Shows language names if set to 1. Defaults to 1.
- string `display_names_as`       Whether to display the language name or its slug. Accepts 'slug' and 'name'. Defaults to 'name'.
- string `item_display`           Whether to display languages as a list or inline. Accepts 'list-item' and 'inline'. Defaults to 'inline'.
- string `item_spacing`           Whether to preserve or discard whitespace between list items. Accepts 'preserve' and 'discard'. Defaults to 'preserve'.
- int    `hide_current`           Hides the current language if set to 1. Defaults to 0.
- int    `hide_if_no_translation` Hides the link if there is no translation if set to 1. Defaults to 0.

**Screenshots**

Image - Language switcher shortcode in the header template.
![Language switcher shortcode in the header template](https://github.com/user-attachments/assets/b0fa465c-52fc-4eb8-a19d-c330f397da61)

Image - Language switcher in the page header.
![Language switcher in the page header](https://github.com/user-attachments/assets/a39efdb2-183e-44ba-bc1d-3f39892d5004)

## Support

This is a free extension created for the community. The Ultimate Member team does not provide support for this extension.
Open new [issue](https://github.com/umdevelopera/um-polylang/issues) if you are facing a problem or have a suggestion.

**Give a star if you think this extension is useful. Thanks.**

## Useful links

[Ultimate Member core plugin info and download](https://wordpress.org/plugins/ultimate-member)

[Documentation for Ultimate Member](https://docs.ultimatemember.com)

[Official extensions for Ultimate Member](https://ultimatemember.com/extensions/)

[Free extensions for Ultimate Member](https://docs.google.com/document/d/1wp5oLOyuh5OUtI9ogcPy8NL428rZ8PVTu_0R-BuKKp8/edit?usp=sharing)

[Code snippets for Ultimate Member](https://docs.google.com/document/d/1_bikh4JYlSjjQa0bX1HDGznpLtI0ur_Ma3XQfld2CKk/edit?usp=sharing)
