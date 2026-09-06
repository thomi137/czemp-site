// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

export default function Edit() {
    const blockProps = useBlockProps();

    return (
        <div {...blockProps}>
            <ServerSideRender block={metadata.name} />
        </div>
    );
}
