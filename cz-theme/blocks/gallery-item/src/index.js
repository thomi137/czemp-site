import edit from './edit';
import save from './save';
import { registerBlockType } from '@wordpress/blocks';

registerBlockType('czemp-theme/gallery-item', {
    edit,
    save,
});