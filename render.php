<?php
/**
 * PHP file to render the Job Listings Block on the frontend.
 *
 * @package job-listings-block
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get jobs data from attributes, default to empty array if not set.
// Sanitize attributes here if necessary, though saved attributes are generally trusted from editor.
$jobs = isset( $attributes['jobs'] ) && is_array( $attributes['jobs'] ) ? $attributes['jobs'] : array();
$hiring_org_name = isset($attributes['hiringOrganizationName']) ? sanitize_text_field($attributes['hiringOrganizationName']) : 'Your Company Name';
$hiring_org_url = isset($attributes['hiringOrganizationUrl']) ? esc_url($attributes['hiringOrganizationUrl']) : ''; // Use esc_url for URLs

// Generate a unique ID base *once* for this block instance to scope JS/CSS IDs.
$unique_id_base = uniqid( 'job-block-' );
$total_jobs_count = count( $jobs ); // Initial total count

// Get block wrapper attributes (for alignment, custom classes, etc.).
// Using wp_kses_data because it allows safe HTML attributes like class, id, data-*.
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'job-listings-block-wrapper' ) );

?>
<div <?php echo wp_kses_data( $wrapper_attributes ); ?>>
	<div class="job-listings-container">

        <h2 id="job-count-<?php echo esc_attr( $unique_id_base ); ?>" class="job-count-heading">
            <?php
             /* translators: %d: Number of open positions. */ // <--- MUST be right here
            printf(
                esc_html( _n( '%d Open Position', '%d Open Positions', $total_jobs_count, 'job-listings-block' ) ),
                (int) $total_jobs_count
            );
            ?>
        </h2>

		<!-- Filters Section -->
        <div class="filters-section">
             <span class="filter-label"><?php echo esc_html__( 'Filter by', 'job-listings-block' ); ?></span>
             <div class="filter-dropdown">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false" aria-hidden="true" width="20" height="20"><path d="M12 11.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5z"></path><path d="M12 2C8.13 2 5 5.13 5 9c0 4.17 4.42 9.92 6.24 12.11.4.48 1.13.48 1.53 0C14.58 18.92 19 13.17 19 9c0-3.87-3.13-7-7-7zm0 19.5c-1.53-2.18-5-7.6-5-12.5 0-2.76 2.24-5 5-5s5 2.24 5 5c0 4.9-3.47 10.32-5 12.5z"></path></svg>
                <select id="location-filter-<?php echo esc_attr( $unique_id_base ); ?>" aria-label="<?php esc_attr_e( 'Filter by Location', 'job-listings-block' ); ?>">
                    <option value=""><?php esc_html_e( 'All Locations', 'job-listings-block' ); ?></option>
                    <!-- Options populated by JS -->
                </select>
             </div>
             <div class="filter-dropdown">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" focusable="false" aria-hidden="true" width="20" height="20"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
                <select id="department-filter-<?php echo esc_attr( $unique_id_base ); ?>" aria-label="<?php esc_attr_e( 'Filter by Department', 'job-listings-block' ); ?>">
                    <option value=""><?php esc_html_e( 'All Departments', 'job-listings-block' ); ?></option>
                     <!-- Options populated by JS -->
               </select>
            </div>
            <?php // Clear Filter Button ?>
            <button id="clear-filters-<?php echo esc_attr( $unique_id_base ); ?>" class="clear-filters-button" aria-label="<?php esc_attr_e( 'Clear Filters', 'job-listings-block' ); ?>" title="<?php esc_attr_e( 'Clear Filters', 'job-listings-block' ); ?>">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                 <span><?php esc_html_e( 'Clear', 'job-listings-block' ); ?></span>
            </button>
        </div>


		<?php if ( ! empty( $jobs ) ) : ?>
			<!-- Job List -->
			<div id="job-list-<?php echo esc_attr( $unique_id_base ); ?>" class="job-list" role="list">
				<?php foreach ( $jobs as $index => $job ) :
                    // Prepare variables (sanitization/escaping applied on output below)
                    $job_id = isset( $job['id'] ) ? sanitize_key( $job['id'] ) : 'job-' . $index;
					$job_title_raw = isset( $job['title'] ) ? $job['title'] : '';
					$job_department_raw = isset( $job['department'] ) ? trim( $job['department'] ) : '';
                    $job_department_slug = sanitize_title( $job_department_raw );
                    $job_locations_raw = isset( $job['locations'] ) && is_array($job['locations']) ? $job['locations'] : [];
                    $job_locations_display_array = array_map('esc_html', $job_locations_raw); // Pre-escape for implode safety
                    $job_location_display = implode(', ', $job_locations_display_array);
                    $location_slugs = implode(',', array_map('sanitize_title', $job_locations_raw));
                    $job_employment_type_raw = isset($job['employmentType']) ? $job['employmentType'] : 'Full-time';
                    $job_description_raw = isset($job['description']) ? $job['description'] : '';
					$job_url_raw = isset( $job['url'] ) ? $job['url'] : '#';
                    $open_in_new_tab = isset($job['openInNewTab']) && $job['openInNewTab'];
                    // $target_attr variable removed - handled directly in <a> tag

                    // Basic check for essential data before attempting to render item
                    if (empty($job_title_raw) || empty($job_locations_raw) || empty($job_department_raw)) {
                        continue;
                    }
				?>
					<div class="job-item"
						 data-locations="<?php echo esc_attr( $location_slugs ); ?>"
						 data-department="<?php echo esc_attr( $job_department_slug ); ?>"
                         data-employment-type="<?php echo esc_attr( $job_employment_type_raw ); ?>"
                         id="<?php echo esc_attr( $job_id ); ?>"
                         role="listitem">

						<div class="job-details">
                            <div class="job-meta">
                                <span class="job-department"><?php echo esc_html( $job_department_raw ); ?></span>
                                <span class="job-employment-type"><?php echo esc_html( $job_employment_type_raw ); ?></span>
                            </div>
							<h3 class="job-title"><?php echo esc_html( $job_title_raw ); ?></h3>
                            <?php if (!empty($job_description_raw)): ?>
                                <?php // Allows basic HTML like <p>, <strong>, <a> etc. defined by wp_kses_post ?>
                                <p class="job-description-snippet"><?php echo wp_kses_post( $job_description_raw ); ?></p>
                            <?php endif; ?>
						</div>
						<div class="job-location-info">
							<span class="job-location-label"><?php esc_html_e( 'Location', 'job-listings-block' ); ?></span>
                            <?php // $job_location_display is pre-escaped string ?>
							<span class="job-location"><?php echo esc_html( $job_location_display ); // FIXED: Escape the final string ?></span>
						</div>
						<div class="job-link-wrapper">
                            <?php // Conditionally output target/rel attributes, href is escaped. ?>
							<a href="<?php echo esc_url( $job_url_raw ); ?>" class="job-link"<?php if ($open_in_new_tab) { echo ' target="_blank" rel="noopener noreferrer"'; } ?>>
								<?php esc_html_e( 'Learn more', 'job-listings-block' ); ?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" focusable="false" aria-hidden="true" width="18" height="18"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"></path></svg>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div> <!-- End Job List -->

             <!-- Message Container for No Results -->
            <div id="no-jobs-message-<?php echo esc_attr( $unique_id_base ); ?>" class="no-jobs-found-message" style="display: none;">
                 <?php esc_html_e( 'No open position found', 'job-listings-block' ); ?>
            </div>


            <?php
            // --- Generate JSON-LD Script ---
            $job_posting_schema = array();
             $hiring_org = array(
                "@type" => "Organization",
                "name" => $hiring_org_name, // Already sanitized
                "sameAs" => $hiring_org_url // Already escaped
            );
            $current_date = current_time( 'Y-m-d' );

            foreach ( $jobs as $job ) {
                $title = isset( $job['title'] ) ? sanitize_text_field( $job['title'] ) : null;
                 // Use first location for schema or handle multiple if API supports it
                $job_locations_raw = isset( $job['locations'] ) && is_array($job['locations']) ? $job['locations'] : [];
                $loc_text = !empty($job_locations_raw) ? sanitize_text_field($job_locations_raw[0]) : null; // Use first location for schema

                $desc = !empty($job['description']) ? wp_kses_post( $job['description'] ) : '<p>' . sanitize_text_field( $job['title'] ) . ' position available.</p>';
                $url = isset( $job['url'] ) ? esc_url( $job['url'] ) : null;
                $emp_type = isset($job['employmentType']) ? sanitize_text_field($job['employmentType']) : "Full-time"; // Default to Full-time string

                if (!$title || !$url) continue; // Skip if essential SEO data missing

                $schema_item = array(
                    "@context" => "https://schema.org/",
                    "@type" => "JobPosting",
                    "title" => $title,
                    "description" => $desc, // wp_kses_post applied when preparing var
                    "hiringOrganization" => $hiring_org,
                    // Format employmentType for schema.org standards (e.g., FULL_TIME, PART_TIME)
                    "employmentType" => strtoupper( str_replace( '-', '_', $emp_type ) ),
                    "datePosted" => $current_date,
                    "url" => $url,
                    "jobLocation" => array( "@type" => "Place" )
                );

                 if ($loc_text && $loc_text !== 'Multiple Locations') { // Schema often expects one primary location
                     $parts = explode(',', $loc_text);
                     $schema_item['jobLocation']['address'] = array("@type" => "PostalAddress");
                     if(count($parts) > 0) $schema_item['jobLocation']['address']['addressLocality'] = trim($parts[0]);
                     if(count($parts) > 1) $schema_item['jobLocation']['address']['addressCountry'] = trim($parts[count($parts) - 1]);
                 } else if ($loc_text === 'Multiple Locations') {
                      $schema_item['jobLocationType'] = "TELECOMMUTE"; // Or handle region based - Telecommute is safer guess
                      if (isset($schema_item['jobLocation']['address'])) unset($schema_item['jobLocation']['address']); // Remove address if multiple/remote
                      $schema_item['jobLocation']['name'] = "Multiple Locations"; // Use name for multiple
                 } else {
                     unset($schema_item['jobLocation']); // Remove location if not specified
                 }
                $job_posting_schema[] = $schema_item;
            }

            if ( ! empty( $job_posting_schema ) ) {
                // Using wp_json_encode ensures valid JSON output
                echo '<script type="application/ld+json">' . wp_json_encode( $job_posting_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>';
            }
            // --- End JSON-LD Script ---
            ?>


            <!-- Filtering JavaScript -->
			<script id="job-filter-script-<?php echo esc_attr( $unique_id_base ); ?>">
				// Wrap in IIFE to avoid global scope pollution and run immediately
                (function() {
                    // Use the unique ID base generated by PHP
                    const uniqueIdBase = '<?php echo esc_js( $unique_id_base ); ?>';

                    // Find elements *specific to this block instance* using the unique IDs
                    const locationFilter = document.getElementById('location-filter-' + uniqueIdBase);
                    const departmentFilter = document.getElementById('department-filter-' + uniqueIdBase);
                    const jobList = document.getElementById('job-list-' + uniqueIdBase);
                    const noJobsMessage = document.getElementById('no-jobs-message-' + uniqueIdBase);
                    const jobCountDisplay = document.getElementById('job-count-' + uniqueIdBase);
                    const clearButton = document.getElementById('clear-filters-' + uniqueIdBase);


                    // Check if all elements were found before proceeding
                    if (!locationFilter || !departmentFilter || !jobList || !noJobsMessage || !jobCountDisplay || !clearButton) {
                        return; // Exit if essential elements are missing
                    }

                    const jobItems = jobList.querySelectorAll('.job-item');

                    // --- Dynamic population (Handles multiple locations) ---
                    if (jobItems.length > 0) {
                        const locations = new Map(); // Store slug -> display text
                        const departments = new Map();

                        jobItems.forEach(item => {
                            // Handle multiple location slugs/display
                            const locationSlugs = item.dataset.locations ? item.dataset.locations.split(',') : [];
                             const locTextEl = item.querySelector('.job-location');
                             const locFullText = locTextEl ? locTextEl.textContent.trim() : '';
                             // Use the full comma-separated text for display value lookup in the dropdown
                             const displayLocationText = locFullText || locSlug.replace(/-/g, ' '); // Fallback if element missing

                            locationSlugs.forEach((locSlug) => {
                                if (locSlug && !locations.has(locSlug)) {
                                    locations.set(locSlug, displayLocationText); // Map slug to full display text
                                }
                            });

                            // Department
                            const depValue = item.dataset.department;
                            const depTextEl = item.querySelector('.job-department');
                            const depText = depTextEl ? depTextEl.textContent.trim() : null;
                            if (depValue && depText && !departments.has(depValue)) {
                                departments.set(depValue, depText);
                            }
                        });

                        // Filter out duplicate display texts before sorting and adding options
                        const uniqueDisplayLocations = [...new Map(locations).values()];
                        const sortedLocations = uniqueDisplayLocations.sort((a, b) => a.localeCompare(b));

                        const uniqueDisplayDepartments = [...new Map(departments).values()];
                        const sortedDepartments = uniqueDisplayDepartments.sort((a, b) => a.localeCompare(b));


                        while (locationFilter.options.length > 1) locationFilter.remove(1);
                        while (departmentFilter.options.length > 1) departmentFilter.remove(1);

                        // Add unique locations based on display text
                        sortedLocations.forEach((text) => {
                             // Find the first slug associated with this display text
                            const matchingEntry = [...locations.entries()].find(entry => entry[1] === text);
                            if (matchingEntry) {
                                const value = matchingEntry[0]; // Use the slug as the value
                                const option = document.createElement('option');
                                option.value = value;
                                option.textContent = text; // Display the text
                                locationFilter.appendChild(option);
                            }
                        });

                        // Add unique departments based on display text
                        sortedDepartments.forEach((text) => {
                            const matchingEntry = [...departments.entries()].find(entry => entry[1] === text);
                            if (matchingEntry) {
                                const value = matchingEntry[0]; // Use the slug as value
                                const option = document.createElement('option');
                                option.value = value;
                                option.textContent = text;
                                departmentFilter.appendChild(option);
                            }
                        });
                    }
                    // --- End dynamic population ---


                    // --- Filtering Logic (Handles multiple locations) ---
                    function filterJobs() {
                        const currentJobItems = jobList.querySelectorAll('.job-item');
                        if (currentJobItems.length === 0 && !<?php echo empty($jobs) ? 'true' : 'false'; ?>) {
                             noJobsMessage.style.display = 'block';
                             jobList.style.display = 'none';
                             jobCountDisplay.textContent = '0 Open Positions'; // Update count
                             return;
                        }

                        const selectedLocation = locationFilter.value; // This is the slug
                        const selectedDepartment = departmentFilter.value;
                        let visibleJobsCount = 0;

                        currentJobItems.forEach(item => {
                            const itemLocationSlugs = item.dataset.locations ? item.dataset.locations.split(',') : [];
                            const itemDepartment = item.dataset.department;

                            const locationMatch = selectedLocation === "" || itemLocationSlugs.includes(selectedLocation); // Check if selected slug is IN the list
                            const departmentMatch = selectedDepartment === "" || itemDepartment === selectedDepartment;

                            if (locationMatch && departmentMatch) {
                                item.classList.remove('hidden');
                                visibleJobsCount++;
                            } else {
                                item.classList.add('hidden');
                            }
                        });

                         // Show/hide the 'no jobs found' message
                        if (visibleJobsCount === 0 && jobItems.length > 0) { // Only show if jobs existed but none match
                            noJobsMessage.style.display = 'block';
                            jobList.style.display = 'none';
                        } else {
                            noJobsMessage.style.display = 'none';
                            jobList.style.display = 'grid';
                        }

                        // Update job count display heading
                        let countText = '';
                        if (visibleJobsCount === 1) {
                            countText = '1 Open Position'; // Singular, Title Case
                        } else {
                            countText = `${visibleJobsCount} Open Positions`; // Plural, Title Case
                        }
                        jobCountDisplay.textContent = countText;

                    } // <-- End of filterJobs function

                    // Clear Button Event Listener
                    clearButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        locationFilter.value = "";
                        departmentFilter.value = "";
                        filterJobs();
                    });

                    // Add event listeners for dropdowns
                    locationFilter.addEventListener('change', filterJobs);
                    departmentFilter.addEventListener('change', filterJobs);

                 })(); // Immediately invoke the function
			</script>


		<?php else : ?>
			<p><?php esc_html_e( 'No job listings have been added yet.', 'job-listings-block' ); ?></p>
            <?php // Hide the job count heading if there are no jobs at all initially ?>
            <style> #job-count-<?php echo esc_attr( $unique_id_base ); ?> { display: none; } </style>
		<?php endif; ?>

	</div>
</div>
<?php
// End output buffering.
?>