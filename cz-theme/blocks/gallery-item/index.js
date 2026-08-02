// Copyright (c) 2026 Thomas Prosser. All rights reserved.

import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';

import Edit from './edit';
import Save from './save';

registerBlockType(metadata.name, {
	edit: Edit,
	save: Save,
});
