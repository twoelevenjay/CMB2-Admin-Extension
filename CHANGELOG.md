

## Changelog

### 1.0.4 10.19.2024
* Add tag generation to workflow.

### 1.0.3 10.19.2024
* Add short description to the readme file.

### 1.0.2 10.19.2024
* Test version bump

### 1.0.1 10.19.2024
* Github workflow updates.

### 1.0.0 10.19.2024
* Major version release with stability improvements and compatibility update for WordPress 6.0.
* Improved user interface for managing meta boxes from the WordPress admin.

### 0.2.3 beta 04.23.2018
* Fix headers already sent warning.

### 0.2.2 beta 04.23.2018
* Show different example code for repeatable groups.

### 0.2.1 beta 04.23.2018
* Fix show / hide repeatable group additional options.

### 0.2.0 beta 04.23.2018
* Add the option to add fields to a repeatable group.
* Fix showing the correct support fields when adding / removing a field to a metabox.

### 0.1.8 beta 04.06.2018
* Fix the argument order in the wp_enqueue_script call.

### 0.1.7 beta 11.07.2017
* Display usage functions like "get_post_meta" with generated meta key.
* Update codebase to comply with the latest coding standards. Props [@jrfnl](https://github.com/jrfnl)
* Correct portuguese (pt_PT) translated previously changed. Props [@pedro-mendonca](https://github.com/pedro-mendonca)

### 0.1.6 beta 10.19.2017
* Provide defaults for the fields. Props [@georgestephanis](https://github.com/georgestephanis)

### 0.1.5 beta 12.06.2016
* Fix color picker background bug.

### 0.1.4 beta 09.06.2016
* Fix currency symbol and a few other field specific args.

### 0.1.3 beta 07.12.2016
* Tidying up and refactoring, thanks to phpcs and phpmd.

### 0.1.2 beta 06.29.2016
* PHP bug fixes. Props [@jrfnl](https://github.com/jrfnl)
* Fix language loading. Props [@jrfnl](https://github.com/jrfnl)
* Portuguese translation. Props [@pedro-mendonca](https://github.com/pedro-mendonca)

### 0.1.1 beta 06.26.2016
* Disable the group field type and the default option until support is added.

### 0.1.0 beta 06.26.2016
* Some much needed refactoring.

### 0.0.9 beta 06.26.2016
* Fix load_textdomain.

### 0.0.8 beta 06.26.2016
* Show hide option fields based on field type selected under the meta_box post type.

### 0.0.7 beta 06.25.2016
* Added support for the rest of the Custom, Field Attributes.

### 0.0.6 beta 06.23.2016
* Add option to select which taxonomy should be used for taxonomy based fields.

### 0.0.5 beta 06.23.2016
* Minor field output fixes.

### 0.0.4 beta 06.22.2016
* Simplify admin notices, this fixes fatal error.

### 0.0.3 beta 01.24.2016
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

### 0.0.2 beta 06.15.2015
* Hide UI for disallowed users. Previously only hid plugin table rows.

### 0.0.1 beta 06.10.2015
* Initial release, is working but could use a lot more features.
