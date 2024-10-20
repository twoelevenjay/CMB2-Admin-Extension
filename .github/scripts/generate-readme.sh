#!/bin/bash

# Path to store the readme.txt file
output_file=${1:-readme.txt}

# Generate readme.txt for WordPress.org from readme.md and changelog.md

# Start with the contents of readme.md
echo "Generating $output_file from readme.md and changelog.md..."

# Clear previous readme.txt if it exists
> "$output_file"

# Extract the plugin header section from readme.txt (template)
cat <<EOT >> "$output_file"
=== CMB2 Admin Extension ===
Contributors: twoelevenjay
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=leon%40211j%2ecom&lc=MQ&item_name=Two%20Eleven%20Jay&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest
Tags: metaboxes, forms, fields, options, settings
Requires at least: 4.5
Tested up to: 6.6.2
Requires PHP: 8.1
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

EOT

# Append the description from readme.md
echo "== Description ==" >> "$output_file"
sed -n '/# CMB2 Admin Extension/,$p' readme.md | sed '1d' >> "$output_file"  # Skipping the first line (header) of readme.md

# Add installation instructions
cat <<EOT >> "$output_file"

== Installation ==
1. Extract the .zip file and upload its contents to the \`/wp-content/plugins/\` directory. Alternatively, you can install directly through the Plugin directory within your WordPress admin.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to the CMB2 Admin Extension interface within the WordPress admin to create and manage meta boxes.

== Frequently Asked Questions ==
= Do I need CMB2 to use this extension? =
Yes, CMB2 is required for this extension to work.

= Where can I find documentation? =
Documentation will be available in the [GitHub wiki](https://github.com/twoelevenjay/CMB2-Admin-Extension/wiki).

EOT

# Add changelog from changelog.md
echo "== Changelog ==" >> "$output_file"
cat changelog.md >> "$output_file"

# Add upgrade notice
cat <<EOT >> "$output_file"

== Upgrade Notice ==
= 1.0.4 =
This major version introduces stability improvements and compatibility with the latest WordPress version.

== Translation ==
* Available in French and Portuguese. Contributions are welcome for other languages.

== 3rd Party Resources ==
* [CMB2](https://github.com/WebDevStudios/CMB2/) from [WebDevStudios](https://webdevstudios.com).

== Contribution ==
All contributions are welcome. Please read the [CONTRIBUTING](https://github.com/twoelevenjay/CMB2-Admin-Extension/blob/master/CONTRIBUTING.md) doc for more details.

EOT

echo "$output_file generated successfully!"
