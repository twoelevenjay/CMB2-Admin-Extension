=== CMB2 Admin Extension ===
Contributors:          twoelevenjay  
Tags:                  metaboxes, forms, fields, options, settings  
Requires at least:    4.5  
Tested up to:         6.6.2  
Requires PHP: Not specified
Stable tag:           1.0.3  
License:              GPLv2 or later  
License URI:          http://www.gnu.org/licenses/gpl-2.0.html  

== Description ==

CMB2 Admin Extension adds a user interface for admins to create CMB2 meta boxes from the WordPress admin.

[Download plugin on wordpress.org](https://wordpress.org/plugins/cmb2-admin-extension/)

CMB2 Admin Extension requires the most current version of the CMB2 plugin. You can find that [here](https://wordpress.org/plugins/cmb2/) . And you can find documentation on CMB2 [here](https://github.com/WebDevStudios/CMB2/wiki/Field-Types#types).


== Installation ==

1. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
1. Activate the plugin through the "Plugins" menu in WordPress.


== Frequently Asked Questions ==

FAQ's usually end up in the [github wiki](https://github.com/twoelevenjay/CMB2-Admin-Extension/wiki).

== Changelog ==

* Add tag generation to workflow.

* Add short description to the readme file.

* Test version bump

* Github workflow updates.

* Major version release with stability improvements and compatibility update for WordPress 6.0.
* Improved user interface for managing meta boxes from the WordPress admin.

* Fix headers already sent warning.

* Show different example code for repeatable groups.

* Fix show / hide repeatable group additional options.

* Add the option to add fields to a repeatable group.
* Fix showing the correct support fields when adding / removing a field to a metabox.

* Fix the argument order in the wp_enqueue_script call.

* Display usage functions like "get_post_meta" with generated meta key.
* Update codebase to comply with the latest coding standards. Props [@jrfnl](https://github.com/jrfnl)
* Correct portuguese (pt_PT) translated previously changed. Props [@pedro-mendonca](https://github.com/pedro-mendonca)

* Provide defaults for the fields. Props [@georgestephanis](https://github.com/georgestephanis)

* Fix color picker background bug.

* Fix currency symbol and a few other field specific args.

* Tidying up and refactoring, thanks to phpcs and phpmd.

* PHP bug fixes. Props [@jrfnl](https://github.com/jrfnl)
* Fix language loading. Props [@jrfnl](https://github.com/jrfnl)
* Portuguese translation. Props [@pedro-mendonca](https://github.com/pedro-mendonca)

* Disable the group field type and the default option until support is added.

* Some much needed refactoring.

* Fix load_textdomain.

* Show hide option fields based on field type selected under the meta_box post type.

* Added support for the rest of the Custom, Field Attributes.

* Add option to select which taxonomy should be used for taxonomy based fields.

* Minor field output fixes.

* Simplify admin notices, this fixes fatal error.

* All thanks to [jrfnl](https://github.com/jrfnl)
* Synced the GH repo with the WP repo to make sure it was up to date.
* Removed some files from the repo which shouldn't have been there in the first place.
* The readme wasn't properly parsable for the WP repo, fixed that.
* Fixed the plugin header which referred to the wrong plugin.
* Use CMB2_LOADED constant to check for CMB2.
* Prefix the CMB2_PLUGIN_FILE constant so as not to confuse it with one coming from CMB2 native.
* Don't hard-code the path to the plugins directory.
* Properly encode variables used in the activation url.
* Don't add the $cmb2 variable to the global namespace.
* Prevent conflict in the global namespace by wrapping things in ! defined() and ! function_exists()
* Fixed the is_cmb2_allowed() function which did not allow for new installs. The result of that was that after activation the plugin no longer showed in the plugins list, didn't show in the admin menu, couldn't be used nor deactivated, so rendered itself effectively useless.
* Fixed undefined index notices. [See: https://wordpress.org/support/topic/not-working-1299](http://)
* Make settings page title translatable.

* Hide UI for disallowed users. Previously only hid plugin table rows.

* Initial release, is working but could use a lot more features.

== Upgrade Notice ==
Upgrade to version 1.0.4 * Add tag generation to workflow.

