// Copyright (c) 2026 Thomas Prosser. All rights reserved.

import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';

import Edit from './edit';

registerBlockType(metadata.name, {
	edit: Edit,
	save: () => null,
});
