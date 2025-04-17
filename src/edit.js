import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText, // If you want rich text descriptions
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl, // Use if you want simple text area for description
	Button,
	SelectControl,
	Notice,
    __experimentalHeading as Heading, // Use Heading component for better structure
    Flex, // For layouting buttons etc.
    FlexItem
} from '@wordpress/components';
import './editor.scss'; // Editor-specific styles

export default function Edit({ attributes, setAttributes }) {
	const { jobs = [], hiringOrganizationName, hiringOrganizationUrl } = attributes;

	const blockProps = useBlockProps(); // Get block props for wrapper

    // --- Job Management Functions ---
	const addJob = () => {
		const newJobs = [
			...jobs,
			{
				id: `job-${Date.now()}`, // Simple unique ID
				title: '',
				department: '',
				location: '',
                employmentType: 'Full-time', // Default type
                description: '',
				url: '',
			},
		];
		setAttributes({ jobs: newJobs });
	};

	const updateJob = (index, key, value) => {
		const newJobs = jobs.map((job, i) => {
			if (i === index) {
				return { ...job, [key]: value };
			}
			return job;
		});
		setAttributes({ jobs: newJobs });
	};

	const removeJob = (index) => {
		const newJobs = jobs.filter((_, i) => i !== index);
		setAttributes({ jobs: newJobs });
	};

    // --- Department & Location Options (derived from current jobs for SelectControls) ---
    const getUniqueValues = (key) => {
        const values = new Set(jobs.map(job => job[key]?.trim()).filter(Boolean));
        return Array.from(values).map(value => ({ label: value, value: value }));
    }
    const departmentOptions = [{ label: __('Select Department...'), value: '' }, ...getUniqueValues('department')];
    const locationOptions = [{ label: __('Select Location...'), value: '' }, ...getUniqueValues('location')];


	return (
		<>
			{/* Inspector Controls (Sidebar) */}
			<InspectorControls>
                <PanelBody title={__('Hiring Organization', 'job-listings-block')} initialOpen={true}>
                    <TextControl
						label={__('Organization Name', 'job-listings-block')}
						value={hiringOrganizationName}
						onChange={(value) => setAttributes({ hiringOrganizationName: value })}
                        help={__('Used for SEO Schema.', 'job-listings-block')}
					/>
                    <TextControl
						label={__('Organization Website URL', 'job-listings-block')}
						value={hiringOrganizationUrl}
						onChange={(value) => setAttributes({ hiringOrganizationUrl: value })}
                        help={__('Used for SEO Schema.', 'job-listings-block')}
					/>
                </PanelBody>

				<PanelBody title={__('Manage Job Listings', 'job-listings-block')} initialOpen={true}>
					{jobs.length === 0 && (
						<Notice status="info" isDismissible={false}>
							{__('No jobs added yet. Click "Add Job" to start.', 'job-listings-block')}
						</Notice>
					)}

					{jobs.map((job, index) => (
						<PanelBody key={job.id || index} title={`${__('Job', 'job-listings-block')} ${index + 1}: ${job.title || __('(New Job)')}`} initialOpen={false} className="job-listings-block-job-panel">
							<TextControl
								label={__('Job Title', 'job-listings-block')}
								value={job.title}
								onChange={(value) => updateJob(index, 'title', value)}
								required
							/>
                            {/* Example using SelectControl for Departments - or use TextControl if preferred */}
                            <SelectControl
                                label={__('Department', 'job-listings-block')}
                                value={job.department}
                                options={departmentOptions}
                                onChange={(value) => updateJob(index, 'department', value)}
                                help={__('Select or type a new department.', 'job-listings-block')}
                                // __nextHasNoMarginBottom // Optional: if using newer WP versions
                            />
                             {/* Allow typing new department if SelectControl doesn't cover it */}
                            <TextControl
								label={__('Department (if not listed)', 'job-listings-block')}
								value={job.department}
								onChange={(value) => updateJob(index, 'department', value)}
							/>

							<TextControl
								label={__('Location', 'job-listings-block')}
								value={job.location}
								onChange={(value) => updateJob(index, 'location', value)}
                                help={__('E.g., "City, Country" or "Multiple Locations"', 'job-listings-block')}
								required
							/>
                            <SelectControl
                                label={__('Employment Type', 'job-listings-block')}
                                value={job.employmentType}
                                options={[
                                    { label: 'Full-time', value: 'Full-time' },
                                    { label: 'Part-time', value: 'Part-time' },
                                    { label: 'Contract', value: 'Contract' },
                                    { label: 'Temporary', value: 'Temporary' },
                                    { label: 'Internship', value: 'Internship' },
                                ]}
                                onChange={(value) => updateJob(index, 'employmentType', value)}
                            />
                             <TextareaControl
                                label={__('Short Description (for SEO)', 'job-listings-block')}
                                value={job.description}
								onChange={(value) => updateJob(index, 'description', value)}
                                help={__('A brief summary used in structured data.', 'job-listings-block')}
                                rows={3}
                             />
							<TextControl
								label={__('Learn More URL', 'job-listings-block')}
								value={job.url}
								onChange={(value) => updateJob(index, 'url', value)}
								type="url"
								required
							/>
							<Button
                                isDestructive
                                isSmall // Use isSmall for less visual weight
                                variant="secondary" // Use secondary for less emphasis than primary action
								onClick={() => removeJob(index)}
                                style={{ marginTop: '10px' }} // Add some space
							>
								{__('Remove Job', 'job-listings-block')} {index + 1}
							</Button>
						</PanelBody>
					))}

					<Button variant="primary" onClick={addJob}>
						{__('Add Job', 'job-listings-block')}
					</Button>
				</PanelBody>


			</InspectorControls>

			{/* Block Editor Preview Area */}
			<div {...blockProps}>
                <Heading level={4}>{__('Job Listings Preview', 'job-listings-block')}</Heading>
				{jobs.length === 0 ? (
                    <p>{__('Add jobs using the sidebar controls.', 'job-listings-block')}</p>
                ) : (
                    <p>{`${jobs.length} ${jobs.length === 1 ? __('job') : __('jobs')} configured. See frontend for full preview with filters.`}</p>
                    // Optional: Render a simplified list here for visual feedback
                    /*<ul>
                        {jobs.map((job, index) => (
                            <li key={job.id || index}><strong>{job.title || '(Untitled)'}</strong> - {job.location || '(No location)'}</li>
                        ))}
                    </ul>*/
                )}
			</div>
		</>
	);
}