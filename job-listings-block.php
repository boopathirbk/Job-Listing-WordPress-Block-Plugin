<?php
/**
 * Plugin Name:       Job Listings Block
 * Description:       A block to display and manage job listings with filtering.
 * Requires at least: 6.0
 * Tested up to:      6.8
 * Requires PHP:      7.4
 * Version:           1.1.1
 * Author:            Boopathi R
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       job-listings-block
 *
 * @package           create-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function job_listings_block_init() {
    // error_log('Job Listings Block - job_listings_block_init function called.'); // Optional: Keep for debugging if needed

	// --- THIS IS THE CORRECTED LINE ---
    // It now points to the plugin's root directory (__DIR__) where block.json is located.
	register_block_type( __DIR__, array(
        'render_callback' => 'job_listings_block_render_callback',
        // It's still good practice to define attributes here when using a render_callback
        // to ensure they are correctly processed and available.
        'attributes'      => array(
            'jobs' => array(
                'type'    => 'array',
                'default' => array(),
                'items'   => array(
                    'type' => 'object',
                ),
            ),
             'hiringOrganizationName' => array(
                 'type' => 'string',
                 'default' => 'Your Company Name',
             ),
             'hiringOrganizationUrl' => array(
                 'type' => 'string',
                 'default' => 'https://www.yourcompanywebsite.com',
             ),
        ),
    ) );
}
add_action( 'init', 'job_listings_block_init' );

/**
 * Server-side rendering function for the block.
 *
 * @param array $attributes The block attributes.
 * @param string $content The block inner content (not used for dynamic blocks).
 * @return string HTML output for the block.
 */
function job_listings_block_render_callback( $attributes, $content ) {
    // Include the render logic file
    // Ensure the path is correct relative to this file
    $render_file = plugin_dir_path( __FILE__ ) . 'render.php';

    if ( file_exists( $render_file ) ) {
        ob_start();
        // Pass attributes to the included file via scope
        include $render_file;
        return ob_get_clean();
    } else {
        // Fallback or error message if render.php is missing
        return '<p>Error: Job Listings Block render file not found.</p>';
    }
}