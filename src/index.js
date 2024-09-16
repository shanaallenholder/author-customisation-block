import { registerBlockType } from '@wordpress/blocks';
import { SelectControl, ServerSideRender } from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import { InspectorControls } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import Edit from './edit';
import save from './save';

// Register the block
registerBlockType('custom/author-block', {
    title: 'Author Profile',
    icon: 'admin-users',
    category: 'widgets',
    attributes: {
        selectedAuthor: {
            type: 'string',
            default: '',
        },
        biography: {
            type: 'string',
            default: ''
        }
    },
    /**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
});



























