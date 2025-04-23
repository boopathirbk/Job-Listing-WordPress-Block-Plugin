<?php
/**
 * PHP file to render the Job Listings Block on the frontend.
 * v1.1.3 - Corrected filter population logic.
 *
 * @package job-listings-block
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get jobs data from attributes, default to empty array if not set.
$jobs = isset( $attributes['jobs'] ) && is_array( $attributes['jobs'] ) ? $attributes['jobs'] : array();
$hiring_org_name = isset($attributes['hiringOrganizationName']) ? sanitize_text_field($attributes['hiringOrganizationName']) : 'Your Company Name';
$hiring_org_url = isset($attributes['hiringOrganizationUrl']) ? esc_url($attributes['hiringOrganizationUrl']) : '';

// Generate a unique ID base *once* for this block instance.
$unique_id_base = uniqid( 'job-block-' );

// Calculate initial count based on *rendered items* (job * location count)
$initial_rendered_item_count = 0;
foreach ($jobs as $job) {
    $job_locations_raw = isset( $job['locations'] ) && is_array($job['locations']) ? $job['locations'] : [];
    // Check if essential fields for rendering are present
    if (!empty($job['title']) && !empty($job['department']) && !empty($job_locations_raw)) {
        // Count valid locations (non-empty strings after trim)
        $valid_locations = array_filter(array_map('trim', $job_locations_raw));
        $initial_rendered_item_count += count($valid_locations);
    }
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'job-listings-block-wrapper' ) );

?>
<div <?php echo wp_kses_data( $wrapper_attributes ); ?>>
	<div class="job-listings-container">

        <h2 id="job-count-<?php echo esc_attr( $unique_id_base ); ?>" class="job-count-heading">
            <?php
                /* translators: %d: Number of open positions. */
                printf(
                    esc_html( _n( '%d Open Position', '%d Open Positions', $initial_rendered_item_count, 'job-listings-block' ) ),
                    (int) $initial_rendered_item_count
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
            <button id="clear-filters-<?php echo esc_attr( $unique_id_base ); ?>" class="clear-filters-button" style="display: none;" aria-label="<?php esc_attr_e( 'Clear Filters', 'job-listings-block' ); ?>" title="<?php esc_attr_e( 'Clear Filters', 'job-listings-block' ); ?>">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                 <span class="clear-filters-button-text"><?php esc_html_e( 'Clear Selection', 'job-listings-block' ); ?></span>
            </button>
        </div>


		<?php if ( ! empty( $jobs ) ) : ?>
			<!-- Job List -->
			<div id="job-list-<?php echo esc_attr( $unique_id_base ); ?>" class="job-list" role="list">
				<?php foreach ( $jobs as $index => $job ) :
                    // Prepare job-level variables
                    $job_id_base = isset( $job['id'] ) ? sanitize_key( $job['id'] ) : 'job-' . $index;
					$job_title_raw = isset( $job['title'] ) ? $job['title'] : '';
					$job_department_raw = isset( $job['department'] ) ? trim( $job['department'] ) : '';
                    $job_department_slug = sanitize_title( $job_department_raw );
                    $job_locations_raw = isset( $job['locations'] ) && is_array($job['locations']) ? $job['locations'] : [];
                    $job_employment_type_raw = isset($job['employmentType']) ? $job['employmentType'] : 'Full-time';
                    $job_description_raw = isset($job['description']) ? $job['description'] : '';
					$job_url_raw = isset( $job['url'] ) ? $job['url'] : '#';
                    $open_in_new_tab = isset($job['openInNewTab']) && $job['openInNewTab'];

                    // Basic check for essential data
                    if (empty($job_title_raw) || empty($job_locations_raw) || empty($job_department_raw)) {
                        continue; // Skip this job entirely if core data missing
                    }

                    // Loop through each location to render an item
                    foreach ($job_locations_raw as $location_index => $single_location) {
                        $single_location_trimmed = trim($single_location);
                        if (empty($single_location_trimmed)) continue; // Skip empty location lines

                        $single_location_slug = sanitize_title($single_location_trimmed);
                        $job_instance_id = $job_id_base . '-' . $single_location_slug;
				?>
					<div class="job-item"
						 data-location="<?php echo esc_attr( $single_location_slug ); ?>"
						 data-department="<?php echo esc_attr( $job_department_slug ); ?>"
                         data-employment-type="<?php echo esc_attr( $job_employment_type_raw ); ?>"
                         id="<?php echo esc_attr( $job_instance_id ); ?>"
                         role="listitem">

						<div class="job-details">
                            <div class="job-meta">
                                <span class="job-department"><?php echo esc_html( $job_department_raw ); ?></span>
                                <span class="job-employment-type"><?php echo esc_html( $job_employment_type_raw ); ?></span>
                            </div>
							<h3 class="job-title"><?php echo esc_html( $job_title_raw ); ?></h3>
                            <?php if (!empty($job_description_raw)): ?>
                                <p class="job-description-snippet"><?php echo wp_kses_post( $job_description_raw ); ?></p>
                            <?php endif; ?>
						</div>
						<div class="job-location-info">
							<span class="job-location-label"><?php esc_html_e( 'Location', 'job-listings-block' ); ?></span>
							<span class="job-location"><?php echo esc_html( $single_location_trimmed ); ?></span>
						</div>
						<div class="job-link-wrapper">
							<a href="<?php echo esc_url( $job_url_raw ); ?>" class="job-link"<?php if ($open_in_new_tab) { echo ' target="_blank" rel="noopener noreferrer"'; } ?>>
								<?php esc_html_e( 'Learn more', 'job-listings-block' ); ?>
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" focusable="false" aria-hidden="true" width="18" height="18"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"></path></svg>
							</a>
						</div>
					</div>
				<?php
                    } // --- End foreach $job_locations_raw ---
                endforeach; // --- End foreach $jobs ---
                ?>
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
                $job_locations_raw = isset( $job['locations'] ) && is_array($job['locations']) ? $job['locations'] : [];
                $loc_text = !empty($job_locations_raw) ? sanitize_text_field($job_locations_raw[0]) : null; // Use first location for schema
                $desc = !empty($job['description']) ? wp_kses_post( $job['description'] ) : '<p>' . sanitize_text_field( $job['title'] ) . ' position available.</p>';
                $url = isset( $job['url'] ) ? esc_url( $job['url'] ) : null;
                $emp_type = isset($job['employmentType']) ? sanitize_text_field($job['employmentType']) : "Full-time";

                if (!$title || !$url) continue;

                $schema_item = array(
                    "@context" => "https://schema.org/", "@type" => "JobPosting", "title" => $title, "description" => $desc,
                    "hiringOrganization" => $hiring_org, "employmentType" => strtoupper( str_replace( '-', '_', $emp_type ) ),
                    "datePosted" => $current_date, "url" => $url, "jobLocation" => array( "@type" => "Place" )
                );

                 if ($loc_text && $loc_text !== 'Multiple Locations') {
                     $parts = explode(',', $loc_text);
                     $schema_item['jobLocation']['address'] = array("@type" => "PostalAddress");
                     if(count($parts) > 0) $schema_item['jobLocation']['address']['addressLocality'] = trim($parts[0]);
                     if(count($parts) > 1) $schema_item['jobLocation']['address']['addressCountry'] = trim($parts[count($parts) - 1]);
                 } else if ($loc_text === 'Multiple Locations') {
                      $schema_item['jobLocationType'] = "TELECOMMUTE";
                      if (isset($schema_item['jobLocation']['address'])) unset($schema_item['jobLocation']['address']);
                      $schema_item['jobLocation']['name'] = "Multiple Locations";
                 } else { unset($schema_item['jobLocation']); }
                $job_posting_schema[] = $schema_item;
            }

            if ( ! empty( $job_posting_schema ) ) {
                echo '<script type="application/ld+json">' . wp_json_encode( $job_posting_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>';
            }
            // --- End JSON-LD Script ---
            ?>


            <!-- Filtering JavaScript (Corrected Population Logic v2) -->
			<script id="job-filter-script-<?php echo esc_attr( $unique_id_base ); ?>">
				// Wrap in IIFE to avoid global scope pollution and run immediately
                (function() {
                    const uniqueIdBase = '<?php echo esc_js( $unique_id_base ); ?>';
                    const locationFilter = document.getElementById('location-filter-' + uniqueIdBase);
                    const departmentFilter = document.getElementById('department-filter-' + uniqueIdBase);
                    const jobList = document.getElementById('job-list-' + uniqueIdBase);
                    const noJobsMessage = document.getElementById('no-jobs-message-' + uniqueIdBase);
                    const jobCountDisplay = document.getElementById('job-count-' + uniqueIdBase);
                    const clearButton = document.getElementById('clear-filters-' + uniqueIdBase);

                    if (!locationFilter || !departmentFilter || !jobList || !noJobsMessage || !jobCountDisplay || !clearButton) { return; }

                    // Get all RENDERED job items (one per location instance)
                    const jobItems = jobList.querySelectorAll('.job-item');

                    // --- Dynamic population (Simplified and Corrected) ---
                    if (jobItems.length > 0) {
                        // Use Maps to store unique entries: Key=Slug, Value=Display Text
                        const uniqueLocations = new Map();
                        const uniqueDepartments = new Map();

                        jobItems.forEach(item => {
                            // Location: Get data directly from the rendered item
                            const locSlug = item.dataset.location;
                            const locTextEl = item.querySelector('.job-location');
                            const locText = locTextEl ? locTextEl.textContent.trim() : locSlug.replace(/-/g, ' '); // Get individual text

                            if (locSlug && !uniqueLocations.has(locSlug)) { // Check slug for uniqueness
                                uniqueLocations.set(locSlug, locText); // Store Slug -> Text
                            }

                            // Department: Get data directly from the rendered item
                            const depSlug = item.dataset.department;
                            const depTextEl = item.querySelector('.job-department');
                            const depText = depTextEl ? depTextEl.textContent.trim() : depSlug.replace(/-/g, ' ');

                            if (depSlug && !uniqueDepartments.has(depSlug)) { // Check slug for uniqueness
                                uniqueDepartments.set(depSlug, depText); // Store Slug -> Text
                            }
                        });

                        // Sort by Display Text (Value in the Map)
                        const sortedLocations = [...uniqueLocations.entries()].sort((a, b) => a[1].localeCompare(b[1]));
                        const sortedDepartments = [...uniqueDepartments.entries()].sort((a, b) => a[1].localeCompare(b[1]));

                        // Populate dropdowns
                        while (locationFilter.options.length > 1) locationFilter.remove(1);
                        while (departmentFilter.options.length > 1) departmentFilter.remove(1);

                        sortedLocations.forEach(([slug, text]) => {
                            const option = document.createElement('option');
                            option.value = slug; // Use slug for value
                            option.textContent = text; // Use text for display
                            locationFilter.appendChild(option);
                        });

                        sortedDepartments.forEach(([slug, text]) => {
                             const option = document.createElement('option');
                             option.value = slug;
                             option.textContent = text;
                             departmentFilter.appendChild(option);
                        });
                    }
                    // --- End dynamic population ---

                     function updateClearButtonVisibility() {
                        if (locationFilter.value !== "" || departmentFilter.value !== "") {
                            clearButton.style.display = 'inline-flex';
                        } else {
                            clearButton.style.display = 'none';
                        }
                    }

                    // --- Filtering Logic (Checks individual item's location) ---
                    function filterJobs() {
                        const currentJobItems = jobList.querySelectorAll('.job-item');
                        if (currentJobItems.length === 0 && !<?php echo empty($jobs) ? 'true' : 'false'; ?>) {
                             noJobsMessage.style.display = 'block'; jobList.style.display = 'none';
                             jobCountDisplay.textContent = '0 Open Positions'; updateClearButtonVisibility(); return;
                        }

                        const selectedLocation = locationFilter.value; // This is the individual slug
                        const selectedDepartment = departmentFilter.value;
                        let visibleJobsCount = 0;

                        currentJobItems.forEach(item => {
                            const itemLocation = item.dataset.location; // Individual location slug
                            const itemDepartment = item.dataset.department;

                            const locationMatch = selectedLocation === "" || itemLocation === selectedLocation;
                            const departmentMatch = selectedDepartment === "" || itemDepartment === selectedDepartment;

                            if (locationMatch && departmentMatch) { item.classList.remove('hidden'); visibleJobsCount++; } else { item.classList.add('hidden'); }
                        });

                         // Show/hide the 'no jobs found' message
                        if (visibleJobsCount === 0 && jobItems.length > 0) { noJobsMessage.style.display = 'block'; jobList.style.display = 'none'; } else { noJobsMessage.style.display = 'none'; jobList.style.display = 'grid'; }

                        // Update job count display heading
                        let countText = visibleJobsCount === 1 ? '1 Open Position' : `${visibleJobsCount} Open Positions`;
                        jobCountDisplay.textContent = countText;
                        updateClearButtonVisibility();
                    }

                    // Event Listeners (Remain the same)
                    clearButton.addEventListener('click', function(e) { e.preventDefault(); locationFilter.value = ""; departmentFilter.value = ""; filterJobs(); });
                    locationFilter.addEventListener('change', filterJobs);
                    departmentFilter.addEventListener('change', filterJobs);

                    // Initial check for clear button visibility
                    updateClearButtonVisibility();

                 })();
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