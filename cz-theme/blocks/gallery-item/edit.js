import {
    InspectorControls,
    MediaUpload,
    MediaUploadCheck,
    InnerBlocks,
    useBlockProps,
} from '@wordpress/block-editor';

import {
    PanelBody,
    Button,
    RangeControl,
    ColorPalette,
    FocalPointPicker,
    SelectControl,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';

import '@wordpress/media-utils';

import { hexToRgba } from './utils';

export default function Edit({ attributes, setAttributes }) {
    const {
        imageUrl,
        overlayColor,
        overlayOpacity,
        focalPoint,
        linkUrl,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'gallery-item',
    });

    const safeFocalPoint = focalPoint ?? { x: 0.5, y: 0.5 };
    const safeOverlayColor = overlayColor ?? '#000000';
    const safeOverlayOpacity = overlayOpacity ?? 0.6;

    const collections = useSelect((select) => {
        return select('core').getEntityRecords('taxonomy', 'collection', { per_page: -1 });
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title="Image">
                    <MediaUploadCheck>
                        <MediaUpload
                            allowedTypes={['image']}
                            onSelect={(media) => setAttributes({
                                imageUrl: media.url,
                                imageAlt: media.alt || '',
                            })}
                            render={({ open }) => (
                                <>
                                    <Button
                                        variant="secondary"
                                        onClick={open}
                                    >
                                        {imageUrl ? 'Replace Image' : 'Select Image'}
                                    </Button>
                                    {imageUrl && (
                                        <FocalPointPicker
                                            url={imageUrl}
                                            value={safeFocalPoint}
                                            onChange={(value) => setAttributes({ focalPoint: value })}
                                        />
                                    )}
                                </>
                            )}
                        />
                    </MediaUploadCheck>
                </PanelBody>

                <PanelBody title="Overlay">
                    <ColorPalette
                        value={safeOverlayColor}
                        onChange={(color) => setAttributes({ overlayColor: color ?? '#000000' })}
                    />
                    <RangeControl
                        __next40pxDefaultSize
                        label="Overlay Opacity"
                        value={safeOverlayOpacity}
                        onChange={(value) => setAttributes({ overlayOpacity: value })}
                        min={0}
                        max={1}
                        step={0.05}
                    />
                </PanelBody>

                <PanelBody title="Link">
                    <SelectControl
                        label="Kollektion"
                        value={linkUrl ?? ''}
                        options={[
                            { label: '— keine —', value: '' },
                            ...(collections ?? []).map((c) => ({
                                label: c.name,
                                value: c.link,
                            })),
                        ]}
                        onChange={(val) => setAttributes({ linkUrl: val })}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                {imageUrl && (
                    <img
                        src={imageUrl}
                        alt=""
                        style={{
                            objectFit: 'cover',
                            objectPosition: `${safeFocalPoint.x * 100}% ${safeFocalPoint.y * 100}%`,
                        }}
                    />
                )}
                <div
                    className="overlay"
                    style={{
                        backgroundColor: hexToRgba(safeOverlayColor, safeOverlayOpacity),
                    }}
                >
                    <InnerBlocks
                        allowedBlocks={[
                            'core/heading',
                            'core/paragraph',
                            'core/buttons',
                            'core/button',
                            'core/list',
                            'core/image',
                        ]}
                        template={[
                            ['core/heading', { placeholder: 'Title' }],
                            ['core/paragraph', { placeholder: 'Description...' }],
                        ]}
                    />
                </div>
            </div>
        </>
    );
}
