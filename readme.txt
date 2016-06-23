=== CMB2 Admin Extension ===
Contributors:      twoelevenjay
Donate link:       http://211j.com
Tags:              metaboxes, forms, fields, options, settings
Requires at least: 4.0
Tested up to:      4.5
Stable tag:        0.0.4
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

CMB2 Admin Extension adds a user interface for admins to create CMB2 meta boxes from the WordPress admin.

== Description ==

CMB2 Admin Extension adds a user interface for admins to create CMB2 meta boxes from the WordPress admin.

CMB2 Admin Extension requires the most current version of the CMB2 plugin. You can find that [here](https://wordpress.org/plugins/cmb2/) . And you can find documentation on CMB2 [here](https://github.com/WebDevStudios/CMB2/wiki/Field-Types#types).

### Features:

* Publish meta boxes from the post edit screen for the post edit screen
* Work with basic usage

### Translation
* None yet, but all are welcome

### Documentation
* CMB2 documentation to come. It will be at [the CMB2 Admin Extension wiki](https://github.com/twoelevenjay/CMB2-Admin-Extension/wiki) on GitHub.

### 3rd Party Resources

##### Custom Field Types
* [CMB2](https://github.com/WebDevStudios/CMB2/) from [WebDevStudios](https://webdevstudios.com).

### Contribution
All contributions welcome. If you would like to submit a pull request, please check out the [trunk branch](https://github.com/twoelevenjay/CMB2-Admin-Extension/tree/trunk) and pull request against it. Please read the [CONTRIBUTING](https://github.com/twoelevenjay/CMB2-Admin-Extension/CONTRIBUTING.md) doc for more details.

== Changelog ==

## 0.0.5 beta 06.23.2016 
* Minor field output fixes

## 0.0.4 beta 06.22.2016
* Simplify admin notices, this fixes fatal error


## 0.0.3 beta 01.24.2016
* All thanks to @jrfnl
* Synced the GH repo with the WP repo to make sure it was up to date.
* Removed some files from the repo which shouldn't have been there in the first place.
* The readme wasn't properly parseable for the WP repo, fixed that.
* Fixed the plugin header which referred to the wrong plugin.
* Use CMB2_LOADED constant to check for CMB2.
* Prefix the CMB2_PLUGIN_FILE constant so as not to confuse it with one coming from CMB2 native.
* Don't hard-code the path to the plugins directory.
* Properly encode variables used in the activation url.
* Don't add the $cmb2 variable to the global namespace.
* Prevent conflict in the global namespace by wrapping things in ! defined() and ! function_exists()
* Fixed the is_cmb2_allowed() function which did not allow for new installs. The result of that was that after activation the plugin no longer showed in the plugins list, didn't show in the admin menu, couldn't be used nor deactivated, so rendered itself effectively useless.
* Fixed undefined index notices. See: https://wordpress.org/support/topic/not-working-1299
* Make settings page title translatable.

## 0.0.2 beta 06.15.2015
* Hide UI for disallowed users. Previously only hid plugin table rows.

## 0.0.1 beta 06.10.2015
* Initial release, is working but could use a lot more features.

== Installation ==

1. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
1. Activate the plugin through the "Plugins" menu in WordPress.
