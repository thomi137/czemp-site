// Copyright (c) 2026 Thomas Prosser. Licensed under GPL-2.0-or-later.

import {
    InnerBlocks,
    useBlockProps,
} from '@wordpress/block-editor';

import { hexToRgba, buildResponsiveImageProps } from './utils';

export default function save({ attributes }) {
    const {
        imageUrl,
        imageAlt,
        imageId,
        imageSizes,
        imageWidth,
        imageHeight,
        overlayColor,
        overlayOpacity,
        focalPoint,
        linkUrl,
        alwaysShowOverlayOnMobile,
    } = attributes;

    // Only ever ADD a class, and only when the toggle is explicitly off.
    // Old, already-saved content has no alwaysShowOverlayOnMobile key at
    // all — it parses to the attribute's default (true) — so this must
    // produce the exact same output as before for every existing instance,
    // or the Site Editor's byte-for-byte validator flags them all as
    // "invalid content" the moment this ships.
    const blockProps = useBlockProps.save({
        className: alwaysShowOverlayOnMobile === false
            ? 'gallery-item gallery-item--mobile-overlay-off'
            : 'gallery-item',
    });

    // {} for every already-published post (no imageId — see block.json's
    // default) or any instance with nothing usable to build a srcset
    // from — same guarantee as the alwaysShowOverlayOnMobile comment
    // above, see buildResponsiveImageProps in utils.js for why no
    // separate deprecated save() is needed for this.
    const responsiveImageProps = buildResponsiveImageProps({
        imageId,
        imageUrl,
        imageSizes,
        imageWidth,
        imageHeight,
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
                    {...responsiveImageProps}
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
