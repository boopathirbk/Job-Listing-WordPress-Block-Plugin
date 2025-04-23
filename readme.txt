=== Job Listings Block ===
Contributors: buofshangrila
Donate link: https://paypal.me/boopathirbk
Tags: block, gutenberg, jobs, listings, careers
Tested up to: 6.8
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a Gutenberg block to display filterable job listings managed in the editor, showing separate entries per location.

== Description ==

This plugin provides a custom Gutenberg block that allows users to easily add and manage job openings on their WordPress site. It features frontend filtering by individual location or department, dynamic counts, support for assigning multiple locations to jobs, and SEO enhancements. Each location assigned to a job results in a distinct filterable item on the frontend. It aims to be lightweight and easy to integrate into any theme.

Key Features:
*   Block Editor Integration: Manage job listings directly using sidebar controls.
*   Customizable Job Details: Add Title, Department, Employment Type, Short Description (for SEO), and a "Learn More" URL.
*   Multiple Locations Per Job: Assign multiple locations (one per line) in the editor.
*   Individual Location Display & Filtering: Renders a separate row for each job/location combination; filters show unique location names and filter accurately.
*   Link Target Control: Choose whether the "Learn More" link opens in a new tab.
*   Dynamic Filtering: Filter by individual Department and Location.
*   Clear Filters Button: Easily reset active filters.
*   Dynamic Job Count: Displays the count of currently visible job/location instances.
*   "No Results" Message: Shows a message when filters yield no matching jobs.
*   SEO Friendly: Generates JSON-LD `JobPosting` structured data.
*   Customizable Hiring Org: Set organization's name and website URL for SEO schema.
*   Theme Font Inheritance: Inherits fonts from your active theme.

== Installation ==

1.  Upload the plugin zip file via the 'Plugins > Add New > Upload Plugin' screen in your WordPress admin area.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Alternatively, unzip the plugin and upload the `job-listings-block` folder to the `/wp-content/plugins/` directory on your server and activate.
4.  Add the "Job Listings" block to any post or page via the block inserter.
5.  Configure jobs and organization details using the block sidebar controls.

== Frequently Asked Questions ==

= How do I add multiple locations? =

In the block editor sidebar, when editing a job, enter each location on a new line in the "Locations (One per line)" text area (e.g., Thane, India [new line] Tirupur, India). Each location will appear as a separate filter option and a separate row on the frontend.

= Is it SEO friendly? =

Yes, the plugin automatically generates `JobPosting` schema.org structured data based on the details you enter. Note that for SEO purposes, typically only the *first* location entered for a job is used in the structured data.

== Screenshots ==

1. The block editor interface showing sidebar controls, including the multi-line location input.
2. Frontend view showing separate rows for a job listed in multiple locations.
3. Frontend view of the location filter dropdown showing unique location names.
4. Frontend view showing the "No open position found" message.
5. Frontend view showing the "Clear Selection" button styled like the filters.

== Changelog ==

= 1.1.1 =
* Change: Render separate list items for each location assigned to a job.
* Change: Populate location filter dropdown with unique individual locations.
* Change: Update job count logic to count rendered job/location instances.
* Change: Rename "Clear All" button to "Clear Selection".
* Style: Adjust "Clear Selection" button style to better match dropdowns.
* Fix: Corrected location filter de-duplication logic in JavaScript.
* Fix: Ensure Location(s) label updates correctly based on the *original* job data.
* Update: Tested up to WordPress 6.5. <-- UPDATE THIS

= 1.1.0 =
* Feature: Add option to open "Learn More" link in a new tab.
* Feature: Add support for multiple locations per job posting (initial implementation).
* Feature: Add "Clear Filters" button to reset selections.
* Feature: Display total open position count as a heading above filters.
* Fix: Apply appropriate escaping to all frontend output per WordPress guidelines.
* Fix: Add missing translator comment for pluralization.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.1 =
This version changes how multi-location jobs are displayed and filtered on the frontend. Each location now gets its own row and filter option.

= 1.1.0 =
This version adds multiple location support, a link target option, and a clear filters button. No special upgrade steps needed from 1.0.0.