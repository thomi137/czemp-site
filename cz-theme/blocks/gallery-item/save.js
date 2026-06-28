import {
    InnerBlocks,
    useBlockProps,
} from '@wordpress/block-editor';

import { hexToRgba } from './utils';

export default function save({ attributes }) {
    const {
        imageUrl,
        imageAlt,
        overlayColor,
        overlayOpacity,
        focalPoint,
        linkUrl,
    } = attributes;

    const blockProps = useBlockProps.save({
        className: 'gallery-item',
    });

    const inner = (
        <div {...blockProps}>
            {imageUrl && (
                <img
                    src={imageUrl}
                    alt={imageAlt || ''}
                    style={{
                        objectFit: 'cover',
                        objectPosition: `${(focalPoint?.x ?? 0.5) * 100}% ${(focalPoint?.y ?? 0.5) * 100}%`,
                    }}
                />
            )}
            <div
                className="overlay"
                style={{
                    backgroundColor: hexToRgba(overlayColor ?? '#000000', overlayOpacity ?? 0.6),
                }}
            >
                <InnerBlocks.Content />
            </div>
        </div>
    );

    if (linkUrl) {
        return <a href={linkUrl}>{inner}</a>;
    }

    return inner;
}
