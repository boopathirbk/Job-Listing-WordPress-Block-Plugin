=== Job Listings Block ===
Contributors: buofshangrila
Donate link: https://example.com/donate/
Tags: block, gutenberg, jobs, listings, careers
Requires at least: 6.0
Tested up to: 6.8 
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display filterable job listings using a Gutenberg block.

== Description ==

This plugin provides a custom Gutenberg block that allows users to easily add and manage job openings on their WordPress site. It features frontend filtering, dynamic counts, multiple location support, and SEO enhancements for a user-friendly job board experience.

Key Features:
*   Block Editor Integration: Manage job listings directly using sidebar controls.
*   Customizable Job Details: Add Title, Department, Employment Type, Short Description (for SEO), and a "Learn More" URL.
*   Multiple Locations: Assign multiple locations to a single job posting (entered one per line).
*   Link Target Control: Choose whether the "Learn More" link opens in a new tab.
*   Frontend Display: Renders a clean, responsive list of jobs.
*   Dynamic Filtering: Filter by Department and Location. Jobs appear if *any* assigned location matches.
*   Clear Filters Button: Easily reset active filters.
*   Dynamic Job Count: Displays the number of currently visible open positions as a heading.
*   "No Results" Message: Shows a message when filters result in no matching jobs.
*   SEO Friendly: Automatically generates JSON-LD `JobPosting` structured data.
*   Customizable Hiring Org: Set organization's name and website URL for SEO schema.
*   Theme Font Inheritance: Inherits fonts from your active theme.


== Installation ==

1.  Upload the plugin files (the entire `job-listings-block` folder after generating the installable zip) to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly by uploading the ZIP file.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Add the "Job Listings" block to any post or page via the block inserter.
4.  Configure jobs and organization details using the block sidebar controls.

== Frequently Asked Questions ==

= How do I add multiple locations? =

In the block editor sidebar, when editing a job, enter each location on a new line in the "Locations (One per line)" text area.

= Is it SEO friendly? =

Yes, the plugin automatically generates `JobPosting` schema.org structured data based on the details you enter.

== Screenshots ==

1. The block editor interface showing sidebar controls.
2. Frontend view of the job listings heading, filters, and jobs.
3. Frontend view showing the "No open position found" message when filters yield no results.
4. Frontend view showing the "Clear Filters" button.

== Changelog ==

= 1.1.0 =
* Feature: Add option to open "Learn More" link in a new tab.
* Feature: Add support for multiple locations per job posting.
* Feature: Add "Clear Filters" button to reset selections.
* Feature: Display total open position count as a heading above filters.
* Fix: Apply appropriate escaping to all frontend output per WordPress guidelines.
* Fix: Add missing translator comment for pluralization.
* Update: Tested up to WordPress [LATEST WP VERSION].

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.0 =
This version adds multiple location support, a link target option, and a clear filters button. No special upgrade steps needed.