import { registerBlockType } from '@wordpress/blocks';
import './style.scss'; // Frontend + Editor Styles
import Edit from './edit';
import metadata from '../block.json'; // Import metadata

registerBlockType(metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	// Save function is omitted for dynamic blocks (rendering handled by PHP)
});