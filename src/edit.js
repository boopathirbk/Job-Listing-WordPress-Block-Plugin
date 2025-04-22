import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	Button,
	SelectControl,
	Notice,
    __experimentalHeading as Heading,
    ToggleControl // <-- Added Import
} from '@wordpress/components';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { jobs = [], hiringOrganizationName, hiringOrganizationUrl } = attributes;

	const blockProps = useBlockProps();

    // --- Job Management Functions ---
	const addJob = () => {
		const newJobs = [
			...jobs,
			{
				id: `job-${Date.now()}`,
				title: '',
				department: '',
				locations: [], // <-- Changed: Default locations to empty array
                employmentType: 'Full-time',
                description: '',
				url: '',
                openInNewTab: false, // <-- Added: Default link target
			},
		];
		setAttributes({ jobs: newJobs });
	};

	const updateJob = (index, key, value) => {
		const newJobs = jobs.map((job, i) => {
			if (i === index) {
                // Handle locations array from textarea
                if (key === 'locationsText') {
                    const locationsArray = value
                        .split('\n') // Split by newline
                        .map(loc => loc.trim()) // Trim whitespace
                        .filter(Boolean); // Remove empty lines
                    return { ...job, locations: locationsArray };
                }
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

    // --- Department Options (derived from current jobs) ---
    const getUniqueDepartments = () => {
        const values = new Set(jobs.map(job => job.department?.trim()).filter(Boolean));
        return Array.from(values).sort().map(value => ({ label: value, value: value }));
    }
    const departmentOptions = [{ label: __('Select Department...'), value: '' }, ...getUniqueDepartments()];

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
                        type="url" // Use url type for better validation
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
                            <TextControl
								label={__('Department', 'job-listings-block')}
								value={job.department}
								onChange={(value) => updateJob(index, 'department', value)}
                                required
							/>

                            {/* Feature 3: Textarea for Multiple Locations */}
                            <TextareaControl
                                label={__('Locations (One per line)', 'job-listings-block')}
                                value={(job.locations || []).join('\n')} // Join array for textarea display
                                onChange={(value) => updateJob(index, 'locationsText', value)} // Use temporary key
                                help={__('Enter each location on a new line. E.g., "City, Country".', 'job-listings-block')}
                                required
                                rows={3} // Adjust rows as needed
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
                            {/* Feature 1: Link Target Toggle */}
                            <ToggleControl
                                label={__('Open link in new tab', 'job-listings-block')}
                                checked={!!job.openInNewTab} // Ensure boolean
                                onChange={(value) => updateJob(index, 'openInNewTab', value)}
                            />

							<Button isDestructive isSmall variant="secondary" onClick={() => removeJob(index)} style={{ marginTop: '10px' }}>
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
                )}
			</div>
		</>
	);
}