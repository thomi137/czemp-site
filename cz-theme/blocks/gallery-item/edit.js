import {
    InspectorControls,
    MediaUpload,
    MediaUploadCheck,
    FocalPointPicker,
    InnerBlocks,
    PanelColorSettings,
    useBlockProps,
} from '@wordpress/block-editor';

import {
    PanelBody,
    Button,
    RangeControl,
} from '@wordpress/components';

import { hexToRgba } from './utils';

export default function Edit({ attributes, setAttributes }) {
    const {
        imageUrl,
        overlayColor,
        overlayOpacity,
        focalPoint,
    } = attributes;

    const blockProps = useBlockProps({
        className: 'gallery-item',
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title="Image">
                    <MediaUploadCheck>
                        <MediaUpload
                            allowedTypes={['image']}
                            onSelect={(media) =>
                                setAttributes({
                                    imageUrl: media.url,
                                })
                            }
                            render={({ open }) => (
                                <>
                                    <Button
                                        variant="secondary"
                                        onClick={open}
                                    >
                                        {imageUrl
                                            ? 'Replace Image'
                                            : 'Select Image'}
                                    </Button>

                                    {imageUrl && (
                                        <FocalPointPicker
                                            url={imageUrl}
                                            value={focalPoint}
                                            onChange={(value) =>
                                                setAttributes({
                                                    focalPoint: value,
                                                })
                                            }
                                        />
                                    )}
                                </>
                            )}
                        />
                    </MediaUploadCheck>
                </PanelBody>

                <PanelBody title="Overlay">
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: overlayColor,
                                onChange: (color) =>
                                    setAttributes({
                                        overlayColor: color,
                                    }),
                                label: 'Overlay Color',
                            },
                        ]}
                    />

                    <RangeControl
                        __next40pxDefaultSize
                        label="Overlay Opacity"
                        value={overlayOpacity}
                        onChange={(value) =>
                            setAttributes({
                                overlayOpacity: value,
                            })
                        }
                        min={0}
                        max={1}
                        step={0.05}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                {imageUrl && (
                    <img
                        src={imageUrl}
                        alt=""
                        style={{
                            objectPosition: `${
                                (focalPoint?.x ?? 0.5) * 100
                            }% ${
                                (focalPoint?.y ?? 0.5) * 100
                            }%`,
                        }}
                    />
                )}

                <div
                    className="overlay"
                    style={{
                        backgroundColor: hexToRgba(
                            overlayColor,
                            overlayOpacity
                        ),
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
                            [
                                'core/heading',
                                {
                                    placeholder: 'Title',
                                },
                            ],
                            [
                                'core/paragraph',
                                {
                                    placeholder:
                                        'Description...',
                                },
                            ],
                        ]}
                    />
                </div>
            </div>
        </>
    );
}
