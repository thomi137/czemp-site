import { 
    InspectorControls, 
    PanelColorSettings, 
    MediaUpload, 
    MediaUploadCheck, 
    FocalPointPicker,
    InnerBlocks,
    TextControl,
    ToggleControl,
} from '@wordpress/block-editor';
import { 
    PanelBody, 
    SelectControl, 
    Button, 
    RangeControl 
} from '@wordpress/components';
import { hexToRgba } from "./utils";

export default function Edit({ attributes, setAttributes }) {
    const {
        imageUrl,
        overlayColor,
        overlayOpacity,
        textAlign,
        verticalAlign,
        fontSize,
        height,
        focalPoint,
        linkUrl,
        openInNewTab
    } = attributes;


    return (
        <>
            <InspectorControls>
                <PanelBody title="Background Image">
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={ media => setAttributes({ imageUrl: media.url }) }
                            allowedTypes={['image']}
                            render={({ open }) => (
                                <>
                                    <Button onClick={open}>
                                        {imageUrl ? 'Change Image' : 'Select Image'}
                                    </Button>
                                    { imageUrl && (
                                        <FocalPointPicker
                                            url={imageUrl}
                                            value={focalPoint}
                                            onChange={(value) =>
                                                setAttributes({ focalPoint: value })
                                            }
                                        />
                                    )}
                                </>

                            )}
                        />
                    </MediaUploadCheck>
                </PanelBody>
                <PanelBody  title="Overlay">
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes.overlayColor,
                                onChange: (color) => {
                                    setAttributes({ overlayColor: color });
                                },
                                label: 'Overlay Color',
                            },
                        ]}
                    />

                    <RangeControl
                        label="Overlay Opacity"
                        value={overlayOpacity}
                        onChange={(value) =>
                            setAttributes({ overlayOpacity: value })
                        }
                        min={0}
                        max={1}
                        step={0.05}
                    />

                </PanelBody>

                <PanelBody title="Text Settings">

                    <SelectControl
                        label="Horizontal Alignment"
                        value={textAlign}
                        options={[
                            { label: 'Left', value: 'left' },
                            { label: 'Center', value: 'center' },
                            { label: 'Right', value: 'right' }
                        ]}
                        onChange={(value) => setAttributes({ textAlign: value })}
                    />

                    <RangeControl
                        label="Block Height"
                        value={height}
                        onChange={(value) =>
                            setAttributes({ height: value })
                        }
                        min={200}
                        max={1000}
                        step={10}
                    />

                    <SelectControl
                        label="Vertical Alignment"
                        value={verticalAlign}
                        options={[
                            { label: 'Top', value: 'flex-left' },
                            { label: 'Middle', value: 'center' },
                            { label: 'Bottom', value: 'flex-end' }
                        ]}
                        onChange={(value) => setAttributes({ verticalAlign: value })}
                    />

                    <SelectControl
                        label="Font Size"
                        value={fontSize}
                        options={[
                            { label: 'Small', value: '14px' },
                            { label: 'Normal', value: '16px' },
                            { label: 'Large', value: '20px' },
                            { label: 'Extra Large', value: '24px' },
                        ]}
                        onChange={(value) => setAttributes({ fontSize: value })}
                    />
                </PanelBody>

                <PanelBody title="Link Settings">

                    <TextControl
                        label="Link URL"
                        value={linkUrl}
                        onChange={(value) =>
                            setAttributes({ linkUrl: value })
                        }
                    />

                    <ToggleControl
                        label="Open in new tab"
                        checked={openInNewTab}
                        onChange={(value) =>
                            setAttributes({ openInNewTab: value })
                        }
                    />

                </PanelBody>

            </InspectorControls>

            {/* Start of HTML */}

            <div className="gallery-item"
                 style={{
                        height: `${height}px`,
                        objectPosition: `${
                            (focalPoint?.x ?? 0.5) * 100
                        }% ${
                            (focalPoint?.y ?? 0.5) * 100
                        }%`
                    }}
            >
                {imageUrl && <img
                    src={imageUrl}
                    alt={title}
                />
                }
                <div className="overlay"
                     style={{
                        backgroundColor: hexToRgba(overlayColor, overlayOpacity),
                        justifyContent: verticalAlign,
                        textAlign,
                        fontSize,
                        pointerEvents: 'auto',   // allow clicks
                     }}
                    >
                     <InnerBlocks    
                        allowedBlocks={[
                            'core/heading',
                            'core/paragraph',
                            'core/buttons',
                            'core/button',
                            'core/list'
                        ]}
                        template={[
                            ['core/heading', { level: 3, placeholder: 'Title' }],
                            ['core/paragraph', { placeholder: 'Description...' }]
                        ]}
                     />
    
                </div>
            </div>
        </>
    );
}
