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
$jobs = isset( $attributes['jobs'] ) && is_array( $attributes['jobs'] ) ? $attributes['jobs'] : array();
$hiring_org_name = isset($attributes['hiringOrganizationName']) ? sanitize_text_field($attributes['hiringOrganizationName']) : 'Your Company Name';
$hiring_org_url = isset($attributes['hiringOrganizationUrl']) ? esc_url($attributes['hiringOrganizationUrl']) : '';

// Generate a unique ID base *once* for this block instance.
$unique_id_base = uniqid( 'job-block-' );
$total_jobs_count = count( $jobs ); // Initial total count

// Get block wrapper attributes (for alignment, custom classes, etc.).
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'job-listings-block-wrapper' ) );

?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="job-listings-container">

        <?php // === NEW: Job Count as Heading (Moved Above Filters) === ?>
        <h2 id="job-count-<?php echo esc_attr( $unique_id_base ); ?>" class="job-count-heading">
            <?php
                // Output initial count using WordPress translation for pluralization
                printf(
                    esc_html( _n( '%d Open Position', '%d Open Positions', $total_jobs_count, 'job-listings-block' ) ), // Changed text format
                    (int) $total_jobs_count
                );
            ?>
        </h2>

		<!-- Filters Section -->
        <div class="filters-section">
            <?php // --- "Filter by" label restored here --- ?>
            <span class="filter-label"><?php echo esc_html__( 'Filter by', 'job-listings-block' ); ?></span>

            <?php // --- Filter Dropdowns remain the same --- ?>
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
             <?php // Job Count div is now moved above ?>
        </div>


		<?php if ( ! empty( $jobs ) ) : ?>
			<!-- Job List -->
			<div id="job-list-<?php echo esc_attr( $unique_id_base ); // Use Unique ID ?>" class="job-list" role="list">
				<?php foreach ( $jobs as $index => $job ) :
                    // Sanitize data for output
                    $job_id = isset( $job['id'] ) ? sanitize_key( $job['id'] ) : 'job-' . $index;
					$job_title = isset( $job['title'] ) ? esc_html( $job['title'] ) : '';
					$job_department_raw = isset( $job['department'] ) ? trim( $job['department'] ) : '';
                    $job_department_display = esc_html($job_department_raw);
					$job_department_slug = sanitize_title( $job_department_raw ); // For data attribute
					$job_location_raw = isset( $job['location'] ) ? trim( $job['location'] ) : '';
                    $job_location_display = esc_html($job_location_raw);
					$job_location_slug = sanitize_title( $job_location_raw ); // For data attribute
                    $job_employment_type = isset($job['employmentType']) ? esc_html($job['employmentType']) : 'Full-time';
                    $job_description = isset($job['description']) ? wp_kses_post($job['description']) : ''; // Allow basic HTML
					$job_url = isset( $job['url'] ) ? esc_url( $job['url'] ) : '#';

                    // Skip rendering if essential data is missing (though editor should require fields)
                    if (empty($job_title) || empty($job_location_raw) || empty($job_department_raw)) {
                        continue;
                    }
				?>
					<div class="job-item"
						 data-location="<?php echo esc_attr( $job_location_slug ); ?>"
						 data-department="<?php echo esc_attr( $job_department_slug ); ?>"
                         data-employment-type="<?php echo esc_attr( $job_employment_type ); ?>"
                         id="<?php echo esc_attr( $job_id ); ?>"
                         role="listitem">

                        <?php // --- Job Item HTML structure remains the same --- ?>
						<div class="job-details">
                            <div class="job-meta">
                                <span class="job-department"><?php echo $job_department_display; ?></span>
                                <span class="job-employment-type"><?php echo $job_employment_type; ?></span>
                            </div>
							<h3 class="job-title"><?php echo $job_title; ?></h3>
                            <?php if (!empty($job_description)): ?>
                                <?php // This snippet is hidden by default CSS but used for SEO ?>
                                <p class="job-description-snippet"><?php echo $job_description; ?></p>
                            <?php endif; ?>
						</div>
						<div class="job-location-info">
							<span class="job-location-label"><?php esc_html_e( 'Location', 'job-listings-block' ); ?></span>
							<span class="job-location"><?php echo $job_location_display; ?></span>
						</div>
						<div class="job-link-wrapper">
							<a href="<?php echo $job_url; ?>" class="job-link" <?php echo ( $job_url !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ); ?>>
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
                "name" => $hiring_org_name,
                "sameAs" => $hiring_org_url
            );
            $current_date = current_time( 'Y-m-d' );

            foreach ( $jobs as $job ) {
                $title = isset( $job['title'] ) ? sanitize_text_field( $job['title'] ) : null;
                $loc_text = isset( $job['location'] ) ? sanitize_text_field( $job['location'] ) : null;
                // Use description from attribute if present, otherwise generate basic one
                $desc = !empty($job['description']) ? wp_kses_post( $job['description'] ) : '<p>' . sanitize_text_field( $job['title'] ) . ' position available.</p>';
                $url = isset( $job['url'] ) ? esc_url( $job['url'] ) : null;
                $emp_type = isset($job['employmentType']) ? sanitize_text_field($job['employmentType']) : "Full-time"; // Default to Full-time string

                if (!$title || !$url) continue; // Skip if essential SEO data missing

                $schema_item = array(
                    "@context" => "https://schema.org/",
                    "@type" => "JobPosting",
                    "title" => $title,
                    "description" => $desc,
                    "hiringOrganization" => $hiring_org,
                    // Format employmentType for schema.org standards (e.g., FULL_TIME, PART_TIME)
                    "employmentType" => strtoupper( str_replace( '-', '_', $emp_type ) ),
                    "datePosted" => $current_date,
                    "url" => $url,
                    "jobLocation" => array( "@type" => "Place" )
                );

                 if ($loc_text && $loc_text !== 'Multiple Locations') {
                     $parts = explode(',', $loc_text);
                     $schema_item['jobLocation']['address'] = array("@type" => "PostalAddress");
                     if(count($parts) > 0) $schema_item['jobLocation']['address']['addressLocality'] = trim($parts[0]);
                     if(count($parts) > 1) $schema_item['jobLocation']['address']['addressCountry'] = trim($parts[count($parts) - 1]);
                 } else if ($loc_text === 'Multiple Locations') {
                     $schema_item['jobLocation']['name'] = "Multiple Locations"; // Use name for multiple
                 } else {
                     unset($schema_item['jobLocation']); // Remove location if not specified
                 }
                $job_posting_schema[] = $schema_item;
            }

            if ( ! empty( $job_posting_schema ) ) {
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
                    // === Get the count display HEADING element ===
                    const jobCountDisplay = document.getElementById('job-count-' + uniqueIdBase);

                    // Check if all elements were found before proceeding
                    if (!locationFilter || !departmentFilter || !jobList || !noJobsMessage || !jobCountDisplay) { // Added jobCountDisplay check
                        // console.warn('Job filter or dependent elements not found for block instance:', uniqueIdBase);
                        return; // Exit if essential elements are missing
                    }

                    const jobItems = jobList.querySelectorAll('.job-item');

                    // --- Dynamic population ---
                    // Only proceed if there are items to populate from
                    if (jobItems.length > 0) {
                        const locations = new Map();
                        const departments = new Map();

                        jobItems.forEach(item => {
                            const locValue = item.dataset.location;
                            const depValue = item.dataset.department;
                            // Ensure elements exist before accessing textContent
                            const locTextEl = item.querySelector('.job-location');
                            const depTextEl = item.querySelector('.job-department');
                            const locText = locTextEl ? locTextEl.textContent.trim() : null;
                            const depText = depTextEl ? depTextEl.textContent.trim() : null;

                            if (locValue && locText && !locations.has(locValue)) {
                                locations.set(locValue, locText);
                            }
                            if (depValue && depText && !departments.has(depValue)) {
                                departments.set(depValue, depText);
                            }
                        });

                        // Sort options alphabetically by display text
                        const sortedLocations = [...locations.entries()].sort((a, b) => a[1].localeCompare(b[1]));
                        const sortedDepartments = [...departments.entries()].sort((a, b) => a[1].localeCompare(b[1]));

                        // Clear existing options except the first "All" option
                        while (locationFilter.options.length > 1) locationFilter.remove(1);
                        while (departmentFilter.options.length > 1) departmentFilter.remove(1);

                        sortedLocations.forEach(([value, text]) => {
                            const option = document.createElement('option');
                            option.value = value;
                            option.textContent = text;
                            locationFilter.appendChild(option);
                        });

                        sortedDepartments.forEach(([value, text]) => {
                            const option = document.createElement('option');
                            option.value = value;
                            option.textContent = text;
                            departmentFilter.appendChild(option);
                        });
                    } else {
                         // If no jobs initially, the PHP handles the count correctly
                         // No need to update JS count here as PHP did it
                    }
                    // --- End dynamic population ---


                    // --- Filtering Logic ---
                    function filterJobs() {
                        // Ensure jobItems is fresh if content could dynamically change (unlikely here)
                        const currentJobItems = jobList.querySelectorAll('.job-item');
                        if (currentJobItems.length === 0 && !<?php echo empty($jobs) ? 'true' : 'false'; ?>) {
                             // If the list became empty AFTER initial load
                             noJobsMessage.style.display = 'block';
                             jobList.style.display = 'none';
                             // Update count to 0
                             jobCountDisplay.textContent = '0 Open Positions'; // Title Case
                             return;
                        }

                        const selectedLocation = locationFilter.value;
                        const selectedDepartment = departmentFilter.value;
                        let visibleJobsCount = 0; // Counter

                        currentJobItems.forEach(item => {
                            const itemLocation = item.dataset.location;
                            const itemDepartment = item.dataset.department;
                            const locationMatch = selectedLocation === "" || itemLocation === selectedLocation;
                            const departmentMatch = selectedDepartment === "" || itemDepartment === selectedDepartment;

                            // Apply/remove 'hidden' class based on match
                            if (locationMatch && departmentMatch) {
                                item.classList.remove('hidden');
                                visibleJobsCount++; // Increment if visible
                            } else {
                                item.classList.add('hidden');
                            }
                        });

                         // Show/hide the 'no jobs found' message
                        if (visibleJobsCount === 0 && jobItems.length > 0) { // Only show if jobs existed but none match
                            noJobsMessage.style.display = 'block'; // Show message
                            jobList.style.display = 'none'; // Hide the job list container itself
                        } else {
                            noJobsMessage.style.display = 'none'; // Hide message
                            jobList.style.display = 'grid'; // Ensure job list is visible (reset display)
                        }

                        // === Update job count display HEADING ===
                        let countText = '';
                        if (visibleJobsCount === 1) {
                            countText = '1 Open Position'; // Singular, Title Case
                        } else {
                            countText = `${visibleJobsCount} Open Positions`; // Plural, Title Case
                        }
                        jobCountDisplay.textContent = countText;

                    } // <-- End of filterJobs function

                    // Add event listeners if filters were found
                    locationFilter.addEventListener('change', filterJobs);
                    departmentFilter.addEventListener('change', filterJobs);

                 })(); // Immediately invoke the function
			</script>


		<?php else : ?>
             <?php // This shows if block is added but NO jobs were ever entered ?>
			<p><?php esc_html_e( 'No job listings have been added yet.', 'job-listings-block' ); ?></p>
             <?php // Hide the job count heading if there are no jobs at all initially ?>
             <style> #job-count-<?php echo esc_attr( $unique_id_base ); ?> { display: none; } </style>
		<?php endif; ?>

	</div>
</div>
<?php
// End output buffering.
?>