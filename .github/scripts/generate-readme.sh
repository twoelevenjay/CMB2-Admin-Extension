#!/bin/bash

# Define the source markdown files and the output file
README_MD="readme.md"
CHANGELOG_MD="CHANGELOG.md"
OUTPUT_FILE=".github/temp/readme.txt"

# Create the output directory if it doesn't exist
mkdir -p .github/temp

# Helper function to extract content based on headers
extract_section() {
    local file=$1
    local start_pattern=$2
    local end_pattern=$3
    awk "/$start_pattern/{flag=1;next}/$end_pattern/{flag=0}flag" "$file" | sed 's/\*\*//g' # Remove any '**' markers
}

# Extract the latest version and description from the changelog
extract_latest_version() {
    awk '/##/{version=$2; next} /^$/{next} {description=$0; print version, description; exit}' "$CHANGELOG_MD"
}

# Start generating the readme.txt
{
    # Extract the title from the first line of readme.md
    title=$(head -n 1 "$README_MD" | sed 's/# //')
    echo "=== $title ==="

    # Extract fields from the readme.md file
    contributors=$(grep -i "Contributors:" "$README_MD" | sed 's/.*Contributors: */Contributors: /;s/\*\*//g')
    echo "${contributors:-Contributors: Unknown}"

    tags=$(grep -i "Tags:" "$README_MD" | sed 's/.*Tags: */Tags: /;s/\*\*//g')
    echo "${tags:-Tags: Not specified}"

    requires_at_least=$(grep -i "Requires at least:" "$README_MD" | sed 's/.*Requires at least:/Requires at least:/;s/\*\*//g')
    echo "${requires_at_least:-Requires at least: Not specified}"

    tested_up_to=$(grep -i "Tested up to:" "$README_MD" | sed 's/.*Tested up to:/Tested up to:/;s/\*\*//g')
    echo "${tested_up_to:-Tested up to: Not specified}"

    requires_php=$(grep -i "Requires PHP:" "$README_MD" | sed 's/.*Requires PHP:/Requires PHP:/;s/\*\*//g')
    echo "${requires_php:-Requires PHP: Not specified}"

    stable_tag=$(grep -i "Stable tag:" "$README_MD" | sed 's/.*Stable tag:/Stable tag:/;s/\*\*//g')
    echo "${stable_tag:-Stable tag: Not specified}"

    license=$(grep -i "License:" "$README_MD" | sed 's/.*License:/License:/;s/\*\*//g')
    echo "${license:-License: GPLv2 or later}"

    license_uri=$(grep -i "License URI:" "$README_MD" | sed 's/.*License URI:/License URI:/;s/\*\*//g')
    echo "${license_uri:-License URI: http://www.gnu.org/licenses/gpl-2.0.html}"
    echo ""

    # Extract description section
    echo "== Description =="
    extract_section "$README_MD" "## Description" "##"
    echo ""

    # Extract installation section
    echo "== Installation =="
    extract_section "$README_MD" "## Installation" "##"
    echo ""

    # Extract FAQ section
    echo "== Frequently Asked Questions =="
    extract_section "$README_MD" "## Frequently Asked Questions" "##"
    echo ""

    # Extract changelog section from changelog.md
    echo "== Changelog =="
    awk '/##/{flag=1;next}/##/{flag=0}flag' "$CHANGELOG_MD"
    echo ""

    # Automatically populate the latest version and description for upgrade notice
    echo "== Upgrade Notice =="
    latest_version_and_description=$(extract_latest_version)
    echo "Upgrade to version $latest_version_and_description"
    echo ""
    
} > "$OUTPUT_FILE"

# Inform the user of output
echo "readme.txt has been generated at $OUTPUT_FILE"
